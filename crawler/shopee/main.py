import os
import sys
import argparse
import time
import subprocess
from crawlers.batch_crawler import crawl_urls, load_urls_from_file, DOWNLOADS_DIR
from crawlers.shopee_api_crawler import ShopeeApiCrawler
from utils.url_parser import parse_shopee_url
from utils.logger import get_logger

logger = get_logger("main")

def clear_screen():
    os.system('cls' if os.name == 'nt' else 'clear')

def show_banner():
    print("="*65)
    print("      SHOPEE CRAWLER - BẢNG ĐIỀU KHIỂN")
    print("="*65)

def show_guide():
    clear_screen()
    print("="*65)
    print("             HƯỚNG DẪN SỬ DỤNG CHI TIẾT")
    print("="*65)
    print("\n1. ĐĂNG NHẬP (Bắt buộc lần đầu):")
    print("   - Chọn [1] để mở trình duyệt Shopee.")
    print("   - Đăng nhập tài khoản của bạn và giải Captcha (nếu có).")
    print("   - Quay lại terminal này và nhấn Enter để lưu session vĩnh viễn.")
    print("\n2. CRAWL HÀNG LOẠT (BATCH):")
    print("   - Tạo file 'urls.txt' trong cùng thư mục với tool này.")
    print("   - Mỗi dòng dán 1 link sản phẩm Shopee.")
    print("   - Chọn [3] và nhấn Enter để tool tự chạy.")
    print("\n3. KẾT QUẢ & TẢI VỀ:")
    print("   - Sau khi xong, tool sẽ tự động lưu file JSON vào thư mục DOWNLOADS.")
    print("   - Thư mục Downloads sẽ tự động bật lên để bạn lấy file.")
    print("\n" + "-"*65)
    input("Nhấn Enter để quay lại menu...")

def create_sample_file():
    filename = "urls.txt"
    if not os.path.exists(filename):
        with open(filename, "w", encoding="utf-8") as f:
            f.write("# Dán link sản phẩm Shopee vào đây, mỗi dòng 1 link\n")
            f.write("https://shopee.vn/product/89827191/23244410073\n")
        print(f"\n[✅] Đã tạo file mẫu '{filename}'. Bạn có thể mở nó lên để dán thêm link.")
    else:
        print(f"\n[!] File '{filename}' đã tồn tại.")
    time.sleep(2)

def login_manual():
    print("\n[*] Đang khởi động trình duyệt để đăng nhập...")
    crawler = ShopeeApiCrawler()
    try:
        crawler.start()
        print("\n" + "!"*60)
        print("  1. Vui lòng đăng nhập vào Shopee trên trình duyệt.")
        print("  2. Giải Captcha nếu xuất hiện.")
        print("  3. Sau khi xong, hãy QUAY LẠI ĐÂY và nhấn ENTER.")
        print("!"*60)
        input("\n Nhấn ENTER sau khi đã đăng nhập thành công...")
    finally:
        crawler.stop()
    print("\n[✅] Đã lưu session! Sẵn sàng thực chiến.")
    time.sleep(1)

def crawl_single_flow():
    url = input("\n Nhập link sản phẩm Shopee: ").strip()
    if not url: return
    
    try:
        ids = parse_shopee_url(url)
        print(f"[*] Đang xử lý: {url[:50]}...")
        crawler = ShopeeApiCrawler()
        try:
            crawler.start()
            product = crawler.get_product(ids['shop_id'], ids['item_id'], url)
            if product:
                from crawlers.batch_crawler import save_products_to_json
                saved_file = save_products_to_json([product])
                print(f"    ✅ Thành công! Đang tự động tải về thư mục Downloads...")
                print(f"    📂 File: {os.path.basename(saved_file)}")
                os.startfile(os.path.dirname(saved_file))
            else:
                print(f"    ❌ Thất bại: {url[:50]}")
        finally:
            crawler.stop()
    except KeyboardInterrupt:
        print("\n[⚠️] Đã hủy thao tác.")
    except Exception as e:
        print(f"\n[❌] Lỗi: {str(e)}")
    input("\nNhấn Enter để quay lại menu...")

def crawl_batch_flow():
    clear_screen()
    show_banner()
    print("\n CHẾ ĐỘ CRAWL DANH SÁCH")
    print("--------------------------------------------------")
    print("1. Tạo file 'urls.txt' và dán link vào.")
    print("2. Tool sẽ tự động cào và tải kết quả về máy bạn.")
    print("--------------------------------------------------")
    
    file_path = input("\n Nhập tên file (mặc định 'urls.txt'): ").strip() or "urls.txt"
    
    if not os.path.exists(file_path):
        print(f"\n[❌] Không tìm thấy file: {file_path}")
        print("Gợi ý: Chọn chức năng [4] ở menu chính để tạo file mẫu.")
        input("\nNhấn Enter để quay lại menu...")
        return

    crawler = ShopeeApiCrawler()
    try:
        crawler.start()
        urls = load_urls_from_file(file_path)
        if not urls:
            print("\n File rỗng, không có gì để crawl.")
        else:
            result, saved_file = crawl_urls(urls, crawler)
            if saved_file:
                print(f"\n HOÀN TẤT! Đã tải về: {os.path.basename(saved_file)}")
                print(f" Thư mục Downloads đang được mở...")
                os.startfile(os.path.dirname(saved_file))
    except KeyboardInterrupt:
        print("\n Đã dừng quá trình crawl batch theo yêu cầu.")
    finally:
        crawler.stop()
    input("\nNhấn Enter để quay lại menu...")

def main_menu():
    while True:
        clear_screen()
        show_banner()
        print("\n[1] ĐĂNG NHẬP / GIẢI CAPTCHA (Làm 1 lần duy nhất)")
        print("[2] CRAWL 1 LINK ĐƠN LẺ")
        print("[3] CRAWL DANH SÁCH TỪ FILE (BATCH)")
        print("[4] TẠO FILE 'urls.txt' MẪU")
        print("[5] HƯỚNG DẪN SỬ DỤNG")
        print("[6] THOÁT")
        
        choice = input("\n Chọn chức năng (1-6): ").strip()
        
        if choice == '1':
            login_manual()
        elif choice == '2':
            crawl_single_flow()
        elif choice == '3':
            crawl_batch_flow()
        elif choice == '4':
            create_sample_file()
        elif choice == '5':
            show_guide()
        elif choice == '6':
            print("\nChào tạm biệt! ")
            break
        else:
            print("\n Lựa chọn không hợp lệ.")
            time.sleep(1)

def main():
    parser = argparse.ArgumentParser(description="Shopee Crawler CLI")
    parser.add_argument("--url", help="Crawl 1 URL duy nhất")
    parser.add_argument("--file", help="Crawl danh sách URL từ file")
    args = parser.parse_args()

    if args.url:
        ids = parse_shopee_url(args.url)
        crawler = ShopeeApiCrawler()
        try:
            crawler.start()
            crawler.get_product(ids['shop_id'], ids['item_id'], args.url)
        finally:
            crawler.stop()
    elif args.file:
        crawler = ShopeeApiCrawler()
        try:
            crawler.start()
            urls = load_urls_from_file(args.file)
            crawl_urls(urls, crawler)
        finally:
            crawler.stop()
    else:
        main_menu()

if __name__ == "__main__":
    main()
