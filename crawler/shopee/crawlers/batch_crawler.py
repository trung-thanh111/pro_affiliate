"""
=====================================================
  crawlers/batch_crawler.py — Crawl hàng loạt URLs (Async)
=====================================================
"""

import json
import os
import asyncio
import time
import random
from dataclasses import dataclass
from typing import Optional

from config import MAX_BATCH_SIZE
DOWNLOADS_DIR = os.path.join(os.path.expanduser("~"), "Downloads")
from crawlers.shopee_api_crawler import ShopeeApiCrawler
from models.product import ShopeeProduct
from utils.url_parser import parse_shopee_url, extract_ids_from_urls
from utils.logger import get_logger

logger = get_logger("batch_crawler")

@dataclass
class BatchResult:
    products: list[ShopeeProduct]
    errors:   list[dict]

    @property
    def success_count(self) -> int: return len(self.products)
    @property
    def error_count(self) -> int: return len(self.errors)
    @property
    def total(self) -> int: return self.success_count + self.error_count

async def crawl_urls(
    urls: list[str],
    crawler: Optional[ShopeeApiCrawler] = None,
    save_to_file: bool = True,
) -> tuple[BatchResult, Optional[str]]:
    if not urls: return BatchResult([], []), None
    if len(urls) > MAX_BATCH_SIZE: urls = urls[:MAX_BATCH_SIZE]
    urls = list(dict.fromkeys(urls))

    if crawler is None:
        crawler = ShopeeApiCrawler()

    products: list[ShopeeProduct] = []
    errors:   list[dict]          = []

    try:
        parsed_urls = extract_ids_from_urls(urls)
        for index, (url, parse_result) in enumerate(parsed_urls, start=1):
            if isinstance(parse_result, Exception):
                errors.append({"url": url, "reason": str(parse_result)})
                continue

            print(f"[*] [{index}/{len(urls)}] Đang xử lý: {url[:50]}...")
            product = await crawler.get_product(
                shop_id=parse_result["shop_id"],
                item_id=parse_result["item_id"],
                url=url
            )

            if product:
                products.append(product)
                print(f"    ✅ Thành công: {product.name[:60]}...")
            else:
                errors.append({"url": url, "reason": "Crawl thất bại"})
                print(f"    ❌ Thất bại: {url[:50]}")
            
            if index < len(parsed_urls):
                await asyncio.sleep(random.uniform(1, 2))

        result = BatchResult(products=products, errors=errors)
        saved_file = None
        if save_to_file and products:
            saved_file = save_products_to_json(products)

        return result, saved_file

    except Exception as e:
        logger.error(f"Lỗi batch: {str(e)}")
        return BatchResult(products, errors), None

def save_products_to_json(products: list[ShopeeProduct], filepath: Optional[str] = None) -> str:
    if not filepath:
        timestamp = time.strftime("%Y%m%d_%H%M%S")
        filename = f"shopee-product-{timestamp}.json"
        filepath = os.path.join(DOWNLOADS_DIR, filename)

    os.makedirs(os.path.dirname(filepath), exist_ok=True)
    data = [p.to_dict() for p in products]
    with open(filepath, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=2)
    return filepath

def load_urls_from_file(filepath: str) -> list[str]:
    if not os.path.exists(filepath): return []
    with open(filepath, "r", encoding="utf-8") as f:
        return [line.strip() for line in f if line.strip() and not line.strip().startswith("#")]
