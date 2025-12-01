# LITERATURE LOUNGE - HỆ THỐNG CỬA HÀNG SÁCH TRỰC TUYẾN

## Giới Thiệu Chung

**Literature Lounge** là một hệ thống thương mại điện tử (E-commerce) chuyên về sách, được xây dựng theo mô hình kiến trúc lai, kết hợp giữa Web Client truyền thống (dùng PHP) và dịch vụ API thông minh (dùng Python/FastAPI) để cung cấp tính năng Chatbot tư vấn và tra cứu thông tin sách.

Hệ thống hỗ trợ đầy đủ các chức năng mua sắm, quản lý đơn hàng cho người dùng cuối và một bảng điều khiển Admin (AdminCP) mạnh mẽ để quản lý toàn bộ nghiệp vụ cửa hàng.

## Công Nghệ Sử Dụng

| Thành phần | Công nghệ chính | Chức năng |
| :--- | :--- | :--- |
| **Web Client/Core Logic** | PHP, MySQL | Giao diện người dùng, xử lý logic nghiệp vụ (CRUD, Đặt hàng, Thanh toán). |
| **Database** | MySQL (Database: `literaturelounge_data`) | Lưu trữ dữ liệu sản phẩm, đơn hàng, người dùng, v.v. |
| **Thanh toán** | VNPay, MoMo (QR & ATM), COD | Tích hợp nhiều phương thức thanh toán. |
| **Chatbot AI Service** | Python (FastAPI, Gemini API) | Cung cấp các công cụ tra cứu thông tin sách, tác giả, giá, và trạng thái đơn hàng. |

## Tính Năng Nổi Bật

### I. Frontend (Giao diện Khách hàng)

  * **Quản lý Tài khoản**:
      * Đăng ký và Đăng nhập bằng tên đăng nhập/mật khẩu (mật khẩu được mã hóa MD5).
      * Xem/Chỉnh sửa Hồ sơ cá nhân và Đổi mật khẩu.
  * **Sản phẩm & Tương tác:**
      * Hiển thị danh sách sản phẩm theo danh mục và phân trang.
      * Xem chi tiết sản phẩm, giá, tác giả và thêm vào giỏ hàng.
      * Chức năng tìm kiếm sản phẩm theo tiêu đề hoặc tác giả.
      * Gửi bình luận/đánh giá cho sản phẩm.
  * **Quy trình Đặt hàng:**
      * Quản lý Giỏ hàng (thêm, xóa, tăng/giảm số lượng).
      * Xác nhận thông tin giao hàng (người nhận, địa chỉ, SĐT).
      * Lựa chọn và xử lý thanh toán: COD, VNPay, MoMo (QR/ATM).
      * Xem Lịch sử Đơn hàng và chi tiết đơn hàng.
      * Hỗ trợ hủy đơn hàng (áp dụng cho đơn COD chưa được duyệt).

### II. Admin Panel (AdminCP)

  * **Quản lý Danh mục:** Thêm, Sửa, Xóa danh mục (có kiểm tra sản phẩm liên quan trước khi xóa).
  * **Quản lý Sản phẩm:** Thêm, Cập nhật thông tin chi tiết (tiêu đề, giá, số lượng, mô tả, hình ảnh, tác giả, giảm giá).
  * **Quản lý Đơn hàng:**
      * Xem Dashboard tổng quan (Tổng doanh thu, đơn chờ duyệt, đơn đã duyệt, đơn đã hủy).
      * Duyệt (`order_status = 1`) và Hủy (`order_status = 2`) đơn hàng, đồng thời hoàn trả số lượng sản phẩm vào kho.
      * Xem chi tiết từng đơn hàng.
  * **Quản lý Người dùng & Bình luận:**
      * Liệt kê danh sách người dùng và cho phép sửa thông tin, vô hiệu hóa (xóa mềm) tài khoản.
      * Quản lý và xóa bình luận của khách hàng.
  * **Thống kê:** Hiển thị biểu đồ thống kê doanh thu theo tuần (7 ngày), tháng (30 ngày) và năm (365 ngày).

## 📂 Cấu Trúc Thư Mục Chính

```
.
├── admincp/ 
│   ├── config/ (Kết nối CSDL)
│   ├── css/
│   ├── js/ (Logic cho AdminCP, Charts)
│   ├── modules/ (Các module quản lý: categories, products, orders, users)
│   ├── index.php (Trang điều hướng chính của Admin)
│   └── login.php (Trang đăng nhập Admin)
├── assets/ 
│   ├── bootstrap/
│   └── images/ (Lưu trữ ảnh sản phẩm, banner)
├── pages/ 
│   ├── main/ (Các trang giao diện người dùng chính)
│   │   ├── account/ (Đăng nhập, Đăng ký, Profile)
│   │   ├── cart/ (Thêm, xóa, thay đổi giỏ hàng)
│   │   ├── order/ (Quy trình đặt hàng, thanh toán, lịch sử)
│   │   └── product/ (Hiển thị, tìm kiếm, chi tiết sản phẩm)
│   └── menu.php, footer.php, main.php (Khung sườn giao diện)
├── main.py (Dịch vụ FastAPI/Python Chatbot)
└── index.php (Trang chủ và điều hướng Frontend)
```