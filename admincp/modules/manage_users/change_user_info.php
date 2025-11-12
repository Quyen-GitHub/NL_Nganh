<?php
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql_getUser = "SELECT * FROM tbluser where user_id = $user_id";
    $query_getUser = mysqli_query($mysqli, $sql_getUser);
    $row = mysqli_fetch_array($query_getUser);
}
?>

<div>
    <div id="content" class="container-fluid">
        <div class="card">
            <div class="card-header font-weight-bold">
                Change user information
            </div>
            <div class="card-body">
                <form action="modules/manage_users/change.php?id=<?= $user_id?>" method="POST">
                    <div class="form-group">
                        <label for="fullname">Fullname: </label>
                        <input required class="form-control" type="text" name="user_fullname" id="fullname"
                            value="<?php echo $row['user_fullname'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Address: </label>
                        <input required class="form-control" type="text" name="user_address" id="address"
                            value="<?php echo $row['user_address'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone number: </label>
                        <input required class="form-control" type="text" name="user_phone" id="phone"
                            value="<?php echo $row['user_phone'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password: </label>
                        <input class="form-control" type="text" name="user_password" id="password">
                    </div>
                    <input type="submit" class="btn btn-primary" name="submit" value="Save">
                </form>
            </div>
        </div>
    </div>
</div>
