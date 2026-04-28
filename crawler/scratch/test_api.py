import requests
import json

def test_shopee_api():
    url = "https://shopee.vn/api/v4/item/get?itemid=23244410073&shopid=89827191"
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36",
        "Referer": "https://shopee.vn/",
        "Accept": "application/json",
        "x-requested-with": "XMLHttpRequest"
    }
    
    try:
        # Step 1: Get session cookies from homepage
        session = requests.Session()
        session.get("https://shopee.vn/", headers=headers, timeout=10)
        
        # Step 2: Hit API
        response = session.get(url, headers=headers, timeout=10)
        print(f"Status: {response.status_code}")
        print(f"Content length: {len(response.text)}")
        if response.status_code == 200:
            data = response.json()
            if data.get("data"):
                print("SUCCESS: Data found!")
                print(f"Product Name: {data['data'].get('name')}")
            else:
                print(f"FAILED: No data in response. JSON: {response.text[:200]}")
    except Exception as e:
        print(f"ERROR: {str(e)}")

if __name__ == "__main__":
    test_shopee_api()
