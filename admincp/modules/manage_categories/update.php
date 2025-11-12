<?php
    include("../../config/connection.php");
    if (isset($_POST['submit']) && isset($_GET['id'])) {
        $category_id = $_GET['id'];
        $category_name = $_POST['category_name'];
        if ($category_name != "") {
            $sql_fix = "UPDATE tblcategory SET category_name = '$category_name' WHERE category_id = $category_id";
            mysqli_query($mysqli, $sql_fix); 
        }
    }
    header('location:../../index.php?cat=cat_list');
?>