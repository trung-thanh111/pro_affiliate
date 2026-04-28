from playwright.sync_api import sync_playwright
from utils import normalize_price, retry
import time

class LazadaCrawler:
    def __init__(self):
        self.ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36"

    @retry(Exception, tries=2, delay=5)
    def crawl(self, url):
        with sync_playwright() as p:
            # Launch browser (headless=True cho production, False để debug nếu cần)
            browser = p.chromium.launch(headless=True)
            context = browser.new_context(user_agent=self.ua)
            page = context.new_page()
            
            try:
                # Block ads/trackers để tăng tốc
                page.route("**/*.{png,jpg,jpeg,gif,webp,css,woff,woff2,svg,otf}", lambda route: route.abort())
                
                # Goto URL với timeout dài hơn (Lazada khá nặng)
                page.goto(url, wait_until="domcontentloaded", timeout=30000)
                
                # Đợi element chính xuất hiện
                page.wait_for_selector('h1', timeout=15000)
                
                # Scroll xuống để trigger lazy load nếu cần
                page.evaluate("window.scrollTo(0, 500)")
                time.sleep(1) # Chờ render nhẹ

                # Trích xuất dữ liệu với nhiều fallback selectors
                name = self._get_text(page, ['h1', '.pdp-mod-product-badge-title', '.pdp-product-title'])
                
                # Giá khuyến mãi
                price_sale_text = self._get_text(page, [
                    '.pdp-price_type_normal', 
                    '.pdp-product-price', 
                    '[class*="pdp-price_type_normal"]'
                ])
                
                # Giá gốc
                price_original_text = self._get_text(page, [
                    '.pdp-price_type_deleted', 
                    '.pdp-price__old',
                    '[class*="pdp-price_type_deleted"]'
                ])

                # Ảnh sản phẩm
                image = ""
                img_el = page.query_selector('.pdp-mod-common-image') or page.query_selector('.gallery-preview-panel__image')
                if img_el:
                    image = img_el.get_attribute('src')

                # Mô tả ngắn
                description = self._get_text(page, ['.pdp-product-detail', '.product-detail__content'])

                if not name or price_sale_text == "0":
                    raise Exception("Không trích xuất được thông tin cơ bản của Lazada")

                return {
                    "name": name.strip(),
                    "price": normalize_price(price_sale_text),
                    "original_price": normalize_price(price_original_text or price_sale_text),
                    "images": [image] if image else [],
                    "description": description.strip()[:1000],
                    "source": "lazada"
                }

            except Exception as e:
                raise e # Để decorator retry xử lý
            finally:
                browser.close()

    def _get_text(self, page, selectors):
        for selector in selectors:
            try:
                el = page.query_selector(selector)
                if el:
                    text = el.inner_text()
                    if text and text.strip():
                        return text.strip()
            except:
                continue
        return ""

def crawl_lazada(url):
    crawler = LazadaCrawler()
    try:
        return crawler.crawl(url)
    except Exception as e:
        return {"error": f"Lỗi Lazada sau nhiều lần thử: {str(e)}"}
