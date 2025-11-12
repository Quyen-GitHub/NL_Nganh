<?php
  $user_id = $_SESSION['user_id'];
  $sql_Cus = "SELECT * FROM tbluser WHERE user_id = $user_id";
  $query_Cus = mysqli_query($mysqli, $sql_Cus);
  $row = mysqli_fetch_array($query_Cus);
  $fullname = $row['user_fullname'];
  $email = $row['user_email'];
  $phone = $row['user_phone'];
  $address = $row['user_address'];
?>

<div class="container">
  <div class="card bg-light my-5">
    <article class="card-body mx-auto">
      <h4 class="card-title mt-3 text-center">Edit Profile</h4>
      <form action="pages/main/account/change.php?id=<?= $user_id?>" method="POST">
        <div class="form-group input-group">
          <span class="input-group-text"> <i class="fa fa-user"></i> </span>
          <input required name="fullname" class="form-control" value="<?php echo $fullname ?>">
        </div>
        <div class="form-group input-group">
          <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
          <input required name="email" class="form-control" value="<?php echo  $email?>">
        </div>
        <div class="form-group input-group">
          <span class="input-group-text"> <i class="fa fa-phone"></i> </span>
          <input required name="phone" class="form-control" value="<?php echo $phone ?>">
        </div>
        <div class="form-group input-group">
          <span class="input-group-text"> <i class="fa fa-building"></i> </span>
          <input required name="address" class="form-control" type="text" value="<?php echo  $address?>">
        </div>
        </br>
        <div class="form-group">
          <input type="submit" class="btn btn-primary btn-block" name="change" value="Xác nhận">
        </div>
      </form>
    </article>
  </div>
</div>