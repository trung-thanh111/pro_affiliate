import re
import urllib.parse
import time
from functools import wraps

def retry(exceptions, tries=3, delay=2, backoff=2):
    """
    Decorator để retry function khi gặp lỗi.
    """
    def decorator(f):
        @wraps(f)
        def wrapper(*args, **kwargs):
            _tries, _delay = tries, delay
            while _tries > 1:
                try:
                    return f(*args, **kwargs)
                except exceptions as e:
                    time.sleep(_delay)
                    _tries -= 1
                    _delay *= backoff
            return f(*args, **kwargs)
        return wrapper
    return decorator

def normalize_price(price_input):
    """Chuẩn hóa giá về kiểu int."""
    if price_input is None:
        return 0
    if isinstance(price_input, (int, float)):
        return int(price_input)
    
    # Loại bỏ VNĐ, $, dấu chấm, dấu phẩy
    clean_str = re.sub(r'[^\d]', '', str(price_input))
    return int(clean_str) if clean_str else 0

def extract_shopee_ids(url):
    """Trích xuất shop_id và item_id từ URL Shopee."""
    decoded_url = urllib.parse.unquote(url)
    
    # Pattern 1: i.SHOP_ID.ITEM_ID
    match = re.search(r'i\.(\d+)\.(\d+)', decoded_url)
    if match:
        return match.group(1), match.group(2)
    
    # Pattern 2: shopid=...&itemid=...
    shop_id = re.search(r'shopid=(\d+)', decoded_url)
    item_id = re.search(r'itemid=(\d+)', decoded_url)
    if shop_id and item_id:
        return shop_id.group(1), item_id.group(1)
        
    return None, None

def get_common_headers():
    return {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36",
        "Accept-Language": "vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7",
        "Accept": "application/json, text/plain, */*",
        "X-Requested-With": "XMLHttpRequest",
    }
