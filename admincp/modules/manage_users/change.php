<?php
include("../../config/connection.php");
if (isset($_POST['submit'])) {
    $id = $_GET['id'];
    $user_fullname = $_POST['user_fullname'];
    $user_address = $_POST['user_address'];
    $user_phone = $_POST['user_phone'];
    $sql_change = "UPDATE tbluser SET user_fullname =  '$user_fullname', user_address =  '$user_address', user_phone =  '$user_phone' WHERE user_id = '$id'";
    mysqli_query($mysqli, $sql_change);
    if($_POST['user_password'] != "") {
        $user_password = md5($_POST['user_password']); 
        $sql_update = "UPDATE tbluser SET user_password =  '$user_password' WHERE user_id = '$id'";
        mysqli_query($mysqli, $sql_update);
    }
    }
    header('location:../../index.php?user=user_list');
?>