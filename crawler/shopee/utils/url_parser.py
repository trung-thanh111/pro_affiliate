"""
=====================================================
  utils/url_parser.py — Parse URL Shopee
  
  Shopee URL có 2 dạng:
  1. https://shopee.vn/ten-sp-i.{shop_id}.{item_id}
  2. https://shopee.vn/product/{shop_id}/{item_id}
  
  Hàm này xử lý cả hai dạng.
=====================================================
"""

import re
from typing import TypedDict
from utils.logger import get_logger

logger = get_logger("url_parser")


class ShopeeIds(TypedDict):
    shop_id: str
    item_id: str


def parse_shopee_url(url: str) -> ShopeeIds:
    """
    Trích xuất shop_id và item_id từ URL Shopee.
    
    Args:
        url: URL sản phẩm Shopee bất kỳ
        
    Returns:
        Dict {'shop_id': '...', 'item_id': '...'}
        
    Raises:
        ValueError: nếu URL không phải định dạng Shopee hợp lệ
        
    Examples:
        >>> parse_shopee_url("https://shopee.vn/Ao-thun-i.123.456")
        {'shop_id': '123', 'item_id': '456'}
    """
    if not url or not isinstance(url, str):
        raise ValueError("URL không được để trống")

    url = url.strip()

    # ── Cách 1: dạng i.{shop_id}.{item_id} ──────
    # Ví dụ: .../Ten-san-pham-i.123456789.987654321
    pattern_standard = r'i\.(\d+)\.(\d+)'
    match = re.search(pattern_standard, url)
    if match:
        result = {
            "shop_id": match.group(1),
            "item_id": match.group(2),
        }
        logger.debug(f"Parsed (dạng chuẩn): {result}")
        return result

    # ── Cách 2: dạng /product/{shop_id}/{item_id} ─
    # Ví dụ: .../product/123456789/987654321
    pattern_product = r'/product/(\d+)/(\d+)'
    match = re.search(pattern_product, url)
    if match:
        result = {
            "shop_id": match.group(1),
            "item_id": match.group(2),
        }
        logger.debug(f"Parsed (dạng /product/): {result}")
        return result

    # ── Không khớp bất kỳ pattern nào ──────────
    raise ValueError(
        f"Không thể parse URL Shopee: '{url}'\n"
        "URL hợp lệ ví dụ: https://shopee.vn/Ten-sp-i.123.456"
    )


def extract_ids_from_urls(urls: list[str]) -> list[tuple[str, ShopeeIds | Exception]]:
    """
    Parse nhiều URL cùng lúc. Trả về list tuple (url, result_or_error).
    Không raise exception — lỗi được trả về trong tuple để xử lý sau.
    
    Returns:
        List of (url, ShopeeIds) hoặc (url, Exception)
    """
    results = []
    for url in urls:
        try:
            ids = parse_shopee_url(url)
            results.append((url, ids))
        except ValueError as e:
            logger.warning(f"Bỏ qua URL lỗi: {url} — {e}")
            results.append((url, e))
    return results
