<?php
session_start();
include("../../../admincp/config/connection.php");
include("config_vnpay.php"); // file bạn gửi ở trên

$user_id = $_SESSION['user_id'] ?? null;
$cart_id = $_SESSION['cart_id'] ?? null;
$total_value = $_POST['total_value'] ?? 0;

if (!$user_id || !$cart_id) {
    header('Location: ../../../index.php?navigate=cart');
    exit;
}

// Nếu chọn COD
if (isset($_POST['cod'])) {
    $order_code = 'COD' . time() . rand(100, 999);

    // Lấy thông tin người nhận từ form
    $receiver = mysqli_real_escape_string($mysqli, $_POST['receiver'] ?? '');
    $phone = mysqli_real_escape_string($mysqli, $_POST['phone'] ?? '');
    $address = mysqli_real_escape_string($mysqli, $_POST['address'] ?? '');
    $notes = mysqli_real_escape_string($mysqli, $_POST['notes'] ?? '');

    $sql_insert = "
        INSERT INTO tblorder (
            user_id,
            order_created_time,
            order_address,
            order_notes,
            order_value,
            order_phone,
            order_status,
            order_receiver,
            order_payment,
            order_code
        ) VALUES (
            '$user_id',
            NOW(),
            '$address',
            '$notes',
            '$total_value',
            '$phone',
            0,
            '$receiver',
            'COD',
            '$order_code'
        )";

    if (!mysqli_query($mysqli, $sql_insert)) {
        die('Lỗi thêm đơn hàng COD: ' . mysqli_error($mysqli));
    }

    $order_id = mysqli_insert_id($mysqli);

    // --- Lưu chi tiết đơn hàng ---
    $sql_cart = "SELECT * FROM tblcart_details WHERE cart_id = '$cart_id'";
    $query_cart = mysqli_query($mysqli, $sql_cart);
    while ($row = mysqli_fetch_assoc($query_cart)) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $price = $row['price'];

        mysqli_query($mysqli, "
            INSERT INTO tblorder_details (order_id, product_id, quantity, order_code, purchase_price)
            VALUES ('$order_id', '$product_id', '$quantity', '$order_code', '$price')
        ");

        mysqli_query($mysqli, "
            UPDATE tblproduct
            SET product_quantity = product_quantity - $quantity
            WHERE product_id = $product_id
        ");
    }

    // Xóa giỏ hàng
    mysqli_query($mysqli, "DELETE FROM tblcart_details WHERE cart_id = '$cart_id'");

    unset($_SESSION['total_value']);

    header("Location: ../../../index.php?navigate=finish&cod_success=1&order_code=$order_code");
    exit;
}

// Nếu chọn VNPay
if (isset($_POST['vnpay'])) {
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
