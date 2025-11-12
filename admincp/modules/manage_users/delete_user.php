<?php 
    include("../../config/connection.php");
    if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "UPDATE tbluser
    SET user_enabled = 0, user_deleted = 1 where user_id = $user_id";
    mysqli_query($mysqli,$sql);
    header('location:../../index.php?user=user_list');
    }
?>