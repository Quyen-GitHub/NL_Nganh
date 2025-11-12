<?php
    if(isset($_GET['process'])) {
        $query_order = mysqli_query($mysqli,"SELECT * FROM tblorder where order_status = '2' order by order_id DESC");
        $title = "The canceled orders";
    } else {
        $query_order = mysqli_query($mysqli,"SELECT * FROM tblorder where order_status = '1' order by order_id DESC");
        $title = "The approved orders";
    }
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <h5 class="m-0 "><?= $title?></h5>        
        </div>
    <table class="table table-striped table-checkall">
        <thead>
            <tr>
                <th scope="col">STT</th>
                <th scope="col">Order ID</th>
                <th scope="col">User ID</th>
                <th scope="col">Created time</th>
                <th scope="col">Address</th>
                <th scope="col">Value</th>
                <th scope="col">Details</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $sum=0;
                $num=0;
                while($row_order = mysqli_fetch_array($query_order)){
                    $sum+=$row_order['order_value']; 
                    $num++;
            ?>
            <tr>
                <td><?php echo $num ?></td>
                <td><?php echo $row_order['order_id']?></td>
                <td><?php echo $row_order['user_id']?></td>
                <td><?php echo $row_order['order_created_time']?></td>
                <td><?php echo $row_order['order_address']?></td>
                <td><?php echo number_format($row_order['order_value'],0,',','.')?> VND</td>
                <td><a href="?order=order_details&id=<?php echo $row_order['order_id']?>">Details</a></td>
            </tr>
        <?php
        }
         $total_value=$sum;  
        ?>
        <tr>
            <th colspan="5">Number of orders: <?= $num ?></th>
            <th colspan="3">Total value: <?= number_format($total_value,0,',','.') ?> VND</th>
        </tr>
        </tbody>
    </table>
    </div>
    </div>
</div>