<?php
    $order_id = isset($_GET['id']) ? $_GET['id'] : '';
    $sql_order = "SELECT * from tblorder where order_id = $order_id";
    $query_order = mysqli_query($mysqli, $sql_order);
    $row = mysqli_fetch_assoc($query_order);
    $sql_order_detail = "SELECT product_title, tblorder_details.quantity,
    tblorder_details.purchase_price from tblproduct inner join tblorder_details
    on tblorder_details.product_id = tblproduct.product_id
    where tblorder_details.order_id = $order_id";
    $query_order_detail = mysqli_query($mysqli, $sql_order_detail);
?>

<div id="content" class="container-fluid">
    <div class="card">     
    <table class="table table-bordered table-checkall">
    <tr>
            <th colspan="4"><h1 class="text-center">Order details</h1></th>
        </tr>
        <tr>
            <td colspan="2">Receiver: <?= $row['order_receiver']?></td>
            <td colspan="2">Phone number: <?= $row['order_phone']?></td>
        </tr>
        <tr>
            <td colspan="2">Address: <?= $row['order_address']?></td>
            <td colspan="2">Created time: <?= $row['order_created_time']?></td>
        </tr>
        <tr>
            <td colspan="4">Notes: <?= $row['order_notes']?></td>
        </tr>
        <tr>
            <td colspan="2">Order code: <?= $row['order_code']?></td>
            <td colspan="2">Payment: <?= $row['order_payment']?></td>
        </tr>
        <tr>
            <th>Serial</th>
            <th>Title</th>
            <th>Quantity</th>
            <th>Purchase price</th>
        </tr>
        <?php 
            $i=0;
            while($row_detail = mysqli_fetch_assoc($query_order_detail)){
            $i++;    
        ?>
        <tr>
            <td><?= $i?></td>
            <td><?= $row_detail['product_title']?></td>
            <td><?= $row_detail['quantity']?> Kg</td>
            <td><?= number_format($row_detail['purchase_price'],0,',','.')?> VND/Kg</td>
        </tr>
        <?php }?>
        <tr>
            <th colspan="4">Total payment: <?= number_format($row['order_value'],0,',','.')?> VND</th>
        </tr>
    </table>
        </div>
    </div>
</div>