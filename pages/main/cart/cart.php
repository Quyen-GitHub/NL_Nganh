<?php
  $user_id = $_SESSION['user_id'];
  $sql_cart = "SELECT tblproduct.product_id, tblcart_details.quantity, tblproduct.product_image, tblproduct.product_title, tblproduct.product_price, tblproduct.product_discount
  FROM tblcart INNER JOIN tblcart_details ON tblcart.cart_id = tblcart_details.cart_id
  INNER JOIN tblproduct ON tblcart_details.product_id = tblproduct.product_id
  WHERE tblcart.user_id = $user_id";
  $query_cart = mysqli_query($mysqli, $sql_cart);
?>

<div class="container min-height-100 mb-5">
    <h1 class="text-center">Cart</h1>
    <div>
      <?php
      if (isset($_SESSION['user_id'])) {
        ?>
        <form method="POST" action="index.php?navigate=customer_info">
            <?php
            if (mysqli_num_rows($query_cart) > 0) {
              $i = 0;
              $total_value = 0;
              $total_quantity = 0;
              ?>
            <table class="bg-white table-bordered w-100" cellpadding="5px">           
              <thead>
              <tr class="text-center">
                <th scope="col">No.</th>
                <th scope="col">Title</th>
                <th scope="col">Image</th>
                <th scope="col">Quantity</th>
                <th scope="col">Purchase Price</th>
                <th scope="col">Remove</th>
              </tr>
              </thead>
              <tbody>
                <?php while($row = mysqli_fetch_array($query_cart)) {
                  $i++;
                  ?>
                  <tr class="text-center">
                    <td>
                      <?= $i ?>
                    </td>
                    <td>
                      <?= $row['product_title'] ?>
                    </td>
                    <td><img class="product-img" style="width: 260px" src="./assets/images/products/<?= $row['product_image'] ?>"></td>
                    <td>
                      <a class="text-dark" href="pages/main/cart/change.php?change=plus&id=<?= $row['product_id']?>"><i class="fa fa-plus"></i></a>
                      <?= $row['quantity'] ?>
                      <a class="text-dark" href="pages/main/cart/change.php?change=minus&id=<?= $row['product_id']?>"><i class="fa fa-minus"></i></a>
                    </td>
                    <td>
                      <?= number_format($row['product_price'] *(100 - $row['product_discount'])/ 100,0,',','.') ?> VND/Vol
                    </td>
                    <td>
                      <a class="text-danger" href="pages/main/cart/delete.php?id_delete=<?= $row['product_id'] ?>">Remove</a>
                    </td>
                  </tr>
                  <?php
                $value = (int)$row['quantity'] * (int)$row['product_price'] * (100-$row['product_discount'])/100;
                $quantity = $row['quantity'];
                $total_value += $value;
                $total_quantity += (int)$quantity;
              }
              ?>
              </tbody>
            <tr>
              <th colspan="3">Total value: <?= number_format($total_value,0,',','.') ?> VND</th>
              <th colspan="2">Total quantity: <?= $total_quantity ?></th>
              <td>
                <a class="btn btn-danger w-100" href="pages/main/cart/delete.php?delete_all">Remove all</a>
              </td>              
            </tr>
            <tr>
              <td class="w-100" colspan="7">
                <input type="submit" class="btn btn-success w-100" name='submit' value="Đặt hàng">
              </td>
            </tr>
          </table>
          <?php
            $_SESSION['total_value'] = $total_value;
            $_SESSION['total_quantity'] = $total_quantity;
          } else {
          ?>
          <h5 class="text-center">There are no products in the cart</h5>
          <?php
          }                       
          ?>
      </form>
      <?php } ?>
  </div>  
</div>