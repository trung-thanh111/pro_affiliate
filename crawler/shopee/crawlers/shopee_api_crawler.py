"""
=====================================================
  crawlers/shopee_api_crawler.py — Crawler chính (Playwright Async Version)
  
  Nguyên tắc hoạt động:
  1. Sử dụng Playwright (Stealth) để bypass Cloudflare/Bot detection.
  2. Tự động xử lý Modal ngôn ngữ và Overlay.
  3. Chiết xuất đa tầng: API Interception -> JS State -> DOM.
  4. Trả về ShopeeProduct dataclass.
=====================================================
"""

import os
import asyncio
import random
import re
import json
from datetime import datetime
from typing import Optional, List, Dict, Any
from playwright.async_api import async_playwright

from config import (
    SHOPEE_BASE_URL,
    SHOPEE_PRICE_UNIT,
    DEFAULT_HEADERS,
    DELAY_MIN,
    DELAY_MAX,
    MAX_RETRIES,
    REQUEST_TIMEOUT,
    OUTPUT_DIR
)
from models.product import ShopeeProduct
from utils.logger import get_logger

logger = get_logger("shopee_crawler")


class ShopeeApiCrawler:
    """
    Crawler sử dụng Playwright để vượt qua các cơ chế chặn của Shopee.
    """

    def __init__(
        self,
        delay_min: float = DELAY_MIN,
        delay_max: float = DELAY_MAX,
        max_retries: int = MAX_RETRIES,
    ):
        self.delay_min = delay_min
        self.delay_max = delay_max
        self.max_retries = max_retries
        self.user_data_dir = os.path.join(OUTPUT_DIR, "shopee_profile")
        
        # Thống kê
        self._total_requests = 0
        self._successful_requests = 0

        # Persistent context variables
        self.playwright = None
        self.browser_context = None
        self.page = None
        self.browser = None

        if not os.path.exists(self.user_data_dir):
            os.makedirs(self.user_data_dir, exist_ok=True)

    async def start(self, headless: bool = True, use_remote: bool = False):
        """Khởi động trình duyệt hoặc kết nối vào trình duyệt có sẵn."""
        if self.page: return
        
        self.playwright = await async_playwright().start()
        
        if use_remote:
            # Chế độ kết nối vào Chrome đang mở sẵn (Cổng 9222)
            try:
                self.browser = await self.playwright.chromium.connect_over_cdp("http://localhost:9222")
                if not self.browser.contexts:
                    self.browser_context = await self.browser.new_context()
                else:
                    self.browser_context = self.browser.contexts[0]
                
                self.page = self.browser_context.pages[0] if self.browser_context.pages else await self.browser_context.new_page()
                logger.info("✅ Đã kết nối thành công!")
            except Exception as e:
                logger.error(f"❌ Không thể kết nối vào Chrome (9222). Lỗi: {e}")
                if self.playwright:
                    await self.playwright.stop()
                self.playwright = None
                raise e
        else:
            # Các flag sạch, ẩn danh tốt nhất cho Chrome thực
            browser_args = [
                '--disable-infobars',
                '--no-first-run',
                '--password-store=basic',
                '--use-mock-keychain',
                '--disable-blink-features=AutomationControlled',
                '--test-type',
            ]
                
            self.browser_context = await self.playwright.chromium.launch_persistent_context(
                self.user_data_dir,
                headless=headless,
                channel="chrome",
                viewport={'width': 1366, 'height': 768},
                locale="vi-VN",
                timezone_id="Asia/Ho_Chi_Minh",
                user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
                ignore_default_args=["--enable-automation", "--no-sandbox"], 
                args=browser_args
            )
            self.page = self.browser_context.pages[0] if self.browser_context.pages else await self.browser_context.new_page()
        
        # 1. Stealth Injection
        await self.page.add_init_script("""
            Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
            window.chrome = {
                app: { isInstalled: false, InstallState: { DISABLED: 'disabled', INSTALLED: 'installed', NOT_INSTALLED: 'not_installed' } },
                runtime: { PlatformOs: 'win', PlatformArch: 'x86-64', OnInstalledReason: { INSTALL: 'install' } }
            };
            Object.defineProperty(navigator, 'hardwareConcurrency', { get: () => 8 });
            Object.defineProperty(navigator, 'deviceMemory', { get: () => 8 });
        """)
        
        # 2. API Interceptor
        self.last_api_data = None
        def on_response(response):
            if "shopee.vn/api/v4/" in response.url:
                if "item/get" in response.url or "pdp/get" in response.url:
                    asyncio.create_task(self._handle_api_response(response))
        
        self.browser_context.on("response", on_response)
        
        # Warm-up
        try:
            await self.page.goto(SHOPEE_BASE_URL, wait_until="domcontentloaded", timeout=30000)
            await asyncio.sleep(random.uniform(1, 2))
        except: pass

    async def _handle_api_response(self, response):
        try:
            if response.status == 200:
                json_res = await response.json()
                data = json_res.get("data")
                if data and (data.get("name") or data.get("itemid")):
                    self.last_api_data = data
        except: pass

    async def stop(self):
        """Đóng trình duyệt và dọn dẹp tài nguyên."""
        try:
            if self.page: await self.page.close()
            if self.browser_context: await self.browser_context.close()
            if self.browser: await self.browser.close()
            if self.playwright: await self.playwright.stop()
        except: pass
        finally:
            self.page = None
            self.browser_context = None
            self.playwright = None
            self.browser = None

    def _slugify(self, text: str) -> str:
        """Chuyển đổi văn bản thành slug URL."""
        if not text: return ""
        text = text.lower()
        # Thay thế các ký tự tiếng Việt có dấu
        vietnamese_map = {
            'a': 'àáạảãâầấậẩẫăằắặẳẵ',
            'e': 'èéẹẻẽêềếệểễ',
            'i': 'ìíịỉĩ',
            'o': 'òóọỏõôồốộổỗơờớợởỡ',
            'u': 'ùúụủũưừứựửữ',
            'y': 'ỳýỵỷỹ',
            'd': 'đ'
        }
        for char, chars in vietnamese_map.items():
            for c in chars:
                text = text.replace(c, char)
        
        text = re.sub(r'[^a-z0-9\s-]', '', text)
        text = re.sub(r'\s+', '-', text).strip('-')
        return text

    def _extract_product_data(self, item: dict) -> ShopeeProduct:
        p_min = item.get("price_min", item.get("price", 0))
        p_max = item.get("price_max", p_min)
        if p_min > 1000000:
            p_min /= SHOPEE_PRICE_UNIT
            p_max /= SHOPEE_PRICE_UNIT

        raw_images = item.get("images", [])
        processed_images = [f"https://down-vn.img.susercontent.com/file/{img}" if not img.startswith("http") else img for img in raw_images]

        categories = item.get("categories", [])
        cat_name = ""
        if categories:
            cat_name = categories[-1].get("display_name", "")

        return ShopeeProduct(
            name=item.get("name", ""),
            price=float(p_min),
            price_discount=float(p_max),
            album=processed_images,
            # catalogue={
            #     "name": cat_name,
            #     "canonical": self._slugify(cat_name),
            # },
        )

    async def get_product(self, shop_id: str, item_id: str, url: Optional[str] = None) -> Optional[ShopeeProduct]:
        # print(f"DEBUG: Calling get_product with shop_id={shop_id}, item_id={item_id}")
        self._total_requests += 1
        self.last_api_data = None 
        
        try:
            if not self.page or self.page.is_closed():
                await self.start()
        except:
            await self.start()
        
        if not url:
            url = f"{SHOPEE_BASE_URL}/product/{shop_id}/{item_id}?lang=vi"
        
        for attempt in range(2):
            try:
                if attempt > 0:
                    logger.info(f"[*] Thử lại lần {attempt}...")
                    await self.page.reload(wait_until="domcontentloaded")
                else:
                    await self.page.goto(url, wait_until="domcontentloaded", timeout=30000)

                # Polling API
                for _ in range(15):
                    if self.last_api_data:
                        product = self._extract_product_data(self.last_api_data)
                        self._successful_requests += 1
                        return product
                    
                    if _ % 3 == 0:
                        try:
                            api_url = f"https://shopee.vn/api/v4/item/get?itemid={item_id}&shopid={shop_id}"
                            active_data = await self.page.evaluate(f"async () => {{ const r = await fetch('{api_url}'); return await r.json(); }}")
                            if active_data.get("data") and active_data["data"].get("name"):
                                self.last_api_data = active_data["data"]
                                product = self._extract_product_data(self.last_api_data)
                                self._successful_requests += 1
                                return product
                        except: pass

                    if "shopee.vn/verify" in self.page.url: break
                    await asyncio.sleep(1)

                # Captcha handling
                if "shopee.vn/verify" in self.page.url:
                    logger.warning("⚠️ Shopee Captcha! Hãy giải trên trình duyệt...")
                    for _ in range(120):
                        if "shopee.vn/verify" not in self.page.url:
                            await asyncio.sleep(2)
                            break
                        await asyncio.sleep(1)
                    continue

                # Fallback DOM
                logger.warning("⚠️ Thử cào DOM...")
                try:
                    await self.page.wait_for_selector("h1", timeout=5000)
                    full_title = await self.page.title()
                    title = full_title.split("|")[0].strip()
                    heuristics = await self.page.evaluate("""() => {
                        const p = document.querySelector('.G27LRz, .pq6_tw');
                        const b = document.querySelectorAll('.shopee-category-view__breadcrumb__item, .v79pYn');
                        let cat = "";
                        if (b && b.length > 0) {
                            cat = b[b.length - 1].innerText;
                        }
                        return { 
                            price_min: p ? parseInt(p.innerText.replace(/[^0-9]/g, '')) : 0,
                            category_name: cat
                        };
                    }""")
                    if title:
                        self._successful_requests += 1
                        cat_name = heuristics.get('category_name', '')
                        return ShopeeProduct(
                            name=title,
                            price=float(heuristics['price_min']),
                            price_discount=float(heuristics['price_min']),
                            album=[],
                            catalogue={
                                "name": cat_name,
                                "canonical": self._slugify(cat_name),
                            }
                        )
                except: pass

            except Exception as e:
                logger.error(f"❌ Lỗi: {e}")
        
        return None

    def get_stats(self) -> dict:
        rate = (self._successful_requests / self._total_requests * 100) if self._total_requests > 0 else 0
        return {"total": self._total_requests, "success": self._successful_requests, "rate": f"{rate:.1f}%"}
