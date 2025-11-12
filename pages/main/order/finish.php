<?php
// --- Kết nối CSDL ---
include(__DIR__ . '/../../../admincp/config/connection.php');

// --- Bắt đầu session ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Cấu hình múi giờ ---
date_default_timezone_set('Asia/Ho_Chi_Minh');

// --- Lấy thông tin cơ bản ---
$user_id = $_SESSION['user_id'] ?? 0;
$cart_id = $_SESSION['cart_id'] ?? 0;
$order_receiver = $_SESSION['order_receiver'] ?? '';
$order_address = $_SESSION['order_address'] ?? '';
$order_value = $_SESSION['total_value'] ?? 0;
$order_phone = $_SESSION['order_phone'] ?? '';
$order_notes = $_SESSION['order_notes'] ?? '';
$order_code = $_SESSION['order_code'] ?? rand(1000,9999);
$order_created_time = date("Y-m-d H:i:s");

// ==========================
// --- Xử lý COD --- 
// ==========================
if (isset($_GET['cod_success'])) {
    echo '
    <div class="container min-height-100 text-center mt-5">
        <h3 style="color: green;">Đặt hàng thành công!</h3>
        <p>Đơn hàng của bạn sẽ được thanh toán khi nhận hàng (COD).</p>
        <a class="btn btn-info" href="index.php?navigate=order_history">Xem lịch sử đơn hàng</a>
    </div>';
    exit;
}

// ==========================
// --- Xử lý VNPay --- 
// ==========================
if (isset($_GET['vnp_Amount'])) {
    $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';

    if ($vnp_ResponseCode == '00') {
        // --- Lưu thanh toán VNPay ---
        $Amount = $_GET['vnp_Amount'];
        $BankCode = $_GET['vnp_BankCode'] ?? '';
        $BankTranNo = $_GET['vnp_BankTranNo'] ?? '';
        $CardType = $_GET['vnp_CardType'] ?? '';
        $OrderInfo = $_GET['vnp_OrderInfo'] ?? '';
        $PayDate = $_GET['vnp_PayDate'] ?? '';
        $TmnCode = $_GET['vnp_TmnCode'] ?? '';
        $TransactionNo = $_GET['vnp_TransactionNo'] ?? '';

        mysqli_query($mysqli, "
            INSERT INTO tblvnpay (Amount, BankCode, BankTranNo, CardType, OrderInfo, PayDate, TmnCode, TransactionNo, order_code)
            VALUES ('$Amount','$BankCode','$BankTranNo','$CardType','$OrderInfo','$PayDate','$TmnCode','$TransactionNo','$order_code')
        ");

        // --- Lưu đơn hàng ---
        $sql_insert_order = "
            INSERT INTO tblorder (user_id, order_created_time, order_address, order_value, order_phone, order_receiver, order_payment, order_status, order_code)
            VALUES ('$user_id','$order_created_time','$order_address','$order_value','$order_phone','$order_receiver','vnpay','Đã thanh toán','$order_code')
        ";
        mysqli_query($mysqli, $sql_insert_order);
        $order_id = mysqli_insert_id($mysqli);

        // --- Lưu chi tiết đơn hàng ---
        $query_cart = mysqli_query($mysqli, "SELECT * FROM tblcart_details WHERE cart_id = $cart_id");
        while ($row = mysqli_fetch_assoc($query_cart)) {
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];
            $row_product = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM tblproduct WHERE product_id = $product_id"));
            $purchase_price = $row_product['product_price'] * (100 - $row_product['product_discount']) / 100;

            mysqli_query($mysqli, "
                INSERT INTO tblorder_details (order_id, product_id, quantity, order_code, purchase_price)
                VALUES ('$order_id','$product_id','$quantity','$order_code','$purchase_price')
            ");

            // Giảm số lượng sản phẩm
            mysqli_query($mysqli, "UPDATE tblproduct SET product_quantity = product_quantity - $quantity WHERE product_id = $product_id");
        }

        // --- Xóa giỏ hàng ---
        mysqli_query($mysqli, "DELETE FROM tblcart_details WHERE cart_id = $cart_id");
        unset($_SESSION['total_value']);

        echo '
        <div class="container min-height-100 text-center mt-5">
            <h3 style="color: green;">Thanh toán VNPay thành công!</h3>
            <p>Cảm ơn bạn đã đặt hàng, đơn hàng của bạn đang được xử lý.</p>
            <a class="btn btn-info" href="index.php?navigate=order_history">Xem lịch sử đơn hàng</a>
        </div>';
    } else {
        echo '
        <div class="container min-height-100 text-center mt-5">
            <h3 style="color: red;">Thanh toán VNPay thất bại hoặc bị hủy!</h3>
            <p>Đơn hàng chưa được thanh toán. Hãy chọn phương thức khác.</p>
            <a class="btn btn-secondary" href="index.php?navigate=cart">Quay lại giỏ hàng</a>
        </div>';
    }

    exit;
}

// ==========================
// --- Xử lý MoMo --- 
// ==========================
elseif (isset($_GET['partnerCode'])) {
    $resultCode = $_GET['resultCode'] ?? -1; // 0 = thành công

    if ($resultCode == '0') {
        $partnerCode = $_GET['partnerCode'];
        $orderId = $_GET['orderId'];
        $amount = $_GET['amount'];
        $orderInfo = $_GET['orderInfo'];
        $orderType = $_GET['orderType'] ?? '';
        $transId = $_GET['transId'] ?? '';
        $payType = $_GET['payType'] ?? '';

        // --- Lưu thanh toán MoMo ---
        mysqli_query($mysqli, "
            INSERT INTO tblmomo (PartnerCode, OrderId, Amount, OrderInfo, OrderType, TransId, PayType, order_code)
            VALUES ('$partnerCode','$orderId','$amount','$orderInfo','$orderType','$transId','$payType','$order_code')
        ");

        // --- Lưu đơn hàng ---
        $sql_insert_order = "
            INSERT INTO tblorder (user_id, order_created_time, order_address, order_value, order_phone, order_receiver, order_payment, order_status, order_code)
            VALUES ('$user_id','$order_created_time','$order_address','$order_value','$order_phone','$order_receiver','momo','Đã thanh toán','$order_code')
        ";
        mysqli_query($mysqli, $sql_insert_order);
        $order_id = mysqli_insert_id($mysqli);

        // --- Lưu chi tiết đơn hàng ---
        $query_cart = mysqli_query($mysqli, "SELECT * FROM tblcart_details WHERE cart_id = $cart_id");
        while ($row = mysqli_fetch_assoc($query_cart)) {
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];
            $row_product = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM tblproduct WHERE product_id = $product_id"));
            $purchase_price = $row_product['product_price'] * (100 - $row_product['product_discount']) / 100;

            mysqli_query($mysqli, "
                INSERT INTO tblorder_details (order_id, product_id, quantity, order_code, purchase_price)
                VALUES ('$order_id','$product_id','$quantity','$order_code','$purchase_price')
            ");

            // Giảm số lượng sản phẩm
            mysqli_query($mysqli, "UPDATE tblproduct SET product_quantity = product_quantity - $quantity WHERE product_id = $product_id");
        }

        // --- Xóa giỏ hàng ---
        mysqli_query($mysqli, "DELETE FROM tblcart_details WHERE cart_id = $cart_id");
        unset($_SESSION['total_value']);

        echo '
        <div class="container min-height-100 text-center mt-5">
            <h3 style="color: green;">Thanh toán MoMo thành công!</h3>
            <p>Cảm ơn bạn đã đặt hàng, đơn hàng của bạn đang được xử lý.</p>
            <a class="btn btn-info" href="index.php?navigate=order_history">Xem lịch sử đơn hàng</a>
        </div>';
    } else {
        echo '
        <div class="container min-height-100 text-center mt-5">
            <h3 style="color: red;">Thanh toán MoMo thất bại hoặc bị hủy!</h3>
            <p>Hệ thống chưa ghi nhận thanh toán của bạn. Hãy chọn phương thức khác.</p>
            <a class="btn btn-secondary" href="index.php?navigate=cart">Quay lại giỏ hàng</a>
        </div>';
    }

    exit;
}

// ==========================
// --- Không có dữ liệu thanh toán --- 
// ==========================
else {
    echo '
    <div class="container min-height-100 text-center mt-5">
        <h3 style="color: red;">Không có phản hồi từ hệ thống thanh toán!</h3>
        <p>Vui lòng thử lại giao dịch hoặc chọn phương thức khác.</p>
        <a class="btn btn-secondary" href="index.php?navigate=cart">Quay lại giỏ hàng</a>
    </div>';
}
?>
