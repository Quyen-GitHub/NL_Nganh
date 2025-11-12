<?php 
    $sql_user="SELECT * FROM tbluser Where user_enabled = 1 ORDER BY user_id DESC";
    $query_user=mysqli_query($mysqli,$sql_user);
?>
<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <h5 class="m-0 ">List of users</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-checkall">
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Fullname</th>
                        <th scope="col">Address</th>
                        <th scope="col">Phone number</th>
                        <th scope="col">Created date</th>
                        <th scope="col">Login name</th>
                        <th scope="col">Edit/Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i =0;
                    while($row_user=mysqli_fetch_array($query_user)){
                    $i++;
                    ?>
                    <tr>
                        <th scope="row"><?php echo $i ?></th>
                        <td><?php echo $row_user['user_fullname'] ?></td>
                        <td><?php echo $row_user['user_address'] ?></td>
                        <td><?php echo $row_user['user_phone'] ?></td>
                        <td><?php echo $row_user['user_created_date'] ?></td>
                        <td><?php echo $row_user['user_loginname'] ?></td>
                        <td class="d-flex">
                            <a href="?user=change_user_info&id=<?php echo $row_user['user_id']?>" class="btn btn-success btn-sm rounded text-white mr-2" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                            <a href="modules/manage_users/delete_user.php?id=<?php echo $row_user['user_id']?>" class="btn btn-danger btn-sm rounded text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>