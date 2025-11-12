<?php
include("../../../admincp/config/connection.php");
session_start();

// --- 1. Lấy product_id từ URL ---
if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // --- 2. Nếu chưa có cart_id trong session thì tạo mới ---
    if (!isset($_SESSION['cart_id'])) {
        $user_id = $_SESSION['user_id'] ?? 0; // Nếu có đăng nhập thì lấy user_id
        $sql_create_cart = "INSERT INTO tblcart(user_id) VALUES($user_id)";
        if (!mysqli_query($mysqli, $sql_create_cart)) {
            die("Lỗi tạo giỏ hàng: " . mysqli_error($mysqli));
        }
        $_SESSION['cart_id'] = mysqli_insert_id($mysqli);
    }

    $cart_id = $_SESSION['cart_id'];

    // --- 3. Kiểm tra sản phẩm đã có trong giỏ chưa ---
    $sql_check = "SELECT * FROM tblcart_details WHERE cart_id = $cart_id AND product_id = $product_id";
    $result = mysqli_query($mysqli, $sql_check);

    if ($result && mysqli_num_rows($result) > 0) {
        // Nếu có rồi thì tăng số lượng
        $sql_update = "UPDATE tblcart_details 
                       SET quantity = quantity + $quantity 
                       WHERE cart_id = $cart_id AND product_id = $product_id";
        mysqli_query($mysqli, $sql_update);
    } else {
        // Nếu chưa có thì thêm mới
        $sql_insert = "INSERT INTO tblcart_details(cart_id, product_id, quantity) 
                       VALUES($cart_id, $product_id, $quantity)";
        mysqli_query($mysqli, $sql_insert);
    }

    header('location: ../../../index.php?navigate=cart');
}
?>
