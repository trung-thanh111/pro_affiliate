"""
=====================================================
  crawlers/batch_crawler.py — Crawl hàng loạt URLs
  
  Nhận vào danh sách URLs Shopee, crawl từng cái,
  thu gom kết quả và lỗi riêng biệt.
  In progress rõ ràng, lưu JSON kết quả.
=====================================================
"""

import json
import os
import time
import random
from dataclasses import dataclass
from typing import Optional

from config import MAX_BATCH_SIZE
# Tự động lấy đường dẫn thư mục Downloads của hệ thống
DOWNLOADS_DIR = os.path.join(os.path.expanduser("~"), "Downloads")
from crawlers.shopee_api_crawler import ShopeeApiCrawler
from models.product import ShopeeProduct
from utils.url_parser import parse_shopee_url, extract_ids_from_urls
from utils.logger import get_logger

logger = get_logger("batch_crawler")


@dataclass
class BatchResult:
    """Kết quả sau khi crawl 1 batch URL."""
    products: list[ShopeeProduct]
    errors:   list[dict]

    @property
    def success_count(self) -> int:
        return len(self.products)

    @property
    def error_count(self) -> int:
        return len(self.errors)

    @property
    def total(self) -> int:
        return self.success_count + self.error_count

    def print_summary(self) -> None:
        logger.info("─" * 50)
        logger.info(f"📊 KẾT QUẢ BATCH: {self.success_count}/{self.total} thành công")
        if self.errors:
            logger.warning(f"❌ {self.error_count} lỗi:")
            for err in self.errors:
                logger.warning(f"   • {err['url'][:60]} — {err['reason']}")
        logger.info("─" * 50)


def crawl_urls(
    urls: list[str],
    crawler: Optional[ShopeeApiCrawler] = None,
    save_to_file: bool = True,
) -> BatchResult:
    """
    Crawl danh sách URL Shopee và trả về BatchResult.
    
    Args:
        urls:         Danh sách URL Shopee cần crawl
        crawler:      Instance crawler (tạo mới nếu không truyền vào)
        save_to_file: Có lưu JSON ra file không
        
    Returns:
        BatchResult chứa products thành công và errors
    """
    # ── Validate số lượng ────────────────────────
    if not urls:
        logger.error("Danh sách URL rỗng")
        return BatchResult(products=[], errors=[])

    if len(urls) > MAX_BATCH_SIZE:
        logger.warning(
            f"Quá {MAX_BATCH_SIZE} URL ({len(urls)}) — "
            f"chỉ xử lý {MAX_BATCH_SIZE} URL đầu"
        )
        urls = urls[:MAX_BATCH_SIZE]

    # ── Loại bỏ URL trùng lặp ────────────────────
    unique_urls = list(dict.fromkeys(urls))
    # if len(unique_urls) < len(urls):
    #     logger.info(f"Loại bỏ {len(urls) - len(unique_urls)} URL trùng")
    urls = unique_urls

    # ── Khởi tạo crawler nếu chưa có ─────────────
    if crawler is None:
        crawler = ShopeeApiCrawler()

    products: list[ShopeeProduct] = []
    errors:   list[dict]          = []

    try:
        # ── Parse tất cả URLs trước ──────────────────
        parsed_urls = extract_ids_from_urls(urls)

        # ─── Crawl từng URL ───────────────────────────
        try:
            for index, (url, parse_result) in enumerate(parsed_urls, start=1):
                if isinstance(parse_result, Exception):
                    errors.append({"url": url, "reason": str(parse_result)})
                    continue

                print(f"[*] [{index}/{len(urls)}] Đang xử lý: {url[:50]}...")
                product = crawler.get_product(
                    shop_id=parse_result["shop_id"],
                    item_id=parse_result["item_id"],
                    url=url
                )

                if product:
                    products.append(product)
                    print(f"    ✅ Thành công: {product.title[:60]}...")
                else:
                    errors.append({"url": url, "reason": "Crawl thất bại"})
                    print(f"    ❌ Thất bại: {url[:50]}")
                
                if index < len(parsed_urls):
                    time.sleep(random.uniform(1, 2))
        except KeyboardInterrupt:
            print("\n[⚠️] Đang hủy thao tác crawl theo yêu cầu...")
            # Trả về kết quả những gì đã crawl được đến lúc này
            pass

        # ── Tạo kết quả ──────────────────────────────
        result = BatchResult(products=products, errors=errors)

        # ── Lưu JSON ra file ─────────────────────────
        saved_file = None
        if save_to_file and products:
            saved_file = save_products_to_json(products)

        return result, saved_file

    except Exception as e:
        logger.error(f"Lỗi batch: {str(e)}")
        return BatchResult(products=products, errors=errors), None


def save_products_to_json(
    products: list[ShopeeProduct],
    filepath: Optional[str] = None,
) -> str:
    """
    Lưu danh sách sản phẩm ra file JSON với tên duy nhất.
    """
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
    """
    Đọc danh sách URL từ file text (mỗi dòng 1 URL).
    Bỏ qua dòng trống và dòng bắt đầu bằng #.
    """
    if not os.path.exists(filepath):
        logger.error(f"File không tồn tại: {filepath}")
        return []

    with open(filepath, "r", encoding="utf-8") as f:
        urls = [
            line.strip()
            for line in f
            if line.strip() and not line.strip().startswith("#")
        ]

    # logger.info(f"📄 Đọc được {len(urls)} URL từ {filepath}")
    return urls
