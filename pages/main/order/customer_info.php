<?php
    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT * FROM tbluser WHERE user_id = $user_id";
    $query_user = mysqli_query($mysqli,$sql_user);
    $row= mysqli_fetch_array($query_user);
    $order_receiver = $row['user_fullname'];
    $order_address = $row['user_address'];
    $order_value = $_SESSION['total_value'];
    $order_phone = $row['user_phone'];
?>
<div class="container my-5 bg-white">
    <form action="index.php?navigate=confirm_order" method="POST">
        <p class="pt-3 text-center">Enter shipping information</p>
        <div class="mt-2">
            <label for="">Receiver: </label>
            <input required class="form-control" type="text" name="order_receiver" value="<?php echo $order_receiver; ?>">
        </div>
        <div class="mt-2">
            <label for="">Address: </label>
            <input required class="form-control" type="text" name="order_address"  value="<?php echo $order_address; ?>">
        </div>
        <div class="mt-2">
            <label for="">Phone number:</label>
            <input required class="form-control" type="text" name="order_phone" value="<?php echo $order_phone; ?>">
        </div>
        <div class="mt-2">
            <label for="">Notes: </label>
            <input class="form-control" type="text" name="order_notes">
        </div>
        <input class="btn btn-success mt-3 mb-3" type="submit" value="Confirm">
    </form>
</div>