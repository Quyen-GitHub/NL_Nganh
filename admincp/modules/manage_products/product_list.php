<?php 
    if(isset($_GET['trang'])){
        $page = $_GET['trang'];
    } else{
        $page = 1;
    }
    if($page == '' || $page == 1){
        $begin = 0;
    } else{
        $begin = ($page*8)-8;
    }
    $sql_product="SELECT * FROM tblproduct ORDER BY product_id DESC LIMIT $begin,8";
    if (isset($_POST['keyword'])) {
        $keyword= $_POST['keyword'];
        $sql_product="SELECT * FROM tblproduct where tblproduct.product_title OR tblproduct.product_author LIKE '%".$keyword."%'";
    }
    $query_product=mysqli_query($mysqli,$sql_product);
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <h5 class="m-0 ">List of products</h5>
            <div class="form-search form-inline">
                <form action="" method="POST" class="d-flex">
                    <input type="text" class="form-control form-search" placeholder="search" name="keyword">
                    <input type="submit" name="btn-search" value="Search" class="btn btn-info">
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="tableInfo">
                <table class="table">
                    <thead>
                        <tr>
                        <th>No.</th>
                        <th>ID</th>
                        <th>Title</th> 
                        <th>Quantity</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Update</th>
                    </tr>
                </thead>
                        <?php
                        $i=0;
                        while ($row= mysqli_fetch_array($query_product)){
                            $i++; 
                            ?>
                            <tbody>
                                <td><?php echo $i ?></td> 
                                <td><?php echo $row['product_id'];?></td>  
                                <td><?php echo $row['product_title'];?></td>
                                <td><?php echo $row['product_quantity'];?></td>        
                                <td> <img style="width: 180px; height: 180px; object-fit: contain; object-position: center center;" 
                                    src="../assets/images/products/<?php echo $row['product_image'];?>"/></td>    
                                <td><?php echo number_format($row['product_price'],0,',','.');?></td>
                                <td><?php echo $row['product_discount'];?>%</td>
                                <td>
                                    <a href="?product=update_product&id=<?php echo $row['product_id'];?>">Update</a>
                                </td>   
                            </tbody> 
                        <?php
                        }
                    ?>
                </table>
            </div>      
            <?php
            if(!isset($_POST['keyword'])) {
            $sql_trang = mysqli_query($mysqli, "SELECT * FROM tblproduct");
            $row_count = mysqli_num_rows($sql_trang);  
            $trang = ceil($row_count/8);
            ?>				
            <ul class="d-flex justify-content-center list-unstyled">
                <?php
                  for($i=1;$i<=$trang;$i++){ 
                ?>
                    <li class="p-2 m-1" <?php if($i==$page){echo 'style="background: #ccc"';}else{echo 'style="background: #fff"';}?>>
                        <a class="text-dark" href="index.php?product=product_list&trang=<?php echo $i ?>"><?php echo $i ?></a>
                    </li>
                <?php
                } 
            }
                ?>
            </ul>
        </div>
    </div>
</div>