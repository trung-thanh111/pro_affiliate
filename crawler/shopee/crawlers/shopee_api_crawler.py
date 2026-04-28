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
from playwright_stealth import stealth_sync

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

    def start(self):
        """Khởi động trình duyệt một lần duy nhất cho toàn bộ quá trình."""
        if self.page: return
        
        # logger.debug("🚀 Khởi động trình duyệt duy nhất cho toàn bộ quá trình...")
        self.playwright = sync_playwright().start()
        
        user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36"
        
        self.browser_context = self.playwright.chromium.launch_persistent_context(
            self.user_data_dir,
            headless=True,
            channel="chrome",
            user_agent=user_agent,
            viewport={'width': 1366, 'height': 768},
            ignore_default_args=["--enable-automation"],
            args=[
                '--disable-blink-features=AutomationControlled',
                '--disable-infobars',
                '--no-sandbox',
                '--disable-dev-shm-usage',
                '--no-first-run',
            ]
        )
        
        self.page = self.browser_context.pages[0] if self.browser_context.pages else self.browser_context.new_page()
        stealth_sync(self.page)
        
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
        # logger.debug("Warming up session...")
        try:
            self.page.goto(SHOPEE_BASE_URL, wait_until="networkidle", timeout=15000)
            time.sleep(random.uniform(1, 2))
        except:
            pass

    def stop(self):
        """Đóng trình duyệt và giải phóng tài nguyên."""
        if self.browser_context:
            self.browser_context.close()
        if self.playwright:
            self.playwright.stop()
        self.page = None

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
        self.last_api_data = None # Reset cho lượt mới
        
        # Tự động khởi động nếu chưa có page
        if not self.page:
            self.start()
        
        page = self.page
        browser_context = self.browser_context

        if not url:
            url = f"{SHOPEE_BASE_URL}/product/{shop_id}/{item_id}?lang=vi"
        
        try:
            # 1. Truy cập URL (Đợi domcontentloaded là đủ để script chạy)
            try:
                page.goto(url, wait_until="domcontentloaded", timeout=30000)
            except Exception as e:
                logger.warning(f"Goto timeout (domcontentloaded), continuing to check background data...")

            # ─── CHIẾT XUẤT SIÊU TỐC (Active & Passive Polling) ─────────────
            # logger.debug("⚡ Đang trích xuất dữ liệu siêu tốc...")
            
            for _ in range(12): # 12 x 0.5s = 6s
                # ƯU TIÊN 1: Kiểm tra Interceptor (Thụ động)
                if self.last_api_data:
                    product = self._extract_product_data(self.last_api_data)
                    # logger.debug(f"⚡ [FAST-TRACK] Bắt được API Shopee sau {(_ + 1) * 0.5}s")
                    self._successful_requests += 1
                    return product
                
                # ƯU TIÊN 2: Active Fetch (Ép gọi chủ động)
                if _ % 2 == 0: # Thử sau mỗi 1s
                    try:
                        api_url = f"https://shopee.vn/api/v4/item/get?itemid={item_id}&shopid={shop_id}"
                        active_data = page.evaluate(f"async () => {{ const r = await fetch('{api_url}'); return await r.json(); }}")
                        if active_data.get("data") and active_data["data"].get("name"):
                            self.last_api_data = active_data["data"]
                            product = self._extract_product_data(self.last_api_data)
                            # logger.debug(f"⚡ [FAST-TRACK] Ép gọi API chủ động thành công!")
                            self._successful_requests += 1
                            return product
                    except: pass

                # Check nhanh Captcha để thoát loop sớm nếu bị chặn
                if "verify/traffic" in page.url or page.query_selector("text='word word word'"):
                    break
                time.sleep(0.5)

            # 1. Kiểm tra chặn truy cập (Captcha)
            if "verify/traffic" in page.url or page.query_selector("text='word word word'"):
                logger.warning("⚠️ Shopee đang bắt giải Captcha! Vui lòng can thiệp...")
                for _ in range(120):
                    if "verify/traffic" not in page.url:
                        logger.info("✅ Đã vượt qua Captcha")
                        time.sleep(1)
                        break
                    time.sleep(1)
                else:
                    logger.error("❌ Quá thời gian chờ giải Captcha!")
                    return None

            # 2. Heuristic DOM Scraping (Cuối cùng)
            logger.warning("⚠️ API thất bại, đang thử cào DOM...")
            try:
                # Đợi title ổn định
                page.wait_for_selector("h1, .product-briefing", timeout=3000)
                full_title = page.title()
                title = full_title.split("|")[0].strip() if "|" in full_title else full_title
                
                extraction_js = """
                () => {
                    const results = { price_min: 0, price_max: 0, images: [] };
                    const priceEls = Array.from(document.querySelectorAll('.G27LRz, .pq6_tw, ._3e_u38'))
                        .filter(el => el.innerText.includes('₫'));
                    if (priceEls.length > 0) {
                        const pStr = priceEls[0].innerText.replace(/[^0-9]/g, '');
                        results.price_min = results.price_max = parseInt(pStr) || 0;
                    }
                    const imgEls = Array.from(document.querySelectorAll('img'))
                        .filter(img => img.src.includes('http') && (img.src.includes('file') || img.src.includes('img')));
                    results.images = [...new Set(imgEls.map(img => img.src))].slice(0, 5);
                    return results;
                }
                """
                heuristics = page.evaluate(extraction_js)
                if title and title != "Shopee Việt Nam":
                    self.last_api_data = {
                        "name": title, "itemid": item_id, "shopid": shop_id,
                        "price_min": heuristics['price_min'], "price_max": heuristics['price_max'],
                        "images": heuristics['images']
                    }
                    product = self._extract_product_data(self.last_api_data)
                    logger.info(f"✅ Thành công (DOM): {product.title[:40]}...")
                    self._successful_requests += 1
                    return product
            except: pass

            # Thất bại hoàn toàn
            error_img = os.path.join(OUTPUT_DIR, f"error_{item_id}.png")
            page.screenshot(path=error_img)
            logger.error(f"❌ Thất bại hoàn toàn: {item_id}")
            return None

        except Exception as e:
            logger.error(f"💥 Lỗi: {str(e)}")
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
