from playwright.sync_api import sync_playwright
import sys

def debug_shopee(url):
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36")
        
        print(f"Navigating to {url}...")
        page.goto(url, wait_until="networkidle")
        
        print(f"Page Title: {page.title()}")
        
        state = page.evaluate("window.__INITIAL_STATE__")
        if state:
            print("Found __INITIAL_STATE__")
            product = state.get("product", {}).get("product", {})
            if product:
                print(f"Product Name: {product.get('name')}")
            else:
                print("Product data not in state")
        else:
            print("__INITIAL_STATE__ not found")
            
        page.screenshot(path="shopee_debug.png")
        print("Screenshot saved to shopee_debug.png")
        browser.close()

if __name__ == "__main__":
    url = sys.argv[1] if len(sys.argv) > 1 else "https://shopee.vn/product/89827191/23244410073"
    debug_shopee(url)
