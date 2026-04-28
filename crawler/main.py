import sys
import os
import json
import logging
import io
import base64

# Ép kiểu encoding UTF-8 cho toàn bộ output (Fix lỗi charmap trên Windows)
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

# Thêm thư mục hiện tại vào path để import được các file local
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from shopee import crawl_shopee
from lazada import crawl_lazada
from tiktok import crawl_tiktok

# Setup logging to both stderr and file
log_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), "logs")
if not os.path.exists(log_dir):
    os.makedirs(log_dir)

log_file = os.path.join(log_dir, "crawler.log")
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.StreamHandler(sys.stderr),
        logging.FileHandler(log_file, encoding='utf-8')
    ]
)
logger = logging.getLogger("CrawlerMain")
logger.info("--- New Crawl Session Started ---")

def crawl_product(url):
    """
    Dispatcher chính: nhận diện domain và điều hướng đến crawler phù hợp.
    """
    url = url.strip()
    if not url:
        return {"error": "URL không được để trống"}
        
    try:
        logger.info(f"Processing URL: {url}")
        if "shopee.vn" in url:
            logger.info("Domain detected: Shopee")
            return crawl_shopee(url)
        elif "lazada.vn" in url:
            logger.info("Domain detected: Lazada")
            return crawl_lazada(url)
        elif "tiktok.com" in url:
            logger.info("Domain detected: TikTok")
            return crawl_tiktok(url)
        else:
            logger.warning(f"Unsupported domain for URL: {url}")
            return {"error": "Domain không được hỗ trợ. Chỉ hỗ trợ Shopee, Lazada, TikTok."}
    except Exception as e:
        logger.error(f"System Error: {str(e)}", exc_info=True)
        return {"error": f"Lỗi hệ thống: {str(e)}"}

if __name__ == "__main__":
    if len(sys.argv) > 1:
        input_data = sys.argv[1]
        
        # Thử giải mã Base64 (Laravel gửi qua) để tránh lỗi ký tự đặc biệt trên Windows Shell
        try:
            # Sửa lại padding nếu thiếu
            missing_padding = len(input_data) % 4
            if missing_padding:
                input_data += '=' * (4 - missing_padding)
            url = base64.b64decode(input_data).decode('utf-8')
            logger.info("Successfully decoded Base64 URL from Laravel")
        except Exception as e:
            logger.warning(f"Base64 decode failed or not encoded, using raw input: {str(e)}")
            url = input_data
            
        result = crawl_product(url)
        # Trả về JSON chuẩn cho Laravel nhận diện
        print(json.dumps(result, ensure_ascii=False))
    else:
        print(json.dumps({"error": "Thiếu tham số URL"}, ensure_ascii=False))
