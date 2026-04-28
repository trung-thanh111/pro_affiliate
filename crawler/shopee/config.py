"""
=====================================================
  config.py — Cấu hình toàn bộ project
  Tất cả hằng số, endpoint, tuning param đặt ở đây
  Không hardcode giá trị trực tiếp trong code logic
=====================================================
"""

import os
from dotenv import load_dotenv

# Nạp biến môi trường từ file .env nếu có
load_dotenv()

# ─── Shopee API ───────────────────────────────────
SHOPEE_BASE_URL   = "https://shopee.vn"
SHOPEE_API_ITEM   = "https://shopee.vn/api/v4/item/get"
SHOPEE_IMAGE_CDN  = "https://down-vn.img.susercontent.com/file/"

# Giá Shopee lưu nhân 100000 (đơn vị nhỏ nhất: xu)
SHOPEE_PRICE_UNIT = 100_000

# ─── Crawl behavior ──────────────────────────────
# Thời gian chờ ngẫu nhiên giữa mỗi request (giây)
DELAY_MIN = 1.5
DELAY_MAX = 3.5

# Số lần retry khi request thất bại
MAX_RETRIES = 3

# Thời gian chờ tối đa mỗi request (giây)
REQUEST_TIMEOUT = 15

# Số sản phẩm tối đa trong 1 batch
MAX_BATCH_SIZE = 50

# ─── Headers giả lập browser Chrome ─────────────
DEFAULT_HEADERS = {
    "User-Agent": (
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) "
        "Chrome/124.0.0.0 Safari/537.36"
    ),
    "Referer":          "https://shopee.vn/",
    "Accept":           "application/json, text/plain, */*",
    "Accept-Language":  "vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7",
    "Accept-Encoding":  "gzip, deflate, br",
    "Connection":       "keep-alive",
    "X-API-SOURCE":     "pc",
    "X-Requested-With": "XMLHttpRequest",
    "af-ac-enc-dat":    "",
}

# ─── FastAPI server ───────────────────────────────
API_HOST = os.getenv("API_HOST", "0.0.0.0")
API_PORT = int(os.getenv("API_PORT", "8001"))

# ─── Output paths ─────────────────────────────────
OUTPUT_DIR       = os.path.join(os.path.dirname(__file__), "output")
OUTPUT_JSON_FILE = os.path.join(OUTPUT_DIR, "shopee_products.json")
LOG_FILE         = os.path.join(OUTPUT_DIR, "crawler.log")
