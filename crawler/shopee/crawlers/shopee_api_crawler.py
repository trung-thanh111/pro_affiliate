"""
=====================================================
  crawlers/shopee_api_crawler.py — Crawler chính (Playwright Version)
  
  Nguyên tắc hoạt động:
  1. Sử dụng Playwright (Stealth) để bypass Cloudflare/Bot detection.
  2. Tự động xử lý Modal ngôn ngữ và Overlay.
  3. Chiết xuất đa tầng: API Interception -> JS State -> DOM.
  4. Trả về ShopeeProduct dataclass.
=====================================================
"""

import os
import time
import random
import re
import json
from datetime import datetime
from typing import Optional, List, Dict, Any
from playwright.sync_api import sync_playwright

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

        if not os.path.exists(self.user_data_dir):
            os.makedirs(self.user_data_dir, exist_ok=True)

    def start(self, headless: bool = True, use_remote: bool = False):
        """Khởi động trình duyệt hoặc kết nối vào trình duyệt có sẵn."""
        if self.page: return
        
        self.playwright = sync_playwright().start()
        
        if use_remote:
            # Chế độ kết nối vào Chrome đang mở sẵn (Cổng 9222)
            try:
                self.browser = self.playwright.chromium.connect_over_cdp("http://localhost:9222")
                if not self.browser.contexts:
                    self.browser_context = self.browser.new_context()
                else:
                    self.browser_context = self.browser.contexts[0]
                
                self.page = self.browser_context.pages[0] if self.browser_context.pages else self.browser_context.new_page()
                logger.info("✅ Đã kết nối thành công!")
            except Exception as e:
                logger.error(f"❌ Không thể kết nối vào Chrome (9222). Hãy đảm bảo bạn đã mở Chrome bằng lệnh đặc biệt. Lỗi: {e}")
                if self.playwright:
                    self.playwright.stop()
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
                '--test-type', # Quan trọng: Khóa mọi cảnh báo vàng của Chrome
            ]
                
            self.browser_context = self.playwright.chromium.launch_persistent_context(
                self.user_data_dir,
                headless=headless,
                channel="chrome", # Dùng Chrome thật
                viewport={'width': 1366, 'height': 768},
                locale="vi-VN",
                timezone_id="Asia/Ho_Chi_Minh",
                user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36",
                ignore_default_args=["--enable-automation", "--no-sandbox"], 
                args=browser_args
            )
            self.page = self.browser_context.pages[0] if self.browser_context.pages else self.browser_context.new_page()
        
        # ─── CÀI ĐẶT CHUNG CHO CẢ 2 CHẾ ĐỘ ───────────────────────────
        
        # 1. Custom Stealth Injection (Ultimate Edition)
        self.page.add_init_script("""
            // 1. Xóa Webdriver tuyệt đối
            Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
            if (Object.getPrototypeOf(navigator).hasOwnProperty('webdriver')) {
                delete Object.getPrototypeOf(navigator).webdriver;
            }

            // 2. Fake Chrome Object chuyên sâu
            window.chrome = {
                app: { isInstalled: false, InstallState: { DISABLED: 'disabled', INSTALLED: 'installed', NOT_INSTALLED: 'not_installed' } },
                runtime: { PlatformOs: 'win', PlatformArch: 'x86-64', OnInstalledReason: { INSTALL: 'install' } }
            };

            // 3. Fake Hardware Fingerprint (Rất quan trọng cho Akamai)
            Object.defineProperty(navigator, 'hardwareConcurrency', { get: () => 8 });
            Object.defineProperty(navigator, 'deviceMemory', { get: () => 8 });
            Object.defineProperty(navigator, 'plugins', { get: () => [1, 2, 3, 4, 5] });
            Object.defineProperty(navigator, 'languages', { get: () => ['vi-VN', 'vi', 'en-US', 'en'] });

            // 4. Fake WebGL Vendor & Renderer (Bypass hardware check)
            const getParameter = WebGLRenderingContext.prototype.getParameter;
            WebGLRenderingContext.prototype.getParameter = function(parameter) {
                if (parameter === 37445) return 'Google Inc. (NVIDIA)'; // UNMASKED_VENDOR_WEBGL
                if (parameter === 37446) return 'ANGLE (NVIDIA, NVIDIA GeForce RTX 3060 Direct3D11 vs_5_0 ps_5_0, D3D11)'; // UNMASKED_RENDERER_WEBGL
                return getParameter.apply(this, arguments);
            };

            // 5. Bypass Permissions
            const originalQuery = window.navigator.permissions.query;
            window.navigator.permissions.query = parameters => (
                parameters.name === 'notifications' ?
                    Promise.resolve({ state: Notification.permission }) :
                    originalQuery(parameters)
            );
        """)
        
        # ─── Global API Interceptor (Context Level - Catch All) ───────
        self.last_api_data = None
        def on_response(response):
            url = response.url
            if "shopee.vn/api/v4/" in url:
                # logger.debug(f"🔍 API Hit: {url[:80]}...")
                if "item/get" in url or "pdp/get" in url:
                    try:
                        if response.status == 200:
                            json_res = response.json()
                            data = json_res.get("data")
                            if data and (data.get("name") or data.get("itemid")):
                                self.last_api_data = data
                                # logger.debug(f"🔥 [INTERCEPT] Bắt được API: {data.get('name', 'N/A')[:40]}...")
                    except: pass
        
        self.browser_context.on("response", on_response)
        
        # Warm-up nhẹ nhàng ở lần đầu tiên
        try:
            # Đi từ trang chủ để giống người dùng thật hơn
            self.page.goto(SHOPEE_BASE_URL, wait_until="domcontentloaded", timeout=30000)
            
            # Mô phỏng di chuyển chuột ngẫu nhiên để làm ấm
            for _ in range(3):
                self.page.mouse.move(random.randint(100, 700), random.randint(100, 500))
                time.sleep(random.uniform(0.5, 1.5))
                
            time.sleep(random.uniform(1, 2))
        except:
            pass

    def stop(self):
        """Đóng trình duyệt và dọn dẹp tài nguyên."""
        try:
            if self.page:
                self.page.close()
            if self.browser_context:
                self.browser_context.close()
            if hasattr(self, 'browser') and self.browser:
                self.browser.close()
            if self.playwright:
                self.playwright.stop()
        except Exception as e:
            # logger.debug(f"Error during stop: {e}")
            pass
        finally:
            self.page = None
            self.browser_context = None
            self.playwright = None
            self.browser = None

    def _random_delay(self, multiplier: float = 1.0) -> None:
        delay = random.uniform(self.delay_min, self.delay_max) * multiplier
        time.sleep(delay)

    def _extract_product_data(self, item: dict) -> ShopeeProduct:
        """Chuyển đổi dữ liệu thô sang ShopeeProduct tối giản."""
        # Lấy giá min/max từ API hoặc DOM
        p_min = item.get("price_min", item.get("price", 0))
        p_max = item.get("price_max", p_min)
        
        # Nếu là dữ liệu API Shopee, giá thường bị nhân 100,000
        if p_min > 1000000:
            p_min /= SHOPEE_PRICE_UNIT
            p_max /= SHOPEE_PRICE_UNIT

        # Xử lý danh sách ảnh
        raw_images = item.get("images", [])
        processed_images = []
        for img in raw_images:
            if not img.startswith("http"):
                processed_images.append(f"https://down-vn.img.susercontent.com/file/{img}")
            else:
                processed_images.append(img)

        return ShopeeProduct(
            title=item.get("name", ""),
            price_min=float(p_min),
            price_max=float(p_max),
            images=processed_images, # Lấy toàn bộ ảnh có sẵn
        )

    def get_product(self, shop_id: str, item_id: str, url: Optional[str] = None) -> Optional[ShopeeProduct]:
        """Lấy thông tin sản phẩm dùng session hiện có."""
        self._total_requests += 1
        self.last_api_data = None 
        
        # Kiểm tra xem page có còn sống không, nếu không thì khởi động lại
        try:
            if not self.page or self.page.is_closed():
                self.start()
        except:
            self.start()
        
        page = self.page
        if not url:
            url = f"{SHOPEE_BASE_URL}/product/{shop_id}/{item_id}?lang=vi"
        
        max_retries = 2
        for attempt in range(max_retries):
            try:
                # 1. Điều hướng/Reload
                if attempt > 0:
                    logger.info(f"[*] Thử lại lần {attempt} sau khi giải Captcha...")
                    page.reload(wait_until="domcontentloaded")
                else:
                    page.goto(url, wait_until="domcontentloaded", timeout=30000)

                # 2. Polling API (Thụ động & Chủ động)
                for _ in range(15):
                    if self.last_api_data:
                        product = self._extract_product_data(self.last_api_data)
                        self._successful_requests += 1
                        return product
                    
                    if _ % 2 == 0:
                        try:
                            api_url = f"https://shopee.vn/api/v4/item/get?itemid={item_id}&shopid={shop_id}"
                            active_data = page.evaluate(f"async () => {{ const r = await fetch('{api_url}'); return await r.json(); }}")
                            if active_data.get("data") and active_data["data"].get("name"):
                                self.last_api_data = active_data["data"]
                                product = self._extract_product_data(self.last_api_data)
                                self._successful_requests += 1
                                return product
                        except: pass

                    if "shopee.vn/verify" in page.url:
                        break
                    time.sleep(0.5)

                # 3. Xử lý Captcha
                if "shopee.vn/verify" in page.url:
                    logger.warning("⚠️ Shopee đang bắt giải Captcha! Vui lòng can thiệp...")
                    solved = False
                    for _ in range(180):
                        if "shopee.vn/verify" not in page.url:
                            logger.info("✅ Đã vượt qua Captcha")
                            time.sleep(2)
                            solved = True
                            break
                        time.sleep(1)
                    if solved: continue
                    else: return None

                # 4. Fallback DOM
                logger.warning("⚠️ API thất bại, đang thử cào DOM...")
                page.wait_for_selector("h1, .product-briefing", timeout=5000)
                full_title = page.title()
                title = full_title.split("|")[0].strip() if "|" in full_title else full_title
                
                heuristics = page.evaluate("""() => {
                    const res = { price_min: 0, images: [] };
                    const p = document.querySelector('.G27LRz, .pq6_tw');
                    if (p) res.price_min = parseInt(p.innerText.replace(/[^0-9]/g, '')) || 0;
                    res.images = Array.from(document.querySelectorAll('img')).map(i => i.src).slice(0, 3);
                    return res;
                }""")
                
                if title and title != "Shopee Việt Nam":
                    self.last_api_data = {
                        "name": title, "itemid": item_id, "shopid": shop_id,
                        "price_min": heuristics['price_min'], "price_max": heuristics['price_min'],
                        "images": heuristics['images']
                    }
                    product = self._extract_product_data(self.last_api_data)
                    self._successful_requests += 1
                    return product

            except Exception as e:
                logger.error(f"❌ Lỗi: {e}")
        
        return None

    def get_stats(self) -> dict:
        failed = self._total_requests - self._successful_requests
        rate = (self._successful_requests / self._total_requests * 100) if self._total_requests > 0 else 0
        return {
            "total": self._total_requests, 
            "success": self._successful_requests, 
            "failed": failed, 
            "rate": f"{rate:.1f}%"
        }
