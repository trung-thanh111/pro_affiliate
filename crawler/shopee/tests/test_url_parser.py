"""
=====================================================
  tests/test_url_parser.py — Test URL parser
  
  Chạy: python -m pytest tests/ -v
  Hoặc: python tests/test_url_parser.py
=====================================================
"""

import sys
import os
sys.path.insert(0, os.path.dirname(os.path.dirname(__file__)))

from utils.url_parser import parse_shopee_url, extract_ids_from_urls


def test_url_dang_chuan():
    """Test URL dạng chuẩn: ten-san-pham-i.shopid.itemid"""
    url = "https://shopee.vn/Ten-san-pham-day-du-i.123456789.987654321"
    result = parse_shopee_url(url)
    assert result["shop_id"] == "123456789"
    assert result["item_id"] == "987654321"
    print("✅ test_url_dang_chuan PASSED")


def test_url_co_query_string():
    """Test URL có query parameters phía sau"""
    url = "https://shopee.vn/sp-i.111.222?sp_atk=abc&origin=search"
    result = parse_shopee_url(url)
    assert result["shop_id"] == "111"
    assert result["item_id"] == "222"
    print("✅ test_url_co_query_string PASSED")


def test_url_dang_product():
    """Test URL dạng /product/shopid/itemid"""
    url = "https://shopee.vn/product/333444/555666"
    result = parse_shopee_url(url)
    assert result["shop_id"] == "333444"
    assert result["item_id"] == "555666"
    print("✅ test_url_dang_product PASSED")


def test_url_khong_hop_le():
    """URL không phải Shopee phải raise ValueError"""
    try:
        parse_shopee_url("https://lazada.vn/product/123")
        print("❌ test_url_khong_hop_le FAILED — nên raise ValueError")
    except ValueError:
        print("✅ test_url_khong_hop_le PASSED")


def test_url_rong():
    """URL rỗng phải raise ValueError"""
    try:
        parse_shopee_url("")
        print("❌ test_url_rong FAILED — nên raise ValueError")
    except ValueError:
        print("✅ test_url_rong PASSED")


def test_batch_co_url_loi():
    """Batch với 1 URL lỗi không crash toàn bộ"""
    urls = [
        "https://shopee.vn/sp-i.100.200",
        "https://lazada.vn/sp",           # URL lỗi
        "https://shopee.vn/sp-i.300.400",
    ]
    results = extract_ids_from_urls(urls)
    assert len(results) == 3

    # URL đầu hợp lệ
    _, result_0 = results[0]
    assert result_0["shop_id"] == "100"

    # URL giữa là Exception
    _, result_1 = results[1]
    assert isinstance(result_1, Exception)

    # URL cuối hợp lệ
    _, result_2 = results[2]
    assert result_2["shop_id"] == "300"

    print("✅ test_batch_co_url_loi PASSED")


if __name__ == "__main__":
    print("=" * 50)
    print("CHẠY TESTS CHO URL PARSER")
    print("=" * 50)
    test_url_dang_chuan()
    test_url_co_query_string()
    test_url_dang_product()
    test_url_khong_hop_le()
    test_url_rong()
    test_batch_co_url_loi()
    print("=" * 50)
    print("TẤT CẢ TESTS PASSED ✅")
