import os
import json
import re
import logging
import time
from playwright.sync_api import sync_playwright
from utils import normalize_price, retry

logger = logging.getLogger("ShopeeCrawler")

class ShopeeCrawler:
    def __init__(self):
        self.ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36"

    @retry(Exception, tries=2, delay=5)
    def crawl(self, url):
        url = url.strip()
        
        shop_id = ""
        item_id = ""
        match = re.search(r'i\.(\d+)\.(\d+)', url)
        if match:
            shop_id, item_id = match.groups()

        with sync_playwright() as p:
            logger.info("Launching Nuclear Stealth Crawler (Persistent Context)...")
            
            # Sử dụng thư mục profile tạm thời để lưu giữ session/cookies tốt hơn
            user_data_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), "logs", "shopee_profile")
            if not os.path.exists(user_data_dir): os.makedirs(user_data_dir)
            
            context = p.chromium.launch_persistent_context(
                user_data_dir,
                headless=True,
                user_agent=self.ua,
                viewport={'width': 1920, 'height': 1080},
                locale="vi-VN",
                timezone_id="Asia/Ho_Chi_Minh",
                args=['--disable-blink-features=AutomationControlled']
            )
            
            page = context.pages[0] if context.pages else context.new_page()
            
            # Anti-bot injection
            page.add_init_script("""
                Object.defineProperty(navigator, 'webdriver', {get: () => undefined});
                window.chrome = { runtime: {} };
            """)
            
            api_data = None

            def handle_response(response):
                nonlocal api_data
                u = response.url
                if any(x in u for x in ["api/v4/item/get", "api/v2/item/get"]) and response.status == 200:
                    try:
                        d = response.json().get("data")
                        if d and d.get("name"):
                            api_data = d
                            logger.info("API Intercepted!")
                    except: pass

            page.on("response", handle_response)

            try:
                # BƯỚC 1: WARM UP & LANGUAGE BYPASS
                logger.info("Warming up session...")
                page.goto("https://shopee.vn/", wait_until="domcontentloaded", timeout=30000)
                time.sleep(3)
                
                # Force click Tiếng Việt và xóa Overlay
                page.evaluate("""() => {
                    const findAndClick = (text) => {
                        const elements = [...document.querySelectorAll('button, div, span, a')];
                        const target = elements.find(e => e.innerText && e.innerText.trim() === text);
                        if (target) { target.click(); return true; }
                        return false;
                    };
                    findAndClick('Tiếng Việt');
                    // Xóa các popup/overlay gây nghẽn
                    document.querySelectorAll('.shopee-popup, .shopee-modal, .shopee-popup__overlay').forEach(el => el.remove());
                }""")
                
                # Set cookie trực tiếp vào browser
                context.add_cookies([{'name': 'shopee_language', 'value': 'vi', 'domain': '.shopee.vn', 'path': '/'}])
                
                # BƯỚC 2: TRUY CẬP SẢN PHẨM
                logger.info(f"Navigating to product: {url}")
                page.goto(url, wait_until="load", timeout=60000)
                
                # Chờ spinner biến mất (3 chấm)
                logger.info("Waiting for page stability...")
                for _ in range(20):
                    is_loading = page.evaluate("() => !!document.querySelector('.shopee-dot-loading, .shopee-loading-spinner')")
                    if not is_loading: break
                    time.sleep(0.5)
                
                # Cuộn trang để trigger API
                page.evaluate("window.scrollTo(0, 800)")
                time.sleep(2)
                
                # BƯỚC 3: TRÍCH XUẤT ĐA TẦNG
                for _ in range(10):
                    if api_data: break
                    
                    # 1. INITIAL_STATE
                    state = page.evaluate("window.__INITIAL_STATE__")
                    if state:
                        d = state.get("item", {}).get("item") or state.get("product", {}).get("product")
                        if d and d.get("name"):
                            api_data = d
                            logger.info("Found in JS State")
                            break
                    
                    # 2. Direct API call via page
                    if shop_id and item_id:
                        api_url = f"https://shopee.vn/api/v4/item/get?itemid={item_id}&shopid={shop_id}"
                        try:
                            res = page.evaluate(f"fetch('{api_url}').then(r => r.json())")
                            if res and res.get("data") and res.get("data").get("name"):
                                api_data = res.get("data")
                                logger.info("Found via injected API")
                                break
                        except: pass
                    
                    time.sleep(1)

                if not api_data:
                    logger.info("Last resort: DOM Scraping...")
                    name = page.query_selector("h1, .VPh_S6, ._44qnta")
                    price = page.query_selector(".pq7uM9, .G27LRz, ._3e_u36")
                    if name and price:
                        api_data = {"name": name.inner_text(), "price": price.inner_text()}

                if not api_data or not api_data.get("name"):
                    raise Exception("Shopee blocking or parsing failed after nuclear attempt.")

                # Chuẩn hóa giá
                raw_p = api_data.get("price", 0)
                if isinstance(raw_p, str): raw_p = normalize_price(raw_p) * 100000
                p_final = int(raw_p // 100000) if raw_p > 1000000 else int(raw_p)

                return {
                    "name": api_data.get("name"),
                    "price": p_final,
                    "original_price": p_final,
                    "images": [f"https://cf.shopee.vn/file/{img}" if img and not img.startswith("http") else img for img in api_data.get("images", []) if img][:5],
                    "description": api_data.get("description", ""),
                    "source": "shopee"
                }

            except Exception as e:
                try:
                    log_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), "logs")
                    page.screenshot(path=os.path.join(log_dir, "error_shopee.png"))
                    with open(os.path.join(log_dir, "error_shopee.html"), "w", encoding="utf-8") as f:
                        f.write(page.content())
                except: pass
                logger.error(f"Shopee error: {str(e)}")
                raise e
            finally:
                context.close()

def crawl_shopee(url):
    crawler = ShopeeCrawler()
    try:
        return crawler.crawl(url)
    except Exception as e:
        return {"error": f"Lỗi Shopee: {str(e)}"}
