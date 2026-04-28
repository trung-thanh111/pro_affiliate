import requests
import json
import urllib.parse
from bs4 import BeautifulSoup
from utils import normalize_price, get_common_headers, retry

class TikTokCrawler:
    def __init__(self):
        self.headers = get_common_headers()

    @retry(Exception, tries=2, delay=3)
    def crawl(self, url):
        try:
            response = requests.get(url, headers=self.headers, timeout=15)
            response.raise_for_status()
            html = response.text
            soup = BeautifulSoup(html, 'html.parser')

            # 1. Thử parse RENDER_DATA
            render_script = soup.find('script', id='RENDER_DATA')
            if render_script:
                try:
                    raw_json = urllib.parse.unquote(render_script.string)
                    data = json.loads(raw_json)
                    product = self._find_product_recursive(data)
                    if product:
                        return self._map_data(product)
                except:
                    pass

            # 2. Fallback sang Meta Tags
            return self._fallback_meta(soup)

        except Exception as e:
            return {"error": f"Lỗi TikTok: {str(e)}"}

    def _find_product_recursive(self, obj):
        if isinstance(obj, dict):
            if "title" in obj and "product_id" in obj:
                return obj
            for v in obj.values():
                res = self._find_product_recursive(v)
                if res: return res
        elif isinstance(obj, list):
            for i in obj:
                res = self._find_product_recursive(i)
                if res: return res
        return None

    def _map_data(self, p):
        price_info = p.get("price", {})
        return {
            "name": p.get("title", ""),
            "price": normalize_price(price_info.get("sale_price", 0)),
            "original_price": normalize_price(price_info.get("original_price", 0)),
            "images": [p.get("main_image", {}).get("url_list", [""])[0]],
            "description": p.get("description", ""),
            "source": "tiktok"
        }

    def _fallback_meta(self, soup):
        def get_meta(prop):
            tag = soup.find("meta", property=prop) or soup.find("meta", attrs={"name": prop})
            return tag["content"] if tag else ""

        name = get_meta("og:title")
        image = get_meta("og:image")
        desc = get_meta("og:description")

        if not name:
            return {"error": "Không thể lấy dữ liệu TikTok (Bị chặn hoặc cấu trúc thay đổi)"}

        return {
            "name": name,
            "price": 0,
            "original_price": 0,
            "images": [image] if image else [],
            "description": desc,
            "source": "tiktok"
        }

def crawl_tiktok(url):
    crawler = TikTokCrawler()
    return crawler.crawl(url)
