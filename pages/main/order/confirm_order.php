<?php
include(__DIR__ . "/../../../admincp/config/connection.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;
$cart_id = $_SESSION['cart_id'] ?? null;

// Nếu chưa đăng nhập hoặc chưa có giỏ hàng → quay lại giỏ hàng
if (!$user_id || !$cart_id) {
    header('Location: /LiteratureLounge/index.php?navigate=cart');
    exit;
}

// Lấy chi tiết giỏ hàng
$sql_cart_detail = "
    SELECT cd.product_id, cd.quantity, p.product_title, p.product_price, p.product_discount
    FROM tblcart_details cd
    JOIN tblproduct p ON cd.product_id = p.product_id
    WHERE cd.cart_id = " . intval($cart_id);
$query_cart_detail = mysqli_query($mysqli, $sql_cart_detail);

$_SESSION['order_receiver'] = $_POST['order_receiver'] ?? ($_SESSION['order_receiver'] ?? '');
$_SESSION['order_address'] = $_POST['order_address'] ?? ($_SESSION['order_address'] ?? '');
$_SESSION['order_phone'] = $_POST['order_phone'] ?? ($_SESSION['order_phone'] ?? '');
$_SESSION['order_notes'] = $_POST['order_notes'] ?? ($_SESSION['order_notes'] ?? '');

$total_value = 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Xác nhận đơn hàng</title>
  <link rel="stylesheet" href="/LiteratureLounge/assets/bootstrap.min.css">
</head>
<body>
<div class="container py-5">

  <?php if (isset($_GET['error']) && $_GET['error'] === 'payment_failed'): ?>
    <div class="alert alert-danger text-center">
      ❌ Thanh toán thất bại hoặc bị hủy. Vui lòng chọn phương thức khác.
    </div>
  <?php endif; ?>

  <div class="row">
    <!-- Bảng chi tiết đơn hàng -->
    <div class="col-lg-8 mt-5">
      <table class="table table-bordered w-100" cellpadding="5">
        <tr class="text-center bg-light">
          <th colspan="4"><h4>THÔNG TIN ĐƠN HÀNG</h4></th>
        </tr>
        <tr><td colspan="4"><strong>Người nhận:</strong> <?= htmlspecialchars($_SESSION['order_receiver']) ?></td></tr>
        <tr>
          <td colspan="2"><strong>Địa chỉ:</strong> <?= htmlspecialchars($_SESSION['order_address']) ?></td>
          <td colspan="2"><strong>Số điện thoại:</strong> <?= htmlspecialchars($_SESSION['order_phone']) ?></td>
        </tr>
        <tr><td colspan="4"><strong>Ghi chú:</strong> <?= htmlspecialchars($_SESSION['order_notes']) ?></td></tr>

        <tr class="text-center bg-light">
          <th>STT</th><th>Tên sản phẩm</th><th>Số lượng</th><th>Đơn giá</th>
        </tr>
        <?php
        $i = 0;
        mysqli_data_seek($query_cart_detail, 0);
        while ($row = mysqli_fetch_assoc($query_cart_detail)) {
            $i++;
            $price = (int)$row['product_price'] * (100 - (int)$row['product_discount']) / 100;
            $value = (int)$row['quantity'] * $price;
            $total_value += $value;
        ?>
          <tr class="text-center">
            <td><?= $i ?></td>
            <td><?= htmlspecialchars($row['product_title']) ?></td>
            <td><?= intval($row['quantity']) ?></td>
            <td><?= number_format($price, 0, ',', '.') ?> VND</td>
          </tr>
        <?php } ?>
        <tr class="bg-light">
          <th colspan="4" class="text-end">
            Tổng giá trị: <span class="text-danger fw-bold"><?= number_format($total_value, 0, ',', '.') ?> VND</span>
          </th>
        </tr>
      </table>

      <a class="btn btn-danger mt-3" href="/LiteratureLounge/index.php?navigate=cart">← Quay lại giỏ hàng</a>
    </div>

    <!-- Chọn phương thức thanh toán -->
    <div class="col-lg-4 mt-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="text-center mb-3">Phương thức thanh toán</h5>

          <!-- COD + VNPAY -->
          <form method="POST" action="/LiteratureLounge/pages/main/order/process_payment.php">
            <input type="hidden" name="total_value" value="<?= htmlspecialchars($total_value) ?>">
            <input class="btn btn-success mt-2 w-100" type="submit" name="cod" value="Thanh toán khi nhận hàng (COD)">
            <input class="btn btn-primary mt-2 w-100" type="submit" name="vnpay" value="Thanh toán qua VNPay">
          </form>

          <!-- MOMO QR -->
          <form method="POST" action="/LiteratureLounge/pages/main/order/momo_qr_payment.php">
              <input type="hidden" name="total_value" value="<?= htmlspecialchars($total_value) ?>">
              <input class="btn text-light mt-2 w-100" style="background-color: #ae2170;" type="submit" value="Thanh toán MOMO QR">
          </form>

          <!-- MOMO ATM -->
          <form method="POST" action="/LiteratureLounge/pages/main/order/momo_atm_payment.php">
              <input type="hidden" name="total_value" value="<?= htmlspecialchars($total_value) ?>">
              <input class="btn text-light mt-2 w-100" style="background-color: #ae2170;" type="submit" value="Thanh toán MOMO ATM">
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>

<?php
$_SESSION['total_value'] = $total_value;
?>
