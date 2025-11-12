<?php
  include("../../../admincp/config/connection.php");
  if (isset($_POST['change']) && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $partten = "/^[A-Za-z0-9_.]{6,32}@([a-zA-Z0-9]{2,12})(.[a-zA-Z]{2,12})+$/";
    if (preg_match("/^[0-9]{10,12}$/", $phone)){
      $sql_update = "UPDATE tbluser set user_fullname = '" . $fullname . "', user_email ='" . $email . "',
      user_address = '" . $address . "',  user_phone = '" . $phone . "' WHERE user_id = '$user_id'";
      mysqli_query($mysqli, $sql_update);
      header('location: ../../../index.php?navigate=profile');
    }
  }
?>