<?php 
    $sql_category_products="SELECT * FROM tblcategory ORDER BY category_id ASC";
    $query_category_products=mysqli_query($mysqli,$sql_category_products);
    if (isset($_GET['delete_id'])) {
		$sql_check = "SELECT COUNT(*) as count FROM tblproduct WHERE category_id = '".$_GET['delete_id']."'";
		$result = mysqli_query($mysqli, $sql_check);
		$row_count = mysqli_num_rows($result);
		if($row_count == 0){
			$sql_delete = "DELETE FROM tblcategory where tblcategory.category_id = '".$_GET['delete_id']."'";
			mysqli_query($mysqli, $sql_delete);
		} else {	
            echo "<script>alert(\"Unable to delete\")</script>";
		}
	}
?>
    <div id="content" class="container-fluid">
    <div class="row">
        <div class="col-4">
            <div class="card">
                <div class="card-header font-weight-bold">
                    Add a category
                </div>
                <div class="card-body">
                    <form action="modules/manage_categories/add_cat.php" method="POST">
                        <div class="form-group">
                            <label for="category_name">Category name:</label>
                            <input class="form-control" type="text" name="category_name" id="category_name" required>
                        </div>
                        <input type="submit" class="btn btn-success" name="add" value="Add">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-header font-weight-bold">
                    List of categories
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Edit/Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=0;
                            while ($row_category_products=mysqli_fetch_array($query_category_products)) {
                                ?>
                                <tr>
                                    <td><?php echo $row_category_products['category_id']?></td>
                                    <td><?php echo $row_category_products['category_name']?></td>
                                    <td class="d-flex">
                                        <a href="?cat=change_cat_name&id=<?php echo $row_category_products['category_id']?>" class="btn btn-success btn-sm text-white mr-2" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                        <a href="?cat=cat_list&delete_id=<?php echo $row_category_products['category_id']?>" class="btn btn-danger btn-sm text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>     
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>