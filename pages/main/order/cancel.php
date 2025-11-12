<?php
include("../../../admincp/config/connection.php");
session_start();

if (!isset($_GET['id_cancel'])) {
    header('Location: ../../../index.php?navigate=order_history');
    exit;
}

$order_id = intval($_GET['id_cancel']);
$sql_check = "SELECT order_payment, order_status FROM tblorder WHERE order_id = $order_id LIMIT 1";
$q = mysqli_query($mysqli, $sql_check);
if (!$q) {
    header('Location: ../../../index.php?navigate=order_history');
    exit;
}
$row = mysqli_fetch_assoc($q);
if (!$row) {
    header('Location: ../../../index.php?navigate=order_history');
    exit;
}

// Nếu thanh toán bằng momo hoặc vnpay -> không cho hủy
$payment = strtolower($row['order_payment'] ?? '');
if ($payment === 'momo' || $payment === 'vnpay') {
    echo "<script>alert('Đơn hàng đã thanh toán online (MoMo/VNPay), không thể hủy!'); window.location='../../../index.php?navigate=order_history';</script>";
    exit;
}

// Nếu chưa thanh toán (hoặc COD) -> cho hủy: set order_status = 2 (đã hủy) or text
$sql_cancel = "UPDATE tblorder SET order_status = 2 WHERE order_id = $order_id";
mysqli_query($mysqli, $sql_cancel);

// Trả hàng kho: cộng lại số lượng trong tblproduct
$sql_details = "SELECT product_id, quantity FROM tblorder_details WHERE order_id = $order_id";
$qd = mysqli_query($mysqli, $sql_details);
while ($r = mysqli_fetch_assoc($qd)) {
    $pid = intval($r['product_id']);
    $qty = intval($r['quantity']);
    mysqli_query($mysqli, "UPDATE tblproduct SET product_quantity = product_quantity + $qty WHERE product_id = $pid");
}

header('Location: ../../../index.php?navigate=order_history');
exit;
