<?php
session_start();
include("../../../admincp/config/connection.php");
include("config_vnpay.php"); // file bạn gửi ở trên

// Ép kiểu các biến quan trọng sang số nguyên để tăng cường bảo mật cơ bản
$user_id = (int)($_SESSION['user_id'] ?? 0);
$cart_id = (int)($_SESSION['cart_id'] ?? 0);
$total_value = (float)($_POST['total_value'] ?? 0);

if (!$user_id || !$cart_id || $total_value <= 0) {
    // Nếu chưa đăng nhập, chưa có giỏ hàng, hoặc giá trị đơn hàng không hợp lệ
    header('Location: ../../../index.php?navigate=cart');
    exit;
}

// PHẦN 1: XỬ LÝ THANH TOÁN KHI NHẬN HÀNG (COD)

if (isset($_POST['cod'])) {
    $order_code = 'COD' . time() . rand(100, 999);

    // 💡 SỬA LỖI 1: LẤY THÔNG TIN NGƯỜI NHẬN TỪ $_SESSION (NGUỒN ĐÚNG)
    $receiver = mysqli_real_escape_string($mysqli, $_SESSION['order_receiver'] ?? '');
    $phone = mysqli_real_escape_string($mysqli, $_SESSION['order_phone'] ?? '');
    $address = mysqli_real_escape_string($mysqli, $_SESSION['order_address'] ?? '');
    $notes = mysqli_real_escape_string($mysqli, $_SESSION['order_notes'] ?? '');
    
    // 1. INSERT VÀO TBLORDER (Đơn hàng chính)
    $sql_insert = "
        INSERT INTO tblorder (
            user_id, order_created_time, order_address, order_notes, order_value,
            order_phone, order_status, order_receiver, order_payment, order_code
        ) VALUES (
            '$user_id', NOW(), '$address', '$notes', '$total_value',
            '$phone', 0, '$receiver', 'COD', '$order_code'
        )";

    if (!mysqli_query($mysqli, $sql_insert)) {
        die('Lỗi thêm đơn hàng COD: ' . mysqli_error($mysqli));
    }

    $order_id = mysqli_insert_id($mysqli);

    // 2. INSERT VÀO TBLORDER_DETAILS (Chi tiết đơn hàng)

    $sql_cart_details = "
        SELECT cd.product_id, cd.quantity, p.product_price, p.product_discount
        FROM tblcart_details cd
        JOIN tblproduct p ON cd.product_id = p.product_id
        WHERE cd.cart_id = '$cart_id'";
        
    $query_cart_details = mysqli_query($mysqli, $sql_cart_details);
    
    while ($row = mysqli_fetch_assoc($query_cart_details)) {
        $product_id = (int)$row['product_id'];
        $quantity = (int)$row['quantity'];
        $unit_price = (float)$row['product_price'];
        $discount = (int)$row['product_discount'];
        
        // TÍNH TOÁN GIÁ MUA SAU GIẢM GIÁ
        $purchase_price = $unit_price * (100 - $discount) / 100;

        // Chèn vào tblorder_details với giá mua chính xác
        mysqli_query($mysqli, "
            INSERT INTO tblorder_details (order_id, product_id, quantity, order_code, purchase_price)
            VALUES ('$order_id', '$product_id', '$quantity', '$order_code', '$purchase_price')
        ");

        // Cập nhật số lượng sản phẩm (logic này đã đúng)
        mysqli_query($mysqli, "
            UPDATE tblproduct
            SET product_quantity = product_quantity - $quantity
            WHERE product_id = $product_id
        ");
    }

    // 3. Xóa giỏ hàng và chuyển hướng
    mysqli_query($mysqli, "DELETE FROM tblcart_details WHERE cart_id = '$cart_id'");
    
    // Xóa các session thông tin người nhận không cần thiết
    unset($_SESSION['order_receiver']);
    unset($_SESSION['order_address']);
    unset($_SESSION['order_phone']);
    unset($_SESSION['order_notes']);
    unset($_SESSION['total_value']);

    header("Location: ../../../index.php?navigate=finish&cod_success=1&order_code=$order_code");
    exit;
}

// PHẦN 2: XỬ LÝ THANH TOÁN VNPAY

if (isset($_POST['vnpay'])) {
    // ... (Giữ nguyên logic VNPay của bạn) ...
    $vnp_TxnRef = time(); // Mã đơn hàng duy nhất
    $vnp_OrderInfo = "Thanh toan don hang LiteratureLounge";
    $vnp_OrderType = "billpayment";
    $vnp_Amount = $total_value * 100; // nhân 100 vì VNPay tính đơn vị là đồng *100
    $vnp_Locale = "vn";
    $vnp_BankCode = "";
    $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

    $inputData = array(
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => $vnp_OrderType,
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef,
        "vnp_ExpireDate" => $expire
    );

    // Nếu có bank code thì thêm
    if (isset($vnp_BankCode) && $vnp_BankCode != "") {
        $inputData['vnp_BankCode'] = $vnp_BankCode;
    }

    // Sắp xếp theo key (bắt buộc để hash đúng)
    ksort($inputData);
    $query = "";
    $i = 0;
    $hashdata = "";
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
        $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }

    // Tạo secure hash
    $vnp_Url = $vnp_Url . "?" . $query;
    if (isset($vnp_HashSecret)) {
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
    }

    // Lưu đơn tạm với trạng thái "Processing"
    $sql_insert = "INSERT INTO tblorder (user_id, order_total, order_payment, order_status, vnpay_id, created_at)
                    VALUES ('$user_id', '$total_value', 'VNPAY', 'Processing', '$vnp_TxnRef', NOW())";
    mysqli_query($mysqli, $sql_insert);
    $_SESSION['vnp_order_id'] = mysqli_insert_id($mysqli);

    // Chuyển sang VNPay
    header('Location: ' . $vnp_Url);
    exit;
}
?>