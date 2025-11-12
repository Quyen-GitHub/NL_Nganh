<?php
include("../../../config/connection.php");
header('Content-Type: application/json; charset=utf-8');

$action=$_GET['action']??'';
$response=[];

if($action=='get_messages'){
    $res=$mysqli->query("SELECT * FROM chat_history ORDER BY created_at ASC");
    while($row=$res->fetch_assoc()){
        $response[]=[
            'id'=>$row['id'],
            'user_id'=>$row['user_id'],
            'user_msg'=>$row['message_user'],
            'admin_msg'=>$row['message_bot'],
            'is_admin_reply'=>$row['is_admin_reply'],
            'guest_name'=>$row['is_guest']?$row['guest_name']:null
        ];
    }
    echo json_encode($response);
    exit;
}

if($action=='send_reply' && isset($_POST['chat_id'],$_POST['reply'])){
    $chat_id=$_POST['chat_id'];
    $reply=$_POST['reply'];
    $stmt=$mysqli->prepare("UPDATE chat_history SET message_bot=?,is_admin_reply=1 WHERE id=?");
    $stmt->bind_param("si",$reply,$chat_id);
    $stmt->execute();
    echo json_encode(['status'=>true]);
    exit;
}

echo json_encode(['status'=>false]);
?>
