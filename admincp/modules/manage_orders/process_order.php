<?php 
	include("../../config/connection.php"); 
    $process = $_GET['process'];
	if (isset($_GET['id']) && $process =='cancel') 
    {
        $order_id = $_GET['id'];
        $sql_order = "UPDATE tblorder SET order_status = '2' where order_id = $order_id";
        mysqli_query($mysqli,$sql_order);
        $sql_order = "SELECT * FROM tblorder_details 
        where tblorder_details.order_id = $order_id";
        $query_order = mysqli_query($mysqli, $sql_order);
        while ($row = mysqli_fetch_assoc($query_order)) {
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];
            $sql_update = "UPDATE tblproduct set product_quantity = product_quantity + $quantity 
            where product_id = $product_id";
            $query_update = mysqli_query($mysqli, $sql_update);
        }
	    header('location:../../index.php?order=order_dashboard');
	}
    
	if(isset($_GET['id']) && $process =='approve') 
    {
		$order_id=$_GET['id'];
        $order_status=1;
        $sql_order="UPDATE tblorder SET order_status='".$order_status."' where order_id = $order_id";
        mysqli_query($mysqli,$sql_order);
        header('location: ../../index.php?order=order_dashboard');
	}
?>