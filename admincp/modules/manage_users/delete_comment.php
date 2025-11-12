<?php 
    include("../../config/connection.php");
    if (isset($_GET['id'])) {
        $comment_id=$_GET['id'];
        $sql = "DELETE FROM  tblcomment WHERE comment_id='".$comment_id."'";
        mysqli_query($mysqli,$sql);
    }
    header('location:../../index.php?user=comments');
?>