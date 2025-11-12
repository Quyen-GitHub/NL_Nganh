<?php
  $user_id = $_SESSION['user_id'];
  $sql_user = "SELECT * FROM tbluser WHERE user_id = $user_id";
  $query_user = mysqli_query($mysqli, $sql_user);
  $row = mysqli_fetch_array($query_user);
?>
<div class="container min-height-100">
  <div class="card bg-light mt-5">
    <article class="card-body mx-auto">
      <h4 class="card-title mt-3 text-center">My Profile</h4>
      <form action="" method="POST" >
        <div class="form-group input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"> <i class="fa fa-user-circle"></i> </span>
          </div>
          <input readonly name="fullname" class="form-control" value="<?php echo $row['user_fullname'] ?>">
        </div>
        <div class="form-group input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
          </div>
          <input readonly name="email" class="form-control" value="<?php echo $row['user_email'] ?>">
        </div>
        <div class="form-group input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"> <i class="fa fa-phone"></i> </span>
          </div>
          <input readonly name="phone" class="form-control" value="<?php echo $row['user_phone'] ?>">
        </div>
        <div class="form-group input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"> <i class="fa fa-home"></i> </span>
          </div>
          <input readonly name="address" class="form-control"  type="text" value="<?php echo $row['user_address'] ?>">
        </div>
        <p class="text-center">
          <a class="btn btn-outline-primary" href="index.php?navigate=change_password">Change your password</a>
        </p>
        <p class="text-center">
          <a class="btn btn-outline-primary" href="index.php?navigate=change_profile">Edit your profile</a> 
        </p>
      </form>
    </article>
  </div>
</div>