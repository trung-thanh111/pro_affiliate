import sys
import json
import re
import requests
import urllib.parse

def crawl_product(url):
    # Chuẩn hóa URL
    if not url.startswith('http'):
        url = 'https://' + url
    
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "Accept-Language": "vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7",
        "Referer": "https://www.google.com/",
        "Sec-Ch-Ua": '"Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
        "Sec-Ch-Ua-Mobile": "?0",
        "Sec-Ch-Ua-Platform": '"Windows"',
    }

    try:
        session = requests.Session()
        response = session.get(url, headers=headers, timeout=15)
        response.raise_for_status()
        html = response.text

        if "tiktok.com" in url:
            return crawl_tiktok(html)
        elif "shopee.vn" in url:
            return crawl_shopee(html, url, session)
        elif "lazada.vn" in url:
            return crawl_lazada(html)
        else:
            return {"error": "Domain không hỗ trợ"}
    except Exception as e:
        return {"error": f"Lỗi kết nối: {str(e)}"}

def crawl_tiktok(html):
    try:
        match = re.search(r'<script id="RENDER_DATA" type="application/json">(.*?)</script>', html)
        if match:
            raw_data = match.group(1)
            # TikTok quotes and escapes JSON
            decoded_data = urllib.parse.unquote(raw_data)
            data = json.loads(decoded_data)
            
            def find_product(obj):
                if isinstance(obj, dict):
                    if "title" in obj and "product_id" in obj: return obj
                    for v in obj.values():
                        res = find_product(v)
                        if res: return res
                elif isinstance(obj, list):
                    for i in obj:
                        res = find_product(i)
                        if res: return res
                return None
            
            p = find_product(data)
            if p:
                price_val = p.get("price", {})
                return {
                    "name": p.get("title", ""),
                    "price": price_val.get("original_price", "0"),
                    "price_sale": price_val.get("sale_price", "0"),
                    "image": p.get("main_image", {}).get("url_list", [""])[0],
                    "description": p.get("description", "")
                }
        
        # Meta tags fallback
        return {
            "name": re.search(r'<meta property="og:title" content="(.*?)">', html).group(1) if re.search(r'<meta property="og:title" content="(.*?)">', html) else "TikTok Product",
            "price": "0",
            "price_sale": "0",
            "image": re.search(r'<meta property="og:image" content="(.*?)">', html).group(1) if re.search(r'<meta property="og:image" content="(.*?)">', html) else "",
            "description": "Lấy thông tin cơ bản từ Meta Tag. Chi tiết giá cần nhập tay."
        }
    except:
        return {"error": "Không thể phân tích dữ liệu TikTok"}

def crawl_shopee(html, url, session):
    try:
        # Bước 1: Tìm itemid và shopid
        item_id = None
        shop_id = None
        
        # Thử lấy từ URL (format: i.shop_id.item_id)
        id_match = re.search(r'i\.(\d+)\.(\d+)', url)
        if id_match:
            shop_id, item_id = id_match.groups()
        
        # Nếu URL không có, tìm trong HTML (INITIAL_STATE hoặc regex trực tiếp)
        if not item_id or not shop_id:
            # Tìm trong window.__INITIAL_STATE__
            state_match = re.search(r'window\.__INITIAL_STATE__\s*=\s*({.*?});', html)
            if state_match:
                try:
                    state_data = json.loads(state_match.group(1))
                    # Cấu trúc Shopee hay thay đổi, tìm thử ở vài chỗ
                    product_data = state_data.get("product", {}).get("product", {})
                    if product_data:
                        item_id = product_data.get("itemid")
                        shop_id = product_data.get("shopid")
                except: pass
            
            # Regex trực tiếp nếu vẫn chưa có
            if not item_id:
                item_id_match = re.search(r'"itemid":\s*(\d+)', html)
                if item_id_match: item_id = item_id_match.group(1)
            if not shop_id:
                shop_id_match = re.search(r'"shopid":\s*(\d+)', html)
                if shop_id_match: shop_id = shop_id_match.group(1)

        if item_id and shop_id:
            # Bước 2: Gọi API Shopee để lấy data sạch
            api_url = f"https://shopee.vn/api/v4/item/get?itemid={item_id}&shopid={shop_id}"
            api_headers = {
                "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
                "Referer": url,
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json",
            }
            api_res = session.get(api_url, headers=api_headers, timeout=10)
            data = api_res.json().get("data")
            
            if data:
                return {
                    "name": data.get("name", ""),
                    "price": str(int(data.get("price_before_discount", 0) or data.get("price", 0)) // 100000),
                    "price_sale": str(int(data.get("price", 0)) // 100000),
                    "image": f"https://down-vn.img.susercontent.com/file/{data.get('image', '')}",
                    "description": data.get("description", "")
                }

        # Nếu vẫn fail, dùng Meta tags làm phương án cuối
        name_match = re.search(r'<meta property="og:title" content="(.*?)">', html)
        img_match = re.search(r'<meta property="og:image" content="(.*?)">', html)
        if name_match:
            return {
                "name": name_match.group(1),
                "price": "0",
                "price_sale": "0",
                "image": img_match.group(1) if img_match else "",
                "description": "Lấy thông tin cơ bản từ Meta Tag. Shopee đã chặn API crawl sâu."
            }

        return {"error": "Shopee chặn truy cập hoặc link không đúng định dạng"}
    except Exception as e:
        return {"error": f"Lỗi Shopee: {str(e)}"}

def crawl_lazada(html):
    try:
        match = re.search(r'window\.__moduleData__\s*=\s*({.*?});', html)
        if match:
            data = json.loads(match.group(1))
            fields = data.get("data", {}).get("root", {}).get("fields", {})
            p = fields.get("product", {})
            if p:
                return {
                    "name": p.get("title", ""),
                    "price": p.get("price", {}).get("originalPrice", {}).get("value", "0"),
                    "price_sale": p.get("price", {}).get("salePrice", {}).get("value", "0"),
                    "image": p.get("mainImage", ""),
                    "description": p.get("desc", "")
                }
        return {"error": "Lazada chặn truy cập hoặc cấu trúc trang đã thay đổi"}
    except:
        return {"error": "Không thể lấy dữ liệu từ Lazada"}

if __name__ == "__main__":
    if len(sys.argv) > 1:
        url = sys.argv[1]
        result = crawl_product(url)
        print(json.dumps(result))
    else:
        print(json.dumps({"error": "Không có URL"}))
