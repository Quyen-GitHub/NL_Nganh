<?php
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $sql_getOrder = "
    SELECT o.*, u.user_fullname AS order_receiver
    FROM tblorder o
    JOIN tbluser u ON o.user_id = u.user_id
    WHERE o.user_id = $user_id
    ORDER BY o.order_id DESC
  ";
  $query_getOrder = mysqli_query($mysqli, $sql_getOrder);

  // Gợi ý thêm kiểm tra lỗi cho dễ debug
  if (!$query_getOrder) {
    die('SQL Error: ' . mysqli_error($mysqli));
  }
}
?>

<div class="container min-height-100">
  <div class="row">
    <div class="col-md-12 mt-3">
      <h2 class="text-center">List of orders</h2>

      <?php if (isset($_SESSION['user_id']) && mysqli_num_rows($query_getOrder) > 0) { ?>
        <table cellpadding="5px" class="table-bordered w-100 bg-white">
          <thead>
            <tr class="text-center">
              <th scope="col">No.</th>
              <th scope="col">ID</th>
              <th scope="col">Receiver</th>
              <th scope="col">Created time</th>
              <th scope="col">Value</th>
              <th scope="col">Payment</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $i = 0;
            while ($row_getOrder = mysqli_fetch_array($query_getOrder)) {
              $i++;

              // Trạng thái đơn
              if ($row_getOrder['order_status'] == 0) {
                $status = "Pending approval";
                $style = "text-warning";
              } elseif ($row_getOrder['order_status'] == 1) {
                $status = "Approved";
                $style = "text-success";
              } else {
                $status = "Cancelled";
                $style = "text-danger";
              }

              $payment = strtolower($row_getOrder['order_payment']);
            ?>
              <tr class="text-center">
                <td><?php echo $i; ?></td>
                <td><?php echo $row_getOrder['order_id']; ?></td>
                <td><?php echo htmlspecialchars($row_getOrder['order_receiver']); ?></td>
                <td><?php echo $row_getOrder['order_created_time']; ?></td>
                <td><?php echo number_format($row_getOrder['order_value'], 0, ',', '.'); ?> VND</td>
                <td><?php echo strtoupper($payment); ?></td>
                <td class="<?php echo $style; ?>"><?php echo $status; ?></td>
                <td>
                  <a href="index.php?navigate=order_details&id=<?php echo $row_getOrder['order_id']; ?>">View</a>

                  <?php
                  // Chỉ hiển thị nút hủy nếu đơn COD, chưa duyệt, chưa hủy
                  if ($payment === 'cod' && $row_getOrder['order_status'] == 0) {
                  ?>
                    | <a href="pages/main/handle_order/cancel_order.php?id_cancel=<?php echo $row_getOrder['order_id']; ?>"
                         onclick="return confirm('Are you sure you want to cancel this order?');"
                         class="text-danger">Cancel</a>
                  <?php } ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } else { ?>
        <h4 class="text-center mt-4">No order history</h4>
      <?php } ?>

    </div>
  </div>
</div>
