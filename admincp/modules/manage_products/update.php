<?php
    include("../../config/connection.php"); 
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $product_title = $_POST['product_title'];
        $product_description = $_POST['product_description'];
        $product_quantity = $_POST['product_quantity'];
        $product_price = $_POST['product_price'];
        $category_id = $_POST['category'];
        $product_author = $_POST['product_author'];
        if ($_FILES['product_image']['name'] != ""){
            $query_select_image = "SELECT product_image FROM tblproduct WHERE product_id  = $id";
            $result_select_image = mysqli_query($mysqli, $query_select_image);
            $row_select_image = mysqli_fetch_assoc($result_select_image);
            $imageToDelete = $row_select_image['product_image'];
            unlink("../../../assets/images/products/".$imageToDelete);
            $imageName = $_FILES['product_image']['name'];
            $imageTemp = $_FILES['product_image']['tmp_name'];
            move_uploaded_file($imageTemp, "../../../assets/images/products/" . $imageName);
            $sql_update_anh = "UPDATE tblproduct SET product_image = '$imageName' WHERE product_id = '$_GET[id]' ";
            mysqli_query($mysqli, $sql_update_anh);
        }
        $product_discount = $_POST['product_discount'];
        $sql_fix = "UPDATE tblproduct SET category_id =  '$category_id', product_author = '" . $product_author . "', product_title = '" . $product_title . "', product_description = '" . $product_description . "', product_quantity = '$product_quantity', 
        product_price = '" . $product_price . "', product_discount = '$product_discount' WHERE product_id = '$_GET[id]' ";
        mysqli_query($mysqli, $sql_fix);
    }
    header('location: ../../index.php?product=product_list');
?>