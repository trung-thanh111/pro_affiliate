import json

log_file = r'C:\Users\ASUS\.gemini\antigravity\brain\f9e534df-729b-48f7-aa38-4a0ab7e377fe\.system_generated\logs\overview.txt'
with open(log_file, encoding='utf-8') as f:
    for line in f:
        if 'Tạo lại file shopee_api_crawler.py sạch sẽ' in line:
            obj = json.loads(line.strip())
            code_str = obj['tool_calls'][0]['args']['CodeContent']
            
            # Xử lý double-escaped JSON string
            if code_str.startswith('"'):
                try:
                    code_str = json.loads(code_str)
                except:
                    # Hoặc dùng ast.literal_eval nếu nó là repr
                    import ast
                    code_str = ast.literal_eval(code_str)
                    
            with open('extracted.py', 'w', encoding='utf-8') as out:
                out.write(code_str)
            break
