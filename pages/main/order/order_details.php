<?php
    $order_id = isset($_GET['id']) ? $_GET['id'] : '';
    $sql_order = "SELECT order_receiver, order_phone, order_phone, order_created_time,
        order_notes, order_code, order_payment, order_value, order_status 
        from tblorder where order_id = $order_id";
    $query_order = mysqli_query($mysqli, $sql_order);
    $row = mysqli_fetch_assoc($query_order);
    $sql_order_details = "SELECT product_title, tblorder_details.quantity,
    tblorder_details.purchase_price from tblproduct inner join tblorder_details
    on tblorder_details.product_id = tblproduct.product_id
    where tblorder_details.order_id = $order_id";
    $query_order_details = mysqli_query($mysqli, $sql_order_details);
?>
<div class="container mt-5 min-height-100">  
    <table class="table-bordered w-100 bg-white" cellpadding="5px">
        <tr>
            <th colspan="4"><h1 class="text-center">Order details</h1></th>
        </tr>
        <tr>
            <td colspan="2">Receiver: <?= $row['order_receiver']?></td>
            <td colspan="2">Phone number: <?= $row['order_phone']?></td>
        </tr>
        <tr>
            <td colspan="2">Address: <?= $row['order_phone']?></td>
            <td colspan="2">Created time: <?= $row['order_created_time']?></td>
        </tr>
        <tr>
            <td colspan="4">Notes: <?= $row['order_notes']?></td>
        </tr>
        <tr>
            <td colspan="2">Code: <?= $row['order_code']?></td>
            <td colspan="2">Payment method: <?= $row['order_payment']?></td>
        </tr>
        <tr>
            <th>No.</th>
            <th>Title</th>
            <th>Quantity</th>
            <th>Purchase price</th>
        </tr>
        <?php 
        $i=0;
        while($row_details = mysqli_fetch_assoc($query_order_details)){
            $i++;    
        ?>
        <tr>
            <td><?= $i?></td>
            <td><?= $row_details['product_title']?></td>
            <td><?= $row_details['quantity']?></td>
            <td><?= number_format($row_details['purchase_price'],0,',','.') ?> VND/Vol</td>
        </tr>
        <?php }?>
        <tr>
            <th colspan="4">Total value: <?= number_format($row['order_value'],0,',','.') ?> VND</th>
        </tr>
    </table>
    <?php
        if($row['order_status'] == 0)  {
    ?>
    <div class="text-center mt-60">
        <a class="btn btn-danger" href="pages/main/order/cancel.php?id_cancel=<?= $order_id?>">Cancel</a>
    </div>
     <?php } ?>
</div>