import requests
import time
import json
import random
import threading
from concurrent.futures import ThreadPoolExecutor

# --- CONFIGURATION ---
BASE_URL = "https://minsmm.net"  # Thay báº±ng domain tháº­t cá»§a báº¡n
API_ORDERS_URL = f"{BASE_URL}/api/tools/orders/manual"
API_UPDATE_ORDER_URL = f"{BASE_URL}/api/tools/orders/update"
API_TOKENS_URL = f"{BASE_URL}/api/tools/facebook/tokens"
CHECK_COUNT_API = "https://smm79.com/function/getCountReaction.php?api_token=KDT0a50dd2b879a75e42c960bdfa"
RESOLVE_FB_ID_API = "https://id.traodoisub.com/api.php"
MAX_WORKERS = 10000
POLL_INTERVAL = 10
TARGET_SERVICE_ID = 6801

def _normalize_uid(uid):
    if uid is None:
        return None
    uid_str = str(uid).strip()
    if uid_str.isdigit():
        return uid_str
    digits = ''.join(ch for ch in uid_str if ch.isdigit())
    return digits if digits else None

def get_live_tokens(limit=50):
    try:
        response = requests.get(f"{API_TOKENS_URL}?limit={limit}")
        if response.status_code == 200:
            data = response.json()
            rows = data.get('data', [])
            unique_by_uid = {}
            no_uid_accounts = []
            for item in rows:
                token = item.get('data')
                if not token:
                    continue
                uid = _normalize_uid(item.get('uid'))
                account = {
                    'uid': uid,
                    'token': token,
                    'name': item.get('name', '')
                }
                if uid:
                    # Giữ bản ghi mới nhất theo thứ tự API trả về
                    if uid not in unique_by_uid:
                        unique_by_uid[uid] = account
                else:
                    no_uid_accounts.append(account)
            return list(unique_by_uid.values()) + no_uid_accounts
    except Exception as e:
        print(f"[!] Error fetching tokens: {e}")
    return []

def get_pending_orders():
    try:
        response = requests.get(f"{API_ORDERS_URL}")
        if response.status_code == 200:
            return response.json().get('data', [])
    except Exception as e:
        print(f"[!] Error fetching orders: {e}")
    return []

def update_order_status(order_id, status, remains=0, start_count=None, note=""):
    try:
        payload = {
            "id": order_id,
            "status": status,
            "remains": remains,
            "note": note
        }
        if start_count is not None:
            payload["start_count"] = start_count
            
        requests.post(API_UPDATE_ORDER_URL, data=payload)
    except Exception as e:
        print(f"[!] Error updating order {order_id}: {e}")

def get_post_comment_count(post_id):
    try:
        url = f"{CHECK_COUNT_API}&post_id={post_id}"
        response = requests.get(url, timeout=10)
        if response.status_code == 200:
            data = response.json()
            return data.get('comments', {}).get('count', 0)
    except Exception as e:
        print(f"[!] Error checking start count for {post_id}: {e}")
    return 0

def resolve_facebook_post_id(link_or_id):
    value = str(link_or_id).strip()
    if value.isdigit():
        return value

    # Fallback local parse first (nhanh, không cần request ngoài)
    local_id = value.split('/')[-1].split('?')[0] if '/' in value else value
    if str(local_id).isdigit():
        return str(local_id)

    try:
        headers = {
            "accept": "*/*",
            "content-type": "application/x-www-form-urlencoded",
            "origin": "https://id.traodoisub.com",
            "referer": "https://id.traodoisub.com/",
            "user-agent": "Mozilla/5.0"
        }
        res = requests.post(
            RESOLVE_FB_ID_API,
            data={"link": value},
            headers=headers,
            timeout=15
        )
        text = (res.text or "").strip()
        try:
            payload = res.json()
        except Exception:
            payload = {}

        # Thử các key phổ biến trước
        candidates = []
        if isinstance(payload, dict):
            for key in ("id", "post_id", "uid", "data", "result", "message"):
                val = payload.get(key)
                if isinstance(val, (str, int)):
                    candidates.append(str(val))

        # Thêm raw text để regex bắt số nếu API trả plain text
        candidates.append(text)

        for c in candidates:
            digits = ''.join(ch for ch in str(c) if ch.isdigit())
            if digits:
                return digits
    except Exception as e:
        print(f"[!] Resolve post id failed: {e}")

    return local_id

def post_comment(token, post_id, message):
    url = f"https://graph.facebook.com/{post_id}/comments"
    params = {
        "message": message,
        "access_token": token
    }
    try:
        res = requests.post(url, data=params, timeout=10)
        return res.status_code == 200, res.text
    except:
        return False, "Connection Error"

def process_order(order):
    order_id = order['id']
    post_link = order['link'] # CÃ³ thá»ƒ lÃ  link hoáº·c ID
    
    # Convert link -> post_id số (ưu tiên API traodoisub, fallback local)
    post_id = resolve_facebook_post_id(post_link)
    
    # Láº¥y danh sÃ¡ch comment tá»« input_data
    input_data = order.get('input_data', {})
    if not isinstance(input_data, dict):
        try:
            input_data = json.loads(input_data)
        except:
            input_data = {}

    comments_raw = input_data.get('comments', "")
    quantity = int(order.get('quantity', 10))
    
    if isinstance(comments_raw, str):
        comments = [c.strip() for c in comments_raw.split('\n') if c.strip()]
    elif isinstance(comments_raw, list):
        comments = comments_raw
    else:
        comments = []

    default_comments = [
        "Tuyệt vời quá!", "Dịch vụ rất tốt", "Sẽ ủng hộ tiếp", "Rất hài lòng",
        "Đỉnh cao luôn", "Quá chất lượng", "10 điểm cho chất lượng", "Sản phẩm tốt quá",
        "Cảm ơn shop nhé", "Uy tín thực sự", "Nhanh và tốt", "Chất lượng lắm",
        "Đáng đồng tiền bát gạo", "Hài lòng 100%", "Cảm ơn rất nhiều", "Tuyệt vời"
    ]

    # Nếu không có comment, dùng fallback ngẫu nhiên
    if not comments:
        print(f"[*] Order {order_id} no comments found. Using random fallback.")
        comments = [random.choice(default_comments) for _ in range(quantity)]
    elif len(comments) < quantity:
        # Nếu comments ít hơn quantity, lặp để chạy đủ số lượng
        print(f"[*] Order {order_id} has {len(comments)} comments, expanding to quantity={quantity}.")
        base_comments = comments[:]
        while len(comments) < quantity:
            comments.append(random.choice(base_comments))
    elif len(comments) > quantity:
        # Nếu comments nhiều hơn quantity, cắt đúng theo đơn
        comments = comments[:quantity]

    # Trộn ngẫu nhiên danh sách comment
    random.shuffle(comments)

    print(f"[*] Processing Order {order_id} | Post: {post_id} | Total: {len(comments)}")
    
    # Kiá»ƒm tra start_count: Náº¿u Ä‘Ã£ cÃ³ rá»“i thÃ¬ khÃ´ng check láº¡i (giá»¯ nguyÃªn gá»‘c)
    start_count = order.get('start_count')
    if not start_count or start_count == 0:
        start_count = get_post_comment_count(post_id)
        print(f"  [>] Start count (Initial): {start_count}")
    else:
        print(f"  [>] Start count (Resumed): {start_count}")
    
    # Báº¯t Ä‘áº§u xá»­ lÃ½: Set status sang Processing vÃ  khá»Ÿi táº¡o remains = tá»•ng sá»‘ lÆ°á»£ng
    update_order_status(order_id, "Processing", start_count=start_count, remains=len(comments))

    accounts = get_live_tokens(limit=1000)
    if not accounts:
        print("[!] No live tokens available. Waiting...")
        return

    success_count = 0
    fail_count = 0
    # Pool account theo vòng để giảm trùng UID liên tiếp
    token_pool = accounts[:]
    random.shuffle(token_pool)
    
    # Chia Ä‘á»u comment cho cÃ¡c token
    for i, comment in enumerate(comments):
        # Háº¿t 1 vÃ²ng token -> nạp vòng mới từ token còn sống
        if not token_pool:
            if not accounts:
                print("[!] All tokens are dead. Attempting to fetch new tokens...")
                accounts = get_live_tokens(limit=1000)
                if not accounts:
                    print("[!] Still no tokens. Pausing order.")
                    break
            token_pool = accounts[:]
            random.shuffle(token_pool)

        account = token_pool.pop()
        token = account['token']
        uid = account.get('uid') or "unknown"
        ok, msg = post_comment(token, post_id, comment)
        
        if ok:
            success_count += 1
            print(f"  [{success_count}/{len(comments)}] Success UID {uid}: {comment[:20]}...")
        else:
            fail_count += 1
            print(f"  [x] Failed UID {uid}: {msg}")
            # Náº¿u token die, xÃ³a khá»i danh sách sống và pool hiện tại
            if "access token" in msg.lower() or "error" in msg.lower() or "abusive" in msg.lower():
                accounts = [a for a in accounts if a.get('token') != token]
                token_pool = [a for a in token_pool if a.get('token') != token]
        
        # Cáº­p nháº­t tiáº¿n Ä‘á»™ lÃªn web má»—i 5 cmt thÃ nh cÃ´ng cho "real-time" hÆ¡n
        if success_count > 0 and success_count % 5 == 0:
            current_remains = len(comments) - success_count
            update_order_status(order_id, "Processing", remains=current_remains, note=f"Äang cháº¡y: {success_count}/{len(comments)}")

        # Nghá»‰ nháº¹ trÃ¡nh spam
        time.sleep(0.2)

    final_status = "Completed" if success_count > 0 else "Canceled"
    remains = len(comments) - success_count
    note = f"ThÃ nh cÃ´ng: {success_count}, Tháº¥t báº¡i: {fail_count}"
    
    update_order_status(order_id, final_status, remains=remains, note=note)
    print(f"[V] Finished Order {order_id}: {note}")

def main():
    print(f"=== FB AUTO COMMENT BUFF SYSTEM STARTED (Service ID: {TARGET_SERVICE_ID}) ===")
    while True:
        all_orders = get_pending_orders()
        # Lá»c chá»‰ láº¥y Ä‘Æ¡n hÃ ng cá»§a service mong muá»‘n
        orders = [o for o in all_orders if o.get('service_id') == TARGET_SERVICE_ID]
        
        if orders:
            print(f"[*] Found {len(orders)} pending orders for Service {TARGET_SERVICE_ID}.")
            for order in orders:
                process_order(order)
        else:
            # print("[.] No pending orders. Sleeping...")
            pass
            
        time.sleep(POLL_INTERVAL)

if __name__ == "__main__":
    main()
