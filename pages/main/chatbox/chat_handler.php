<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include("../../../admincp/config/connection.php"); 
header('Content-Type: text/plain; charset=utf-8');

if(!isset($_POST['message'])){
    echo "Không nhận được tin nhắn.";
    exit;
}

$user_message = trim($_POST['message']);
$reply = "";

// --- 1. Gợi ý sách ---
if(preg_match('/gợi ý\s+sách\s*(.*)/i',$user_message,$matches)){
    $keyword = trim($matches[1]);
    if($keyword!==""){
        $stmt = $mysqli->prepare("SELECT product_title, product_price, product_author FROM tblproduct WHERE product_title LIKE ? OR product_author LIKE ? LIMIT 5");
        if($stmt){
            $like="%$keyword%";
            $stmt->bind_param("ss",$like,$like);
            $stmt->execute();
            $res = $stmt->get_result();
            if($res->num_rows>0){
                $reply="Gợi ý sách liên quan đến '$keyword':\n";
                while($row=$res->fetch_assoc()){
                    $reply.="• ".$row['product_title']." — ".number_format($row['product_price'],0,',','.')."đ (Tác giả: ".$row['product_author'].")\n";
                }
            } else $reply="Xin lỗi, không tìm thấy sách '$keyword'.";
        } else $reply="Lỗi SQL: ".$mysqli->error;
    } else $reply="Bạn muốn tìm sách thể loại nào?";
}

// --- 2. Tra cứu đơn hàng ---
elseif(preg_match('/đơn\s*hàng\s*#?(\d+)/i',$user_message,$matches)){
    $orderId = $matches[1];
    $stmt = $mysqli->prepare("SELECT order_status,total_price FROM tblorder WHERE order_id=?");
    if($stmt){
        $stmt->bind_param("i",$orderId);
        $stmt->execute();
        $res = $stmt->get_result();
        if($res->num_rows>0){
            $o=$res->fetch_assoc();
            $reply="Trạng thái đơn hàng #$orderId: ".$o['order_status']."\nTổng tiền: ".number_format($o['total_price'],0,',','.')."đ";
        } else $reply="Không tìm thấy đơn hàng #$orderId.";
    } else $reply="Lỗi SQL: ".$mysqli->error;
}

// --- 3. Tin nhắn gửi admin (guest / fallback) ---
else{
    $reply="Tin nhắn đã được gửi đến admin, họ sẽ trả lời bạn sớm.";
}

// --- 4. Lưu vào DB ---
$user_id = $_SESSION['user_id'] ?? 0;
$is_guest = $user_id==0 ? 1:0;
$guest_name = $is_guest ? "Khách vãng lai #".rand(1000,9999) : null;
$is_admin_reply = 0;
$reply_to_user_id = $user_id;

$stmt = $mysqli->prepare("INSERT INTO chat_history(user_id,message_user,message_bot,is_admin_reply,reply_to_user_id,created_at,is_guest,guest_name) VALUES(?,?,?,?,?,NOW(),?,?)");
if($stmt){
    $stmt->bind_param("isiiiis",$user_id,$user_message,$reply,$is_admin_reply,$reply_to_user_id,$is_guest,$guest_name);
    $stmt->execute();
} else {
    error_log("Lỗi lưu chat: ".$mysqli->error);
}

echo nl2br($reply);
?>
