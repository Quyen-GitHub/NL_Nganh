<?php
    include("../../../admincp/config/connection.php");
    session_start();
    $cart_id = $_SESSION['cart_id'];
    if(isset($_GET['id_delete'])){
        $id_delete = $_GET['id_delete'];
        $sql_delete_product = "DELETE FROM tblcart_details WHERE cart_id = $cart_id AND product_id = $id_delete";
        $delete_result = mysqli_query($mysqli, $sql_delete_product);
    }
    if(isset($_GET['delete_all'])){
        $sql_delete_all_products = "DELETE FROM tblcart_details WHERE cart_id = $cart_id";
        $delete_result = mysqli_query($mysqli, $sql_delete_all_products);
    }
    header('location: ../../../index.php?navigate=cart');
?>