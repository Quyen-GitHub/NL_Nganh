<?php
// pages/main/order/momo_qr_payment.php
include("../../../admincp/config/connection.php");
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// config (test sandbox credentials)
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = 'MOMOBKUN20180529';
$accessKey = 'klm05TvNBzhg7h7j';
$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
$orderInfo = "Payment via MoMo QR Code";
$amount = $_POST['total_value'] ?? $_SESSION['total_value'] ?? 0;
if ($amount <= 0) {
    header("Location: ../../../index.php?navigate=confirm_order&error=payment_failed");
    exit;
}
$orderId = time() . rand(1000,9999);
$redirectUrl = "http://localhost/LiteratureLounge/index.php?navigate=finish"; // MoMo will redirect here after payment
$ipnUrl = "http://localhost/LiteratureLounge/index.php?navigate=finish";
$extraData = "";

$requestId = time() . "";
$requestType = "captureWallet";
$rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
$signature = hash_hmac("sha256", $rawHash, $secretKey);

$data = array(
    'partnerCode' => $partnerCode,
    'partnerName' => "Test",
    'storeId' => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => (string)$amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
);

$result = execPostRequest($endpoint, json_encode($data));
$jsonResult = json_decode($result, true);
if (!$jsonResult || !isset($jsonResult['payUrl'])) {
    // MoMo trả lỗi hoặc không có payUrl -> quay về chọn phương thức
    header("Location: ../../../index.php?navigate=confirm_order&error=payment_failed");
    exit;
}

// Lưu order_code vào session để dùng khi redirect về finish.php
$_SESSION['order_code'] = $orderId;
$_SESSION['payment_method'] = 'momo';
$_SESSION['total_value'] = $amount;

header('Location: ' . $jsonResult['payUrl']);
exit;
