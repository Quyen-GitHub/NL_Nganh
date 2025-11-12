<?php
include("../../../admincp/config/connection.php");
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id']: '';
$product_id = isset($_GET['product_id']) ? $_GET['product_id']: '';
$comment_content = $_POST['comment_content'];
$comment_time = date("Y-m-d H:i:s");
if (isset($_POST['comment'])) {
	$sql_add = "INSERT INTO tblcomment(user_id,product_id,comment_content,comment_time) 
	VALUES('".$user_id."','".$product_id."','".$comment_content."','".$comment_time."')";
	mysqli_query($mysqli,$sql_add);
	header("location: ../../../index.php?navigate=product_info&product_id={$_GET['product_id']}");
}
else{
	header("location: ../../../index.php?navigate=productInfo&product_id={$_GET['product_id']}");
}
?>
