<div>
  <?php
    if(isset($_GET['navigate'])){
      $temp = $_GET['navigate'];
    } else {
      $temp = '';
    }
    
    if($temp == "login") {
      include("main/account/login.php");
    } else if($temp == "signup") {
      include("main/account/register.php");
    } else if($temp == "show_products") {
      include("main/product/show_products.php");
    } else if($temp == "search") {
      include("main/product/search_results.php");
    } else if($temp == "profile") {
      include("main/account/profile.php");
    } else if($temp == "change_password") {
      include("main/account/change_password.php");
    } else if($temp == "change_profile") {
      include("main/account/change_profile.php");
    } else if($temp == "category") {
      include("main/product/category.php");
    } else if($temp == "product_info") {
      include("main/product/product_info.php");
    } else if($temp == "cart") {
      include("main/cart/cart.php");
    } else if($temp == "customer_info") {
      include("main/order/customer_info.php");
    } else if($temp == "confirm_order") {
      include("main/order/confirm_order.php");
    } else if($temp == "order_history") {
      include("main/order/order_history.php");
    } else if($temp == "order_details") {
      include("main/order/order_details.php");
    } else if($temp == "finish") {
      include("main/order/finish.php");
    } else {
      include("main/home.php");
    }
  ?>
</div>
