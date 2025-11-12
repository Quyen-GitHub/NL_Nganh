<?php
    $sql_product = "SELECT * FROM tblproduct  where product_id = '$_GET[product_id]' ORDER BY product_id DESC";
    $query_product = mysqli_query($mysqli, $sql_product);
    $row_product = mysqli_fetch_array($query_product);
    $sql_comment = "SELECT * FROM tblcomment,tbluser 
    where tblcomment.product_id = '$_GET[product_id]' and tblcomment.user_id = tbluser.user_id";
    $query_comment = mysqli_query($mysqli, $sql_comment);
?>
<div>
    <div class="container mt-4">
        <form method="post" action="pages/main/cart/add.php?id=<?php echo $row_product['product_id']?>">
            <h1 class="text-center">
                <?php echo $row_product['product_title']; ?>
            </h1>
            <div class="row">
                <div class="col-lg-4">
                   <div class="card">
                    <img src="./assets/images/products/<?php echo $row_product['product_image'];?>"
                    style="display: inline-block; width: 100%; height: 400px; object-fit: contain; object-position: center center;"
                    >
                   </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="text-center mb-3">Information</h5>
                    <h6>Price: <?php echo number_format($row_product['product_price'],0,',','.')?> VND/Vol</h6>
                    <h6>Discount: <?php echo -$row_product['product_discount']?>%</h6>
                    <h6>Purchase price: <?php echo number_format($row_product['product_price'] *(100 - $row_product['product_discount'])/ 100,0,',','.')?> VND/Vol</h6>
                    <?php if (isset($_SESSION['user_id'])) {
                        ?>
                        <div class="form-group">
                            <label for="quantity"><b>Quantity:</b></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1">
                        </div>
                        <?php if($row_product['product_quantity'] > 0 ){?>
                        <div>
                            <input type="submit" class="btn btn-success" name='mua' value="Add to cart">
                        </div>
                        <?php } else { ?>
                            <p class="text-danger">The product is temporarily out of stock!!!</p>
                        <?php
                        }
                    }
                    ?>
                </div>
                <div class="col-lg-4">
                    <div class="card p-3">
                    <h5 class="text-center">Description</h5>
                    <hr>
                    <p class="font-italic">
                        "<?php echo $row_product['product_description']; ?>"
                    </p>
                    <p>Author: <?php echo $row_product['product_author']; ?></p>
                    </div>
                </div>
            </div>
        </form>       
    </div>
    <div class="container my-5">
        <h3>Comments: </h3>
        <form class="form-floating" action="pages/main/product/comment.php?product_id=<?php echo $row_product['product_id']; ?>" method="POST">
            <?php
                while ($row_comment = mysqli_fetch_array($query_comment)) {
            ?>
                    <div class="alert alert-success" role="alert">
                    <p>
                        <small class="font-weight-bold">
                            <?php echo $row_comment['user_fullname']; ?>
                        </small>
                        <br>
                        <small>
                            <?php echo $row_comment['comment_time']; ?>
                        </small>
                    </p>
                    <p>
                    <?php echo $row_comment['comment_content']; ?>
                    </p>
                    </div>
                    <?php
                }
                ?>
            <?php if (isset($_SESSION['user_id'])) {
                ?>
                <div class="form">
                    <textarea class="form-control" placeholder="Leave your comment!"
                        name="comment_content" style="height: 100px"></textarea>
                    </br>
                </div>
                <div class="action">
                    <input type="submit" class="btn btn-success" name='comment' 
                        value="Comment" style="float:right">
                </div>
            <?php
            }
            ?>
         </form>
    </div>      
</div>
