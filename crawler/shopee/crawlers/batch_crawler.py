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

from config import OUTPUT_JSON_FILE, MAX_BATCH_SIZE, OUTPUT_DIR
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
    if len(unique_urls) < len(urls):
        logger.info(f"Loại bỏ {len(urls) - len(unique_urls)} URL trùng")
    urls = unique_urls

    # ── Khởi tạo crawler nếu chưa có ─────────────
    if crawler is None:
        crawler = ShopeeApiCrawler()

    products: list[ShopeeProduct] = []
    errors:   list[dict]          = []

    # 🚀 KHỞI ĐỘNG BROWSER DUY NHẤT
    crawler.start()

    try:
        logger.info(f"🚀 Bắt đầu crawl {len(urls)} URL...")

        # ── Parse tất cả URLs trước ──────────────────
        parsed_urls = extract_ids_from_urls(urls)

        # ── Crawl từng URL ───────────────────────────
        for index, (url, parse_result) in enumerate(parsed_urls, start=1):
            prefix = f"[{index:>3}/{len(urls)}]"

            # Trường hợp parse URL thất bại
            if isinstance(parse_result, Exception):
                logger.warning(f"{prefix} ❌ Parse lỗi: {url[:60]}")
                errors.append({"url": url, "reason": str(parse_result)})
                continue

            # Crawl sản phẩm
            logger.info(f"{prefix} 🔍 {url[:60]}...")
            product = crawler.get_product(
                shop_id=parse_result["shop_id"],
                item_id=parse_result["item_id"],
                url=url
            )

            if product:
                products.append(product)
                # Giới hạn độ dài title khi in log cho đẹp
                short_title = product.title[:50] + "..." if len(product.title) > 50 else product.title
                logger.info(f"{prefix} ✅ {short_title}")
            else:
                errors.append({"url": url, "reason": "Crawl thất bại (Kiểm tra log/ảnh lỗi)"})
                logger.warning(f"{prefix} ⚠️ Không lấy được dữ liệu")
            
            # Nghỉ ngắn giữa các link để tăng tốc (Dữ liệu API giờ bắt rất nhanh)
            if index < len(parsed_urls):
                delay = random.uniform(1, 2)
                logger.info(f"⏳ Nghỉ nhanh {delay:.1f}s...")
                time.sleep(delay)

        # ── Tạo kết quả ──────────────────────────────
        result = BatchResult(products=products, errors=errors)
        result.print_summary()

        # ── In thống kê crawler ───────────────────────
        stats = crawler.get_stats()
        logger.info(
            f"📈 Thống kê crawler: {stats['success']}/{stats['total']} request thành công "
            f"({stats['rate']})"
        )

        # ── Lưu JSON ra file ─────────────────────────
        if save_to_file and products:
            save_products_to_json(products)

        return result

    finally:
        crawler.stop()


def save_products_to_json(
    products: list[ShopeeProduct],
    filepath: str = OUTPUT_JSON_FILE,
) -> None:
    """
    Lưu danh sách sản phẩm ra file JSON.
    Tạo thư mục nếu chưa tồn tại.
    """
    os.makedirs(os.path.dirname(filepath), exist_ok=True)

    data = [p.to_dict() for p in products]
    with open(filepath, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

    logger.info(f"💾 Đã lưu {len(products)} sản phẩm → {filepath}")


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

    logger.info(f"📄 Đọc được {len(urls)} URL từ {filepath}")
    return urls
