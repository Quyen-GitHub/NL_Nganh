<?php
  include("../../config/connection.php");
  session_start();
  if (isset($_POST['add'])) {
      $category_name=$_POST['category_name'];
      $sql_add = "INSERT INTO tblcategory(category_name) VALUES ('$category_name')";
      mysqli_query($mysqli,$sql_add);
      header('location: ../../index.php?cat=cat_list');
  }
?>