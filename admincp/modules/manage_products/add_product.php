<?php 
$sql_category="SELECT * FROM tblcategory ORDER BY category_id DESC";
$query_category=mysqli_query($mysqli,$sql_category);
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold">
            Add an product
        </div>
        <div class="card-body">
            <form action="modules/manage_products/add.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name">Title</label>
                            <input class="form-control" required type="text" name="product_title" id="name">
                        </div>
                        <div class="form-group">
                            <label for="name">Price</label>
                            <input class="form-control" required type="text" name="product_price" id="name">
                        </div>
                        <div class="form-group">
                            <label for="name">Quantity</label>
                            <input class="form-control" required type="text" name="product_quantity" id="name">
                        </div>
                        <div class="form-group">
                            <label for="name">Author</label>
                            <input class="form-control" required type="text" name="product_author" id="name">
                        </div>
                    </div>      
                    <div class="col-6">
                        <div class="form-group">
                            <label for="intro">Description</label>
                            <textarea name="product_description" class="form-control" required id="intro" cols="30" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input class="form-control" required type="file" name="product_image" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="">Category</label>
                    <select class="form-control" required name="category">
                        <option value="">Choose a category</option>
                        <?php while ($row_category=mysqli_fetch_array($query_category)) { ?>
                        <option value="<?php echo $row_category['category_id']?>" name="category_id"><?php echo $row_category['category_name']?></option>
                        <?php }?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Discount(%)</label>
                    <input class="form-control" required type="text" name="product_discount" id="name">
                </div>
                <input type="submit" class="btn btn-primary"  value="ADD" name="submit">
            </form>
        </div>
    </div>
</div>