<?php 
    include("../../config/connection.php"); 
    if (isset($_POST['submit'])) {
        $product_title = $_POST['product_title'];
        $product_price = $_POST['product_price'];
        $product_quantity = $_POST['product_quantity'];
        $product_description = $_POST['product_description'];
        $imageName = $_FILES['product_image']['name'];
        $imageTemp = $_FILES['product_image']['tmp_name'];
        move_uploaded_file($imageTemp, "../../../assets/images/products/" . $imageName);
        $category_id = $_POST['category'];
        $product_author = $_POST['product_author'];
        $product_discount = $_POST['product_discount'];
        $sql_add = "INSERT INTO tblproduct(category_id,product_author,product_title,product_description,product_price,product_quantity,product_image,product_discount) VALUES('".$category_id."','".$product_author."','".$product_title."','".$product_description."','".$product_price."','".$product_quantity."','".$imageName."','".$product_discount."')";
        mysqli_query($mysqli,$sql_add);
        }
    header('location: ../../index.php?product=product_list');
?>