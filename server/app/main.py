import os
import uvicorn
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import google.generativeai as genai
from dotenv import load_dotenv
import re
import mysql.connector
from mysql.connector import Error
import uuid

# --- Cấu hình (Giữ nguyên) ---
load_dotenv()
API_KEY = os.getenv("GEMINI_API_KEY")
if not API_KEY:
    raise ValueError("Không tìm thấy GEMINI_API_KEY.")
genai.configure(api_key=API_KEY)

DB_CONFIG = {
    "host": os.getenv("DB_HOST"),
    "user": os.getenv("DB_USER"),
    "password": os.getenv("DB_PASSWORD"),
    "database": os.getenv("DB_NAME"),
}


# --- Tool 1: get_product_info_from_db (Giữ nguyên) ---
def get_product_info_from_db(search_term: str) -> str:
    """
    Truy xuất CSDL của cửa hàng để tìm thông tin về MỘT sách (sản phẩm) CỤ THỂ.
    Sử dụng hàm này khi người dùng hỏi về GIÁ, SỐ LƯỢNG, MÔ TẢ
    của một cuốn sách cụ thể bằng TÊN SÁCH.
    """
    # ... (Code hàm này giữ nguyên) ...
    print(
        f"[Debug] Tool 1 'get_product_info_from_db' được gọi với từ khóa: '{search_term}'"
    )
    TABLE_NAME = "tblproduct"
    PRODUCT_NAME_COL = "product_title"
    PRICE_COL = "product_price"
    QTY_COL = "product_quantity"
    AUTHOR_COL = "product_author"

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        query = f"SELECT {PRODUCT_NAME_COL}, {PRICE_COL}, {QTY_COL}, {AUTHOR_COL} FROM {TABLE_NAME} WHERE {PRODUCT_NAME_COL} LIKE %s LIMIT 3"
        like_pattern = f"%{search_term}%"
        cursor.execute(query, (like_pattern,))
        results = cursor.fetchall()
        cursor.close()
        conn.close()

        if results:
            context_data = "Thông tin từ CSDL: \n"
            for row in results:
                context_data += (
                    f"- Tên: {row[PRODUCT_NAME_COL]}\n"
                    f"  - Tác giả: {row[AUTHOR_COL]}\n"
                    f"  - Giá: {row[PRICE_COL]} VNĐ\n"
                    f"  - Số lượng còn lại: {row[QTY_COL]}\n\n"
                )
            return context_data
        else:
            return f"Thông tin từ CSDL: Không tìm thấy sản phẩm nào có tên giống '{search_term}'."
    except Error as e:
        print(f"[LỖI CSDL 1] {e}")
        return f"Thông tin từ CSDL: Lỗi khi truy vấn. Lỗi: {e}"


# --- Tool 2: get_products_by_category (Giữ nguyên) ---
def get_products_by_category(category_name: str, offset: int = 0) -> str:
    """
    Truy xuất CSDL để tìm các sách (sản phẩm) thuộc một DANH MỤC (category) CỤ THỂ.
    Sử dụng hàm này khi người dùng muốn LIỆT KÊ sách theo THỂ LOẠI.

    Args:
        category_name (str): Tên của danh mục/thể loại cần tìm (ví dụ: 'Novels', 'History Books').
        offset (int, optional): Số lượng sách cần bỏ qua (để xem trang tiếp theo).
                                 Mặc định là 0 (trang 1, 5 cuốn đầu).
                                 Sử dụng 5 để xem trang 2.
    Returns:
        str: Một chuỗi (string) liệt kê 5 sách tìm được.
    """
    # ... (Code hàm này giữ nguyên) ...
    print(
        f"[Debug] Tool 2 'get_products_by_category' được gọi với danh mục: '{category_name}', offset: {offset}"
    )

    TABLE_PRODUCT = "tblproduct"
    TABLE_CATEGORY = "tblcategory"

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)

        query = f"""
            SELECT p.product_title, p.product_author, p.product_price
            FROM {TABLE_PRODUCT} AS p
            JOIN {TABLE_CATEGORY} AS c ON p.category_id = c.category_id
            WHERE c.category_name LIKE %s
            LIMIT 5 OFFSET %s 
        """

        like_pattern = f"%{category_name}%"
        offset_int = int(offset)

        cursor.execute(query, (like_pattern, offset_int))
        results = cursor.fetchall()
        cursor.close()
        conn.close()

        if results:
            context_data = f"Thông tin từ CSDL (danh mục '{category_name}', trang {offset_int//5 + 1}): \n"
            for row in results:
                context_data += (
                    f"- Tên: {row['product_title']}\n"
                    f"  - Tác giả: {row['product_author']}\n"
                    f"  - Giá: {row['product_price']} VNĐ\n\n"
                )
            return context_data
        else:
            if offset_int > 0:
                return (
                    f"Thông tin từ CSDL: Đã hết sách trong danh mục '{category_name}'."
                )
            return f"Thông tin từ CSDL: Không tìm thấy sách nào thuộc danh mục '{category_name}'."
    except Error as e:
        print(f"[LỖI CSDL 2] {e}")
        return f"Thông tin từ CSDL: Lỗi khi truy vấn danh mục. Lỗi: {e}"


# --- Tool 3: list_all_categories (Giữ nguyên) ---
def list_all_categories() -> str:
    """
    Truy xuất CSDL để LIỆT KÊ TẤT CẢ các danh mục (thể loại) sách hiện có.
    Sử dụng hàm này khi người dùng hỏi chung chung như 'có những danh mục nào?',
    'liệt kê các thể loại', 'bạn có loại sách gì?'.
    Hàm này không nhận bất kỳ tham số nào.
    """
    # ... (Code hàm này giữ nguyên) ...
    print("[Debug] Tool 3 'list_all_categories' được gọi.")

    TABLE_CATEGORY = "tblcategory"

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)

        query = f"SELECT category_name FROM {TABLE_CATEGORY} ORDER BY category_name ASC"

        cursor.execute(query)
        results = cursor.fetchall()
        cursor.close()
        conn.close()

        if results:
            category_list = [row["category_name"] for row in results]
            context_data = f"Thông tin từ CSDL: Cửa hàng hiện có {len(category_list)} danh mục: {', '.join(category_list)}."
            return context_data
        else:
            return "Thông tin từ CSDL: Không tìm thấy danh mục nào."
    except Error as e:
        print(f"[LỖI CSDL 3] {e}")
        return f"Thông tin từ CSDL: Lỗi khi truy vấn danh mục. Lỗi: {e}"


def search_books_by_author(author_name: str) -> str:
    """
    Tìm các cuốn sách được viết bởi một TÁC GIẢ cụ thể.
    Dùng khi người dùng hỏi: 'Có sách của tác giả X không?', 'Tìm sách của Mark Twain'.
    """
    print(f"[Debug] Tool 'search_books_by_author' gọi với: {author_name}")

    # Mapping cột từ file SQL
    TABLE_NAME = "tblproduct"
    TITLE_COL = "product_title"
    AUTHOR_COL = "product_author"
    PRICE_COL = "product_price"

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)

        # Tìm kiếm gần đúng (LIKE)
        query = f"SELECT {TITLE_COL}, {PRICE_COL} FROM {TABLE_NAME} WHERE {AUTHOR_COL} LIKE %s LIMIT 5"
        cursor.execute(query, (f"%{author_name}%",))
        results = cursor.fetchall()
        cursor.close()
        conn.close()

        if results:
            context_data = (
                f"Thông tin từ CSDL: Tìm thấy sách của tác giả '{author_name}':\n"
            )
            for row in results:
                context_data += f"- {row[TITLE_COL]} (Giá: {row[PRICE_COL]} VNĐ)\n"
            return context_data
        else:
            return f"Thông tin từ CSDL: Không tìm thấy sách nào của tác giả '{author_name}'."

    except Error as e:
        print(f"[LỖI CSDL AUTHOR] {e}")
        return "Lỗi truy vấn tác giả."


# def check_order_status(order_code: int) -> str:
#     """
#     Kiểm tra trạng thái đơn hàng dựa trên MÃ ĐƠN HÀNG (Order Code).
#     Dùng khi người dùng hỏi: 'Đơn hàng 2133 sao rồi?', 'Kiểm tra đơn 819'.
#     """
#     print(f"[Debug] Tool 'check_order_status' gọi với mã: {order_code}")

#     TABLE_ORDER = "tblorder"

#     # Mapping trạng thái (Giả định dựa trên logic chung, bạn hãy chỉnh theo logic thực tế của shop)
#     STATUS_MAP = {
#         0: "Chờ xử lý",
#         1: "Đang vận chuyển",
#         2: "Đã giao hàng thành công",
#         3: "Đã hủy",
#     }

#     try:
#         conn = mysql.connector.connect(**DB_CONFIG)
#         cursor = conn.cursor(dictionary=True)

#         query = f"""
#             SELECT order_status, order_value, order_address, order_created_time 
#             FROM {TABLE_ORDER} 
#             WHERE order_code = %s
#         """
#         cursor.execute(query, (order_code,))
#         result = cursor.fetchone()
#         cursor.close()
#         conn.close()

#         if result:
#             status_text = STATUS_MAP.get(result["order_status"], "Không xác định")
#             return (
#                 f"Thông tin đơn hàng #{order_code}:\n"
#                 f"- Trạng thái: {status_text}\n"
#                 f"- Giá trị: {result['order_value']} VNĐ\n"
#                 f"- Địa chỉ: {result['order_address']}\n"
#                 f"- Ngày đặt: {result['order_created_time']}"
#             )
#         else:
#             return (
#                 f"Thông tin từ CSDL: Không tìm thấy đơn hàng nào có mã số {order_code}."
#             )

#     except Error as e:
#         return f"Lỗi truy vấn đơn hàng: {e}"
def check_order_status(order_code: str) -> str:
    """
    Kiểm tra trạng thái đơn hàng dựa trên MÃ ĐƠN HÀNG (Order Code) HOẶC ID ĐƠN HÀNG (Order ID).
    """
    print(f"[Debug] Tool 'check_order_status' gọi với mã: {order_code}")

    TABLE_ORDER = "tblorder"

    # --- CẬP NHẬT TRẠNG THÁI MỚI TẠI ĐÂY ---
    STATUS_MAP = {
        0: "Đang chờ xử lý (Pending approval)",
        1: "Đã chấp nhận - Đang vận chuyển (Accepted/Shipping)",
        2: "Đã từ chối (Rejected)",
    }

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)

        query = f"""
            SELECT order_status, order_value, order_address, order_created_time, order_id, order_code
            FROM {TABLE_ORDER} 
            WHERE order_code = %s OR order_id = %s
            LIMIT 1
        """
        
        # Truyền tham số 2 lần cho 2 dấu ?
        cursor.execute(query, (order_code, order_code)) 
        
        result = cursor.fetchone()
        cursor.close()
        conn.close()

        if result:
            status_text = STATUS_MAP.get(result["order_status"], "Trạng thái không xác định")
            
            # Lấy mã hiển thị đẹp nhất
            display_code = result['order_code'] if result['order_code'] != '0' else result['order_id']
            
            return (
                f"Thông tin đơn hàng #{display_code}:\n"
                f"- Trạng thái: {status_text}\n"
                f"- Giá trị: {result['order_value']:,.0f} VNĐ\n"
                f"- Địa chỉ: {result['order_address']}\n"
                f"- Ngày đặt: {result['order_created_time']}"
            )
        else:
            return (
                f"Thông tin từ CSDL: Không tìm thấy đơn hàng nào có mã số hoặc ID là '{order_code}'."
            )

    except Error as e:
        return f"Lỗi truy vấn đơn hàng: {e}"

def get_product_reviews(product_title: str) -> str:
    """
    Lấy các bình luận/đánh giá của khách hàng về một cuốn sách cụ thể.
    Dùng khi người dùng hỏi: 'Sách này có hay không?', 'Mọi người nói gì về cuốn Nhà Giả Kim?'.
    """
    print(f"[Debug] Tool 'get_product_reviews' gọi với sách: {product_title}")

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)

        # Join 3 bảng để lấy: Tên sách -> Comment -> Tên người comment
        query = """
            SELECT u.user_fullname, c.comment_content, c.comment_time
            FROM tblcomment c
            JOIN tblproduct p ON c.product_id = p.product_id
            JOIN tbluser u ON c.user_id = u.user_id
            WHERE p.product_title LIKE %s
            ORDER BY c.comment_time DESC
            LIMIT 3
        """
        cursor.execute(query, (f"%{product_title}%",))
        results = cursor.fetchall()
        cursor.close()
        conn.close()

        if results:
            context_data = f"Đánh giá từ cộng đồng về sách '{product_title}':\n"
            for row in results:
                context_data += f"- {row['user_fullname']}: \"{row['comment_content']}\" ({row['comment_time']})\n"
            return context_data
        else:
            return f"Thông tin từ CSDL: Chưa có đánh giá nào cho cuốn sách '{product_title}'."

    except Error as e:
        return f"Lỗi truy vấn đánh giá: {e}"


# --- [PHẦN 1: MỚI] Tool 4: get_aggregate_price_by_category ---
def get_aggregate_price_by_category(category_name: str, order: str = "highest") -> str:
    """
    Truy xuất CSDL để tìm sách CÓ GIÁ CAO NHẤT hoặc THẤP NHẤT trong một DANH MỤC.
    Sử dụng hàm này khi người dùng hỏi 'sách nào đắt nhất', 'rẻ nhất', 'giá cao nhất'.

    Args:
        category_name (str): Tên của danh mục/thể loại cần tìm (ví dụ: 'Novels').
        order (str, optional): 'highest' (cao nhất) hoặc 'lowest' (thấp nhất).
                               Mặc định là 'highest'.
    Returns:
        str: Một chuỗi (string) thông báo về cuốn sách và giá của nó.
    """
    print(
        f"[Debug] Tool 4 'get_aggregate_price_by_category' được gọi với: {category_name}, order: {order}"
    )

    TABLE_PRODUCT = "tblproduct"
    TABLE_CATEGORY = "tblcategory"

    # Xác định sắp xếp SQL
    sql_order = "DESC"  # Mặc định là 'highest' (cao nhất)
    if order.lower() == "lowest":
        sql_order = "ASC"  # Đổi thành 'lowest' (thấp nhất)

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)

        # Query này JOIN, LỌC, SẮP XẾP, và chỉ lấy 1 KẾT QUẢ
        query = f"""
            SELECT p.product_title, p.product_author, p.product_price
            FROM {TABLE_PRODUCT} AS p
            JOIN {TABLE_CATEGORY} AS c ON p.category_id = c.category_id
            WHERE c.category_name LIKE %s
            ORDER BY p.product_price {sql_order}
            LIMIT 1
        """

        like_pattern = f"%{category_name}%"
        cursor.execute(query, (like_pattern,))
        result = cursor.fetchone()  # Chỉ lấy 1 dòng
        cursor.close()
        conn.close()

        if result:
            order_text = "cao nhất" if sql_order == "DESC" else "thấp nhất"
            context_data = (
                f"Thông tin từ CSDL: Sách có giá {order_text} trong danh mục '{category_name}' là:\n"
                f"- Tên: {result['product_title']}\n"
                f"  - Tác giả: {result['product_author']}\n"
                f"  - Giá: {result['product_price']} VNĐ"
            )
            return context_data
        else:
            return f"Thông tin từ CSDL: Không tìm thấy sách nào thuộc danh mục '{category_name}'."

    except Error as e:
        print(f"[LỖI CSDL 4] {e}")
        return f"Thông tin từ CSDL: Lỗi khi truy vấn giá tổng hợp. Lỗi: {e}"


# --- Cập nhật System Instruction thông minh hơn ---
SYSTEM_INSTRUCTION = """
Bạn là nhân viên bán sách chuyên nghiệp tại 'Literature Lounge'.
Phong cách: Thân thiện, ngắn gọn, hữu ích.

QUY TẮC QUẢN LÝ CÔNG CỤ (TOOLS):
1. Ưu tiên DÙNG TOOL: Nếu người dùng hỏi về sách, giá, hoặc đơn hàng -> Gọi hàm ngay.
2. Xử lý "XEM THÊM" (Phân trang):
   - Hệ thống lịch sử chỉ lưu văn bản, không lưu trạng thái hàm.
   - KHI người dùng hỏi "xem thêm", "còn nữa không", "tiếp đi":
     -> BẠN PHẢI TỰ ĐỌC LẠI tin nhắn gần nhất của chính mình trong lịch sử.
     -> Xác định xem bạn vừa liệt kê danh mục nào (ví dụ: thấy sách 'Brave New World' -> suy ra là 'Novels').
     -> Gọi lại hàm `get_products_by_category` với `category_name` đó và TĂNG `offset` lên (mặc định trang 1 là 0, trang 2 là 5).
     -> TUYỆT ĐỐI KHÔNG hỏi lại "Bạn muốn xem danh mục nào?" nếu lịch sử đã rõ ràng.

3. Xử lý TỪ KHÓA NGẮN:
   - Nếu người dùng chỉ nhắn tên thể loại (ví dụ: "tiểu thuyết", "trinh thám"), ĐỪNG hỏi lại. Hãy coi đó là yêu cầu tìm sách và gọi hàm `get_products_by_category` ngay lập tức.

QUY TẮC QUẢN LÝ ĐƠN HÀNG:
1. Nếu người dùng đưa ra một mã số cụ thể (ví dụ: "kiểm tra đơn 123"): Dùng `check_order_status`.
2. Nếu người dùng hỏi chung chung về đơn của họ (ví dụ: "đơn hàng của tôi đâu", "tôi đã đặt gì", "kiểm tra đơn vừa đặt"): Dùng `get_my_recent_orders`.

DANH SÁCH CÔNG CỤ:
1. `get_product_info_from_db`: Tìm chi tiết 1 cuốn sách theo tên.
2. `get_products_by_category`: Tìm danh sách sách theo thể loại (có phân trang offset).
3. `list_all_categories`: Liệt kê các thể loại.
4. `get_aggregate_price_by_category`: Tìm sách đắt/rẻ nhất.
5. `search_books_by_author`: Tìm sách theo tên tác giả.
6. `check_order_status`: Kiểm tra đơn hàng.
7. `get_product_reviews`: Xem review sách.
8. `get_my_recent_orders`: Tra cứu danh sách đơn hàng của chính người dùng hiện tại (không cần mã).
"""

def list_my_orders_tool(): 
    """Tool wrapper để Gemini gọi khi người dùng muốn xem đơn hàng của chính họ."""
    return "CHECK_MY_ORDERS"

# Cập nhật lại Model với instruction mới
model = genai.GenerativeModel(
    model_name="gemini-2.5-flash",
    system_instruction=SYSTEM_INSTRUCTION,
    tools=[
        get_product_info_from_db,
        get_products_by_category,
        list_all_categories,
        get_aggregate_price_by_category,
        search_books_by_author,
        check_order_status,
        get_product_reviews,
        list_my_orders_tool,
    ],
)


def _ensure_session_exists(session_id: str, user_id: int | None = None):
    """
    Kiểm tra xem session_id đã có trong DB chưa.
    Nếu chưa, tạo mới để tránh lỗi Foreign Key khi lưu tin nhắn.
    """
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()

        # Kiểm tra tồn tại
        check_query = "SELECT 1 FROM tblchat_sessions WHERE session_id = %s"
        cursor.execute(check_query, (session_id,))
        exists = cursor.fetchone()

        if not exists:
            print(f"[INFO] Session {session_id} chưa tồn tại. Đang tạo mới tự động...")
            insert_query = (
                "INSERT INTO tblchat_sessions (session_id, user_id) VALUES (%s, %s)"
            )
            cursor.execute(insert_query, (session_id, user_id))
            conn.commit()

        cursor.close()
        conn.close()
    except Error as e:
        print(f"[LỖI KIỂM TRA SESSION] {e}")


# --- Các hàm CSDL cho Chat (Giữ nguyên) ---
def _rebuild_history_from_db(session_id: str):
    # (Code giữ nguyên)
    history = []
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        query = "SELECT role, content FROM tblchat_messages WHERE session_id = %s ORDER BY timestamp ASC"
        cursor.execute(query, (session_id,))
        results = cursor.fetchall()
        cursor.close()
        conn.close()
        for row in results:
            history.append({"role": row["role"], "parts": [row["content"]]})
        return history
    except Error as e:
        print(f"[LỖI TÁI TẠO LỊCH SỬ] {e}")
        return []


def _save_message_to_db(session_id: str, role: str, content: str):
    # (Code giữ nguyên)
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        query = "INSERT INTO tblchat_messages (session_id, role, content) VALUES (%s, %s, %s)"
        cursor.execute(query, (session_id, role, content))
        conn.commit()
        cursor.close()
        conn.close()
    except Error as e:
        print(f"[LỖI LƯU TIN NHẮN] {e}")

# def get_my_recent_orders(session_id: str) -> str:
#     """
#     Tra cứu danh sách các đơn hàng gần nhất CỦA CHÍNH NGƯỜI DÙNG đang chat.
#     Sử dụng hàm này khi người dùng hỏi: 'đơn hàng của tôi', 'tôi đã mua gì', 'kiểm tra đơn hàng vừa đặt'.
#     Hàm này KHÔNG cần tham số đầu vào từ người dùng.
#     """
#     print(f"[Debug] Tool 'get_my_recent_orders' gọi với session_id: {session_id}")

#     try:
#         conn = mysql.connector.connect(**DB_CONFIG)
#         cursor = conn.cursor(dictionary=True)

#         # 1. Tìm user_id từ session_id
#         sql_get_user = "SELECT user_id FROM tblchat_sessions WHERE session_id = %s"
#         cursor.execute(sql_get_user, (session_id,))
#         user_row = cursor.fetchone()

#         if not user_row or not user_row['user_id']:
#             cursor.close()
#             conn.close()
#             return "Thông báo: Bạn chưa đăng nhập. Vui lòng đăng nhập để xem đơn hàng của mình."

#         user_id = user_row['user_id']

#         # 2. Lấy danh sách đơn hàng của user đó (kết hợp chi tiết sản phẩm nếu cần)
#         # Mapping trạng thái đơn hàng
#         STATUS_CASE = """
#             CASE 
#                 WHEN order_status = 0 THEN 'Chờ xử lý'
#                 WHEN order_status = 1 THEN 'Đang vận chuyển'
#                 WHEN order_status = 2 THEN 'Giao thành công'
#                 ELSE 'Đã hủy'
#             END
#         """

#         query_orders = f"""
#             SELECT order_id, order_code, order_created_time, order_value, {STATUS_CASE} as status_text
#             FROM tblorder
#             WHERE user_id = %s
#             ORDER BY order_created_time DESC
#             LIMIT 5
#         """
#         cursor.execute(query_orders, (user_id,))
#         orders = cursor.fetchall()
        
#         cursor.close()
#         conn.close()

#         if orders:
#             context_data = "Danh sách đơn hàng của bạn:\n"
#             for order in orders:
#                 # Xử lý hiển thị mã đơn hàng (ưu tiên order_code nếu khác 0, nếu không thì dùng order_id)
#                 display_code = order['order_code'] if order['order_code'] != '0' else f"ID_{order['order_id']}"
                
#                 context_data += (
#                     f"- Mã đơn: {display_code}\n"
#                     f"  + Ngày đặt: {order['order_created_time']}\n"
#                     f"  + Tổng tiền: {order['order_value']:,.0f} VNĐ\n"
#                     f"  + Trạng thái: {order['status_text']}\n\n"
#                 )
#             return context_data
#         else:
#             return "Hệ thống: Bạn chưa có đơn hàng nào trong lịch sử."

#     except Error as e:
#         print(f"[LỖI TRA CỨU ĐƠN CỦA TÔI] {e}")
#         return "Hệ thống: Có lỗi khi truy xuất dữ liệu đơn hàng."
def get_my_recent_orders(session_id: str) -> str:
    """
    Tra cứu danh sách các đơn hàng gần nhất CỦA CHÍNH NGƯỜI DÙNG đang chat.
    """
    print(f"[Debug] Tool 'get_my_recent_orders' gọi với session_id: {session_id}")

    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)

        # 1. Tìm user_id từ session_id
        sql_get_user = "SELECT user_id FROM tblchat_sessions WHERE session_id = %s"
        cursor.execute(sql_get_user, (session_id,))
        user_row = cursor.fetchone()

        if not user_row or not user_row['user_id']:
            cursor.close()
            conn.close()
            return "Thông báo: Bạn chưa đăng nhập. Vui lòng đăng nhập để xem đơn hàng của mình."

        user_id = user_row['user_id']

        # 2. Lấy danh sách đơn hàng (CẬP NHẬT CASE SQL TẠI ĐÂY)
        STATUS_CASE = """
            CASE 
                WHEN order_status = 0 THEN 'Đang chờ xử lý'
                WHEN order_status = 1 THEN 'Đã chấp nhận (Đang vận chuyển)'
                WHEN order_status = 2 THEN 'Đã từ chối'
                ELSE 'Trạng thái khác'
            END
        """

        query_orders = f"""
            SELECT order_id, order_code, order_created_time, order_value, {STATUS_CASE} as status_text
            FROM tblorder
            WHERE user_id = %s
            ORDER BY order_created_time DESC
            LIMIT 5
        """
        cursor.execute(query_orders, (user_id,))
        orders = cursor.fetchall()
        
        cursor.close()
        conn.close()

        if orders:
            context_data = "Danh sách đơn hàng của bạn:\n"
            for order in orders:
                display_code = order['order_code'] if order['order_code'] != '0' else f"ID_{order['order_id']}"
                
                context_data += (
                    f"- Mã đơn: {display_code}\n"
                    f"  + Ngày đặt: {order['order_created_time']}\n"
                    f"  + Tổng tiền: {order['order_value']:,.0f} VNĐ\n"
                    f"  + Trạng thái: {order['status_text']}\n\n"
                )
            return context_data
        else:
            return "Hệ thống: Bạn chưa có đơn hàng nào trong lịch sử."

    except Error as e:
        print(f"[LỖI TRA CỨU ĐƠN CỦA TÔI] {e}")
        return "Hệ thống: Có lỗi khi truy xuất dữ liệu đơn hàng."

# --- Khởi tạo FastAPI (Giữ nguyên) ---
app = FastAPI()
origins = ["http://localhost", "http://127.0.0.1"]
app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# --- Pydantic Models (Giữ nguyên) ---
class StartSessionRequest(BaseModel):
    user_id: int | None = None


class StartSessionResponse(BaseModel):
    session_id: str


class ChatRequest(BaseModel):
    session_id: str
    message: str


class ChatResponse(BaseModel):
    response: str


class HistoryMessage(BaseModel):
    role: str
    content: str


# --- API Endpoints (Giữ nguyên) ---
# 1. Cập nhật Pydantic Model
class StartSessionRequest(BaseModel):
    user_id: int | None = None
    client_session_id: str | None = None # [MỚI] Nhận session_id từ localStorage nếu có

# 2. Cập nhật API start_session
@app.post("/start_session", response_model=StartSessionResponse)
async def start_session(request: StartSessionRequest):
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        
        session_id = None

        # --- TRƯỜNG HỢP 1: Đã đăng nhập (Có user_id) ---
        # -> Ưu tiên tìm trong DB theo user_id (Bất chấp localStorage)
        if request.user_id:
            check_query = """
                SELECT session_id FROM tblchat_sessions 
                WHERE user_id = %s 
                ORDER BY created_at DESC LIMIT 1
            """
            cursor.execute(check_query, (request.user_id,))
            existing_session = cursor.fetchone()
            
            if existing_session:
                session_id = existing_session['session_id']
                # Nếu session cũ chưa gắn user_id (do trước đó là khách), giờ gắn vào luôn
                # (Tùy chọn: logic gộp session khách vào tài khoản)
            else:
                # Tạo mới cho User
                session_id = str(uuid.uuid4())
                insert_query = "INSERT INTO tblchat_sessions (session_id, user_id) VALUES (%s, %s)"
                cursor.execute(insert_query, (session_id, request.user_id))
                conn.commit()

        # --- TRƯỜNG HỢP 2: Khách vãng lai (user_id là None) ---
        else:
            # Kiểm tra xem Khách có gửi kèm session_id cũ không?
            if request.client_session_id:
                # Kiểm tra session_id này có tồn tại trong DB không (tránh ID rác)
                check_query = "SELECT session_id FROM tblchat_sessions WHERE session_id = %s"
                cursor.execute(check_query, (request.client_session_id,))
                if cursor.fetchone():
                    session_id = request.client_session_id
                    print(f"[INFO] Khách vãng lai quay lại với session: {session_id}")
            
            # Nếu không có session cũ hoặc session cũ không hợp lệ -> Tạo mới
            if not session_id:
                session_id = str(uuid.uuid4())
                insert_query = "INSERT INTO tblchat_sessions (session_id, user_id) VALUES (%s, NULL)"
                cursor.execute(insert_query, (session_id,))
                conn.commit()
                print(f"[INFO] Tạo session mới cho khách vãng lai: {session_id}")

        cursor.close()
        conn.close()
        return StartSessionResponse(session_id=session_id)

    except Error as e:
        print(f"[LỖI TẠO SESSION] {e}")
        return StartSessionResponse(session_id="")

@app.get("/get_history/{session_id}", response_model=list[HistoryMessage])
async def get_history(session_id: str):
    # (Code giữ nguyên)
    history = []
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        query = "SELECT role, content FROM tblchat_messages WHERE session_id = %s ORDER BY timestamp ASC"
        cursor.execute(query, (session_id,))
        results = cursor.fetchall()
        cursor.close()
        conn.close()
        for row in results:
            history.append(HistoryMessage(role=row["role"], content=row["content"]))
        return history
    except Error as e:
        print(f"[LỖI LẤY LỊCH SỬ] {e}")
        return []


# --- [PHẦN 3: CẬP NHẬT] API Endpoint /chat ---
# Cập nhật vòng lặp 'while' để xử lý CẢ BỐN tool
@app.post("/chat", response_model=ChatResponse)
async def chat_with_bot(request: ChatRequest):
    try:
        user_message = request.message
        session_id = request.session_id

        _ensure_session_exists(session_id)

        print(f"\n[User] (Session: {session_id[:8]}...): {user_message}")

        _save_message_to_db(session_id, "user", user_message)
        history = _rebuild_history_from_db(session_id)
        chat = model.start_chat(history=history[:-1])
        response = chat.send_message(user_message)
        part = response.candidates[0].content.parts[0]

        while part.function_call:
            call = part.function_call
            print(f"[Gemini] Yêu cầu gọi hàm: {call.name}")

            if call.name == "get_product_info_from_db":
                args = call.args
                search_term = args.get("search_term", "")
                db_context_string = get_product_info_from_db(search_term)
                print(f"[Tool 1] Kết quả: {db_context_string[:100]}...")

                response = chat.send_message(
                    {
                        "function_response": {
                            "name": call.name,
                            "response": {"context": db_context_string},
                        }
                    }
                )
                part = response.candidates[0].content.parts[0]

            elif call.name == "get_products_by_category":
                args = call.args
                category_name = args.get("category_name", "")
                offset = args.get("offset", 0)
                offset_int = int(offset)

                db_context_string = get_products_by_category(category_name, offset_int)
                print(f"[Tool 2] Kết quả: {db_context_string[:100]}...")

                response = chat.send_message(
                    {
                        "function_response": {
                            "name": call.name,
                            "response": {"context": db_context_string},
                        }
                    }
                )
                part = response.candidates[0].content.parts[0]

            elif call.name == "list_all_categories":
                db_context_string = list_all_categories()
                print(f"[Tool 3] Kết quả: {db_context_string[:100]}...")

                response = chat.send_message(
                    {
                        "function_response": {
                            "name": call.name,
                            "response": {"context": db_context_string},
                        }
                    }
                )
                part = response.candidates[0].content.parts[0]

            # [THÊM MỚI] Xử lý cho Tool 4
            elif call.name == "get_aggregate_price_by_category":
                args = call.args
                category_name = args.get("category_name", "")
                order = args.get("order", "highest")  # Mặc định là 'highest'

                db_context_string = get_aggregate_price_by_category(
                    category_name, order
                )
                print(f"[Tool 4] Kết quả: {db_context_string[:100]}...")

                response = chat.send_message(
                    {
                        "function_response": {
                            "name": call.name,
                            "response": {"context": db_context_string},
                        }
                    }
                )
                part = response.candidates[0].content.parts[0]

            elif call.name == "search_books_by_author":
                author_name = call.args.get("author_name", "")
                db_result = search_books_by_author(author_name)
                print(f"[Tool 5] Kết quả: {db_result[:100]}...")

                # Gửi kết quả lại cho Gemini
                response = chat.send_message(
                    {
                        "function_response": {
                            "name": call.name,
                            "response": {"context": db_result},
                        }
                    }
                )
                # Cập nhật biến part để vòng lặp tiếp tục (hoặc kết thúc nếu xong)
                part = response.candidates[0].content.parts[0]

            # [Tool 6] Kiểm tra trạng thái đơn hàng
            elif call.name == "check_order_status":
                # Lưu ý: order_code trong DB là số int
                # try:
                #     order_code = int(call.args.get("order_code", 0))
                # except ValueError:
                #     order_code = 0  # Xử lý nếu AI gửi nhầm chuỗi không phải số

                # db_result = check_order_status(order_code)
                # print(f"[Tool 6] Kết quả: {db_result[:100]}...")
                order_input = str(call.args.get("order_code", ""))
                
                db_result = check_order_status(order_input) # Gọi hàm mới cập nhật
                print(f"[Tool 6] Kết quả: {db_result[:100]}...")
                
                response = chat.send_message(
                    {
                        "function_response": {
                            "name": call.name,
                            "response": {"context": db_result},
                        }
                    }
                )
                part = response.candidates[0].content.parts[0]

            # [Tool 7] Xem đánh giá sản phẩm
            elif call.name == "get_product_reviews":
                product_title = call.args.get("product_title", "")
                db_result = get_product_reviews(product_title)
                print(f"[Tool 7] Kết quả: {db_result[:100]}...")

                response = chat.send_message(
                    {
                        "function_response": {
                            "name": call.name,
                            "response": {"context": db_result},
                        }
                    }
                )
                part = response.candidates[0].content.parts[0]
                
            # [Tool 8 - MỚI] Tra cứu đơn hàng của tôi
            elif call.name == "list_my_orders_tool":
                print(f"[Tool 8] Người dùng hỏi về đơn hàng của họ. Session: {session_id}")
                
                # Gọi hàm xử lý thực tế với session_id lấy từ request
                db_result = get_my_recent_orders(session_id)
                print(f"[Tool 8] Kết quả: {db_result[:100]}...")

                response = chat.send_message(
                    {
                        "function_response": {
                            "name": call.name,
                            "response": {"context": db_result},
                        }
                    }
                )
                part = response.candidates[0].content.parts[0]
            else:
                print(f"[WARN] Bot gọi hàm không xác định: {call.name}")
                response = chat.send_message(
                    {
                        "function_response": {
                            "name": call.name,
                            "response": {"context": "Error: Function not found."},
                        }
                    }
                )
                part = response.candidates[0].content.parts[0]

        final_answer = response.text
        print(f"[Gemini] {final_answer}")

        _save_message_to_db(session_id, "model", final_answer)

        return ChatResponse(response=final_answer)

    except Exception as e:
        print(f"[LỖI NGHIÊM TRỌNG TRONG /CHAT] {e}")
        return ChatResponse(
            response=f"Xin lỗi, tôi đang gặp một chút sự cố kỹ thuật. {e}"
        )


# --- Chạy server: py -m app.main ---
if __name__ == "__main__":
    uvicorn.run("app.main:app", host="127.0.0.1", port=8000, reload=True)
