<?php
    include("../../../admincp/config/connection.php");
    session_start();
    $cart_id = $_SESSION['cart_id'];
    if(isset($_GET['id']) && isset($_GET['change'])){
        $id = $_GET['id'];
        if($_GET['change'] == 'plus'){
            $sql_update = "UPDATE tblcart_details SET quantity = quantity + 1 WHERE product_id = '$id' AND cart_id = '$cart_id'";
            $update_result = mysqli_query($mysqli, $sql_update);
        }
        if($_GET['change'] == 'minus') {
            $sql_sl = "SELECT quantity FROM tblcart_details WHERE product_id = '$id' AND cart_id = '$cart_id'";
            $result = mysqli_query($mysqli, $sql_sl);
            $row = mysqli_fetch_assoc($result);
            $current_quantity = $row['quantity'];

            if ($current_quantity >= 1) {
                if ($current_quantity == 1) {
                    $deleteQuery = "DELETE FROM tblcart_details WHERE product_id = '$id' AND cart_id = '$cart_id'";
                    $deleteResult = mysqli_query($mysqli, $deleteQuery);
                } else {
                    $sql_update = "UPDATE tblcart_details SET quantity = quantity - 1 WHERE product_id = '$id' AND cart_id = '$cart_id'";
                    $update_result = mysqli_query($mysqli, $sql_update);
                }
            }
        }
    }
    header('location: ../../../index.php?navigate=cart');
?>