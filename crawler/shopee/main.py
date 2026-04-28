"""
=====================================================
  main.py — Chạy crawler từ command line
  
  Dùng khi không cần Laravel, chỉ muốn crawl
  và lưu kết quả ra file JSON.
  
  Sử dụng:
    python main.py                          # Chạy demo URLs
    python main.py --file urls.txt          # Đọc URLs từ file
    python main.py --url https://shopee.vn/... # Crawl 1 URL
=====================================================
"""

import sys
import json
import argparse

from crawlers.batch_crawler import crawl_urls, load_urls_from_file
from crawlers.shopee_api_crawler import ShopeeApiCrawler
from utils.url_parser import parse_shopee_url
from utils.logger import get_logger

logger = get_logger("main")


def crawl_single_url(url: str) -> None:
    """Crawl 1 URL và in kết quả ra terminal."""
    logger.info(f"Crawl URL: {url}")
    
    try:
        ids = parse_shopee_url(url)
    except ValueError as e:
        logger.error(f"URL không hợp lệ: {e}")
        sys.exit(1)

    crawler = ShopeeApiCrawler()
    crawler.start()
    try:
        product = crawler.get_product(ids["shop_id"], ids["item_id"], url=url)

        if product:
            print("\n" + "═" * 60)
            print("✅ SẢN PHẨM ĐÃ CRAWL THÀNH CÔNG")
            print("═" * 60)
            print(json.dumps(product.to_dict(), ensure_ascii=False, indent=2))
        else:
            logger.error("Crawl thất bại")
            sys.exit(1)
    finally:
        crawler.stop()


def crawl_from_file(filepath: str) -> None:
    """Crawl danh sách URL từ file."""
    urls = load_urls_from_file(filepath)
    if not urls:
        logger.error("Không có URL nào để crawl")
        sys.exit(1)

    crawl_urls(urls, save_to_file=True)


def demo_crawl() -> None:
    """
    Demo: crawl các URL mẫu.
    Thay thế các URL dưới đây bằng URL Shopee thật.
    """
    demo_urls = [
        # ── Thay các URL này bằng URL Shopee thật của bạn ──
        "https://shopee.vn/example-product-i.123456789.987654321",
    ]
    
    logger.info("🎯 Chạy demo với URL mẫu...")
    logger.warning("⚠️  Hãy thay URL trong demo_urls bằng URL Shopee thật!")
    
    crawl_urls(demo_urls, save_to_file=True)


# ── CLI argument parser ───────────────────────────

def main():
    parser = argparse.ArgumentParser(
        description="Shopee Product Crawler",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Ví dụ sử dụng:
  python main.py                              # Chạy demo
  python main.py --url https://shopee.vn/... # Crawl 1 sản phẩm
  python main.py --file urls.txt             # Crawl từ file
  python main.py --file urls.txt             # File: mỗi dòng 1 URL
        """,
    )

    group = parser.add_mutually_exclusive_group()
    group.add_argument(
        "--url",
        type=str,
        help="URL Shopee của 1 sản phẩm cần crawl",
    )
    group.add_argument(
        "--file",
        type=str,
        help="Đường dẫn file .txt chứa danh sách URL (mỗi dòng 1 URL)",
    )

    args = parser.parse_args()

    if args.url:
        crawl_single_url(args.url)
    elif args.file:
        crawl_from_file(args.file)
    else:
        demo_crawl()


if __name__ == "__main__":
    main()
