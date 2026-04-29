import os
import sys
import argparse
import time
import subprocess
import shutil
from config import OUTPUT_DIR
from crawlers.batch_crawler import crawl_urls, load_urls_from_file, DOWNLOADS_DIR
from crawlers.shopee_api_crawler import ShopeeApiCrawler
from utils.url_parser import parse_shopee_url
from utils.logger import get_logger

logger = get_logger("main")

def delete_profile():
    profile_path = os.path.join(OUTPUT_DIR, "shopee_profile")
    if os.path.exists(profile_path):
        print(f"\n[*] Đang xóa profile tại: {profile_path}...")
        try:
            # Kill chrome processes if any
            subprocess.run(['taskkill', '/F', '/IM', 'chrome.exe'], capture_output=True)
            time.sleep(1)
            shutil.rmtree(profile_path)
            print("[✅] Đã xóa profile thành công.")
        except Exception as e:
            print(f"[❌] Lỗi xóa: {str(e)}")
    else:
        print("\n[!] Không tìm thấy profile.")
    time.sleep(1)

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
    print("   - Chọn [4] và nhấn Enter để tool tự chạy.")
    print("\n3. SIÊU TÀNG HÌNH (REMOTE CHROME):")
    print("   - Chọn [2] để mở một cửa sổ Chrome.")
    print("   - Đăng nhập Shopee trên đó.")
    print("   - Khi crawl, chọn 'y' để kết nối vào cửa sổ này.")
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
        print(f"[✅] Đã tồn tại file '{filename}'.")
    time.sleep(1)

def launch_chrome_remote():
    """Hướng dẫn và tự động mở Chrome ở chế độ Remote Debugging"""
    profile_path = os.path.join(os.getcwd(), "output", "chrome_debug_profile")
    if not os.path.exists(profile_path):
        os.makedirs(profile_path, exist_ok=True)
    
    chrome_paths = [
        r"C:\Program Files\Google\Chrome\Application\chrome.exe",
        r"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe",
        os.path.expandvars(r"%LocalAppData%\Google\Chrome\Application\chrome.exe")
    ]
    chrome_exe = next((p for p in chrome_paths if os.path.exists(p)), None)
    
    if not chrome_exe:
        print("\n[❌] Không tìm thấy Google Chrome. Hãy mở thủ công với flag: --remote-debugging-port=9222")
        return False

    print("\n[*] Đang khởi động Google Chrome ...")
    cmd = f'"{chrome_exe}" --remote-debugging-port=9222 --user-data-dir="{profile_path}" --no-first-run'
    subprocess.Popen(cmd, shell=True)
    print("[✅] Đã mở Chrome. HÃY ĐĂNG NHẬP SHOPEE TRÊN ĐÓ TRƯỚC.")
    time.sleep(2)
    return True

def crawl_single_flow():
    url = input("\n Nhập link sản phẩm Shopee: ").strip()
    if not url: return
    
    ids = parse_shopee_url(url)
    print(f"[*] Đang xử lý: {url[:50]}...")
    use_remote = input(" Dùng Chrome đang mở ở Option [1] (y/n)? [y]: ").strip().lower() != 'n'
    
    crawler = ShopeeApiCrawler()
    try:
        crawler.start(headless=False, use_remote=use_remote)
        product = crawler.get_product(ids['shop_id'], ids['item_id'], url)
        if product:
            from crawlers.batch_crawler import save_products_to_json
            saved_file = save_products_to_json([product])
            print(f"    ✅ Thành công! File: {os.path.basename(saved_file)}")
            os.startfile(os.path.dirname(saved_file))
        else:
            print(f"    ❌ Thất bại: {url[:50]}")
    except Exception as e:
        print(f"\n[❌] Lỗi: {str(e)}")
    finally:
        crawler.stop()
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
        return
    
    use_remote = input("\n Dùng Chrome đang mở ở Option [1] (y/n)? [y]: ").strip().lower() != 'n'
    
    crawler = ShopeeApiCrawler()
    try:
        crawler.start(headless=False, use_remote=use_remote)
        urls = load_urls_from_file(file_path)
        if not urls:
            print("\n File rỗng.")
        else:
            result, saved_file = crawl_urls(urls, crawler)
            if saved_file:
                print(f"\n HOÀN TẤT! Đã lưu: {os.path.basename(saved_file)}")
                os.startfile(os.path.dirname(saved_file))
    except Exception as e:
        print(f"\n[❌] Lỗi: {str(e)}")
    finally:
        crawler.stop()
    input("\nNhấn Enter để quay lại menu...")

def main_menu():
    while True:
        clear_screen()
        show_banner()
        print("\n[0] XÓA PROFILE CŨ (Làm mới hoàn toàn)")
        # print("[1] ĐĂNG NHẬP (Làm 1 lần duy nhất)")
        print("[1] MỞ CHROME & ĐĂNG NHẬP THỦ CÔNG")
        print("[2] CRAWL 1 LINK ĐƠN LẺ")
        print("[3] CRAWL DANH SÁCH (urls.txt hoặc link file .txt)")
        print("[4] TẠO FILE 'urls.txt' MẪU")
        print("[5] HƯỚNG DẪN")
        print("[6] THOÁT")
        
        choice = input("\n Chọn chức năng (0-6): ").strip()
        
        if choice == '0':
            delete_profile()
        # elif choice == '1':
        #     from login_helper import manual_login
        #     manual_login()
        elif choice == '1':
            launch_chrome_remote()
        elif choice == '2':
            crawl_single_flow()
        elif choice == '3':
            crawl_batch_flow()
        elif choice == '4':
            create_sample_file()
        elif choice == '5':
            show_guide()
        elif choice == '6':
            break
        else:
            print("\n Lựa chọn không hợp lệ.")
            time.sleep(1)

def main():
    main_menu()

if __name__ == "__main__":
    main()
