<?php
    $sql_CountOrder1=mysqli_query($mysqli,"SELECT * FROM tblorder WHERE order_status= '1'");
    $CountOrder1= mysqli_num_rows($sql_CountOrder1);
    $sql_AllMoney=mysqli_query($mysqli,"SELECT order_value FROM tblorder where order_status='1'");
    $i=0;
    while($allMoney=mysqli_fetch_array($sql_AllMoney)){
        $i+=$allMoney['order_value'];
    }
    $AllMoney=0;
    $AllMoney=$i;
    $sql_CountOrder2 = mysqli_query($mysqli,"SELECT * FROM tblorder WHERE order_status= '0' ");
    $CountOrder2 = mysqli_num_rows($sql_CountOrder2);
    $sql_CountOrder3=mysqli_query($mysqli,"SELECT order_id FROM tblorder WHERE order_status= '2'");
    $CountOrder3 = mysqli_num_rows($sql_CountOrder3);
?>
<div class="container-fluid py-5">  
    <div class="row">
        <div class="col">
                <div class="card text-white bg-primary mb-3" style="max-width: 18rem; height: 120px;">
                <div class="card-header">GROSS REVENUE</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo number_format($AllMoney,0,',','.') ?> VND</h5>
                </div>
             </div>
        </div>
        <div class="col">
            <div class="card text-white bg-success mb-3" style="max-width: 18rem; height: 120px;">
                <div class="card-header">APPOVED ORDERS</div>
                <div class="card-body">
                    <h5 class="card-title"><?php  echo $CountOrder1 ?></h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white bg-danger mb-3" style="max-width: 18rem; height: 120px;">
                <div class="card-header">ORDER PENDING APPROVAL</div>
                <div class="card-body">
                    <h5 class="card-title"><?php  echo $CountOrder2 ?></h5>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white bg-dark mb-3" style="max-width: 18rem; height: 120px;">
                <div class="card-header">CANCELED ORDER</div>
                <div class="card-body">
                    <h5 class="card-title"><?php  echo $CountOrder3 ?></h5>
                </div>
            </div>
    </div>
</div>
    <div class="card">
        <p class="card-header font-weight-bold">
            ORDER PENDING APPROVAL
        </p>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Code</th>
                        <th scope="col">Receiver</th>
                        <th scope="col">Value</th>
                        <th scope="col">Phone number</th>
                        <th scope="col">Address</th>
                        <th scope="col">Created time</th>
                        <th scope="col">Payment method</th>
                        <th scope="col">Approve/Cancel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i=0;
                    while($row=mysqli_fetch_array($sql_CountOrder2)){
                    $i++;?>
                    <tr>
                        <th scope="row"><?php echo $i?></th>
                        <td><?php echo $row['order_code']?></td>
                        <td>
                            <?php echo $row['order_receiver']?>
                        </td>
                        <td><?php echo number_format($row['order_value'],0,',','.')?> VND</td>
                        <td><a href="#"><?php echo $row['order_phone']?></a></td>
                        <td><?php echo $row['order_address']?></td>
                        <td><?php echo $row['order_created_time']?></td>
                        <td><?php echo $row['order_payment']?></td>
                        <td>
                            <a href="modules/manage_orders/process_order.php?process=approve&id=<?php echo $row['order_id']?>" class="btn btn-success btn-sm text-white" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-check-square"></i></a>
                            <a href="modules/manage_orders/process_order.php?process=cancel&id=<?php echo $row['order_id']?>" class="btn btn-danger btn-sm text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>