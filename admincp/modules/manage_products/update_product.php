<?php
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql_getProduct = "SELECT * FROM tblproduct where product_id=$product_id LIMIT 1";
    $query_getProduct = mysqli_query($mysqli, $sql_getProduct);
    $row = mysqli_fetch_array($query_getProduct);
    $sql_category = "SELECT * FROM tblcategory ORDER BY category_id DESC";
    $query_category = mysqli_query($mysqli,$sql_category);
}
?>

<div>
    <div id="content" class="container-fluid">
        <div class="card">
            <div class="card-header font-weight-bold">Update</div>
            <div class="card-body">
                <form action="modules/manage_products/update.php?id=<?=$product_id?>" 
                    method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="">Title</label>
                        <input
                            id=""
                            class="form-control"
                            type="text"
                            name="product_title"
                            value="<?php echo $row['product_title'] ?>"
                        />
                    </div>
                    <div class="form-group">
                        <label for="">Description</label>
                        <textarea class="form-control" required name="product_description"><?php echo $row['product_description'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Quantity</label>
                        <input
                            id=""
                            class="form-control" required
                            type="text"
                            name="product_quantity"
                            value="<?php echo $row['product_quantity'] ?>"
                        />
                    </div>
                    <div class="form-group">
                        <label for="">Price</label>
                        <input
                            id=""
                            class="form-control" required
                            type="text"
                            name="product_price"
                            value="<?php echo $row['product_price'] ?>"
                        />
                    </div>
                    <div class="form-group">
                        <label for="">Discount(%)</label>
                        <input
                            id=""
                            class="form-control" required
                            type="text"
                            name="product_discount"
                            value="<?php echo $row['product_discount']?>"
                        />
                    </div>
                    <div class="form-group">
                        <label for="">Author</label>
                        <input
                            id=""
                            class="form-control" required
                            type="text"
                            name="product_author"
                            value="<?php echo $row['product_author']?>"
                        />
                    </div>
                    <div class="form-group">
                    <label for="">Category</label>
                    <select class="form-control" required name="category">
                        <option value="">Choose a category</option>
                        <?php while ($row_category=mysqli_fetch_array($query_category)){
                        ?>
                        <option value="<?php echo $row_category['category_id']?>" name="category_id"><?php echo $row_category['category_name']?></option>
                    <?php }?>
                    </select>
                </div>
                
                    <div class="form-group">
                        <label for="formFile">Image</label>
                        <img
                            style="width: 240px;height: 240px;object-fit: contain;object-position: center center;"
                            src="../assets/images/products/<?php echo $row['product_image'] ?>"
                        />
                        <input
                            class="form-control"
                            type="file"
                            name="product_image"
                            accept="image/*"
                        />
                    </div>
                    <input
                        type="submit"
                        class="btn btn-primary"
                        name="submit"
                        value="Update"
                    />
                </form>
            </div>
        </div>
    </div>
</div>
