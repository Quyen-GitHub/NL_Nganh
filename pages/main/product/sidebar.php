<?php
$sql_getList = "SELECT * FROM tblcategory ORDER BY category_id ASC";
$query_getList= mysqli_query($mysqli, $sql_getList);
?>
<div class="text-white" >
  <p><i class="fas fa-list"></i> CATEGORY</p>
  <ul class="list-unstyled">
    <?php
      while ($row_getList= mysqli_fetch_array($query_getList)) {
    ?>
      <li class="p-2 mt-2">
        <a class="text-white" 
          href="index.php?navigate=show_products&category_id=<?php echo $row_getList['category_id']?>">
          <?php echo $row_getList['category_name']?>
        </a>
      </li>
    <?php
      }
    ?>
  </ul>
</div>