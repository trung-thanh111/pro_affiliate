import os
import sys
import argparse
import asyncio
import time
import subprocess
import shutil
from datetime import datetime
from config import OUTPUT_DIR
from crawlers.batch_crawler import crawl_urls, load_urls_from_file, DOWNLOADS_DIR
from crawlers.shopee_api_crawler import ShopeeApiCrawler
from utils.url_parser import parse_shopee_url
from utils.logger import get_logger

logger = get_logger("main")
import crawlers.shopee_api_crawler
print(f"\n[DEBUG] =======================================")
print(f"[DEBUG] TIME: {datetime.now().strftime('%H:%M:%S')}")
print(f"[DEBUG] MAIN FILE: {__file__}")
print(f"[DEBUG] CRAWLER FILE: {crawlers.shopee_api_crawler.__file__}")
print(f"[DEBUG] =======================================\n")

def clear_screen():
    # Tạm thời tắt clear để xem log debug
    # os.system('cls' if os.name == 'nt' else 'clear')
    pass

def show_banner():
    print("="*65)
    print(f"      SHOPEE CRAWLER - ASYNC (Build: {datetime.now().strftime('%H:%M:%S')})")
    print("="*65)

def launch_chrome_remote():
    profile_path = os.path.join(os.getcwd(), "output", "chrome_debug_profile")
    os.makedirs(profile_path, exist_ok=True)
    chrome_paths = [
        r"C:\Program Files\Google\Chrome\Application\chrome.exe",
        r"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe",
    ]
    chrome_exe = next((p for p in chrome_paths if os.path.exists(p)), None)
    if not chrome_exe:
        print("\n[❌] Không tìm thấy Chrome. Hãy mở thủ công: --remote-debugging-port=9222")
        return False
    print("\n[*] Đang mở Chrome ...")
    subprocess.Popen(f'"{chrome_exe}" --remote-debugging-port=9222 --user-data-dir="{profile_path}"', shell=True)
    time.sleep(2)
    return True

async def crawl_single_flow():
    url = input("\n Nhập link sản phẩm Shopee: ").strip()
    if not url: return
    ids = parse_shopee_url(url)
    use_remote = input(" Dùng Chrome đang mở (y/n)? [y]: ").strip().lower() != 'n'
    
    crawler = ShopeeApiCrawler()
    try:
        await crawler.start(headless=False, use_remote=use_remote)
        product = await crawler.get_product(ids['shop_id'], ids['item_id'], url)
        if product:
            from crawlers.batch_crawler import save_products_to_json
            saved_file = save_products_to_json([product])
            print(f" ✅ Thành công! File: {os.path.basename(saved_file)}")
            # os.startfile(os.path.dirname(saved_file)) # Tắt cho đỡ phiền khi debug
    except Exception as e:
        print(f"\n[❌] Lỗi tại main: {str(e)}")
    finally:
        await crawler.stop()
    input("\nNhấn Enter để quay lại menu...")

async def crawl_batch_flow():
    file_path = input("\n Nhập tên file (mặc định 'urls.txt'): ").strip() or "urls.txt"
    if not os.path.exists(file_path):
        print(f"\n[❌] Không tìm thấy file: {file_path}")
        return
    use_remote = input("\n Dùng Chrome đang mở (y/n)? [y]: ").strip().lower() != 'n'
    
    crawler = ShopeeApiCrawler()
    try:
        await crawler.start(headless=False, use_remote=use_remote)
        urls = load_urls_from_file(file_path)
        if urls:
            result, saved_file = await crawl_urls(urls, crawler)
            if saved_file:
                print(f"\n HOÀN TẤT! File: {os.path.basename(saved_file)}")
    except Exception as e:
        print(f"\n[❌] Lỗi tại main: {str(e)}")
    finally:
        await crawler.stop()
    input("\nNhấn Enter để quay lại menu...")

def main_menu():
    while True:
        # clear_screen()
        show_banner()
        print("[1] MỞ CHROME & ĐĂNG NHẬP")
        print("[2] CRAWL 1 LINK")
        print("[3] CRAWL DANH SÁCH (urls.txt)")
        print("[4] THOÁT")
        
        choice = input("\n Chọn (1-4): ").strip()
        if choice == '1':
            launch_chrome_remote()
        elif choice == '2':
            asyncio.run(crawl_single_flow())
        elif choice == '3':
            asyncio.run(crawl_batch_flow())
        elif choice == '4':
            break
        time.sleep(0.5)

if __name__ == "__main__":
    main_menu()
