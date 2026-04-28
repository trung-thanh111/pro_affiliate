import os
import time
from playwright.sync_api import sync_playwright
from config import OUTPUT_DIR

def login():
    user_data_dir = os.path.join(OUTPUT_DIR, "shopee_profile")
    if not os.path.exists(user_data_dir):
        os.makedirs(user_data_dir, exist_ok=True)
    
    print(f"[*] Đang mở trình duyệt: {user_data_dir}")
    print("[!] Vui lòng đăng nhập vào Shopee:.")
    print("[!] Sau khi đăng nhập xong, hãy ĐÓNG TRÌNH DUYỆT để lưu session.")

    with sync_playwright() as p:
        # Sử dụng cùng User-Agent Desktop như Crawler
        user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36"
        
        browser_context = p.chromium.launch_persistent_context(
            user_data_dir,
            headless=False,
            channel="chrome",
            user_agent=user_agent,
            viewport={'width': 1366, 'height': 768},
            ignore_default_args=["--enable-automation"],
            args=['--disable-blink-features=AutomationControlled']
        )
        
        page = browser_context.pages[0]
        # Không dùng stealth_sync nữa vì đôi khi nó lại bị bắt bài
        
        print("[*] Đang vào Shopee. Nếu vẫn bị 'word word word', hãy thử đổi sang mạng 4G.")
        page.goto("https://shopee.vn", wait_until="networkidle", timeout=60000)
        
        # Giữ trình duyệt mở cho đến khi bạn đóng thủ công
        try:
            while len(browser_context.pages) > 0:
                time.sleep(1)
        except:
            pass
        
        print("[+] Trình duyệt đã đóng. Session đã được lưu.")

if __name__ == "__main__":
    login()
