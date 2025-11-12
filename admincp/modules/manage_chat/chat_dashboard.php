<div class="container mt-3">
    <h4>Chat với khách hàng</h4>
    <div id="chat-box" style="height:400px; overflow-y:auto; border:1px solid #ccc; padding:10px; margin-bottom:10px;"></div>
    <input type="hidden" id="chat_id">
    <div class="input-group">
        <input type="text" id="admin_reply" class="form-control" placeholder="Nhập phản hồi...">
        <button id="send_reply" class="btn btn-primary">Gửi</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function fetchMessages(){
    $.getJSON("chat_admin_ajax.php?action=get_messages",function(data){
        let html='';
        data.forEach(msg=>{
            let userLabel=msg.guest_name?msg.guest_name:"User #"+msg.user_id;
            html+="<b>"+userLabel+":</b> <span data-id='"+msg.id+"'>"+msg.user_msg+"</span><br>";
            if(msg.is_admin_reply==1) html+="<b>Admin:</b> "+msg.admin_msg+"<br>";
            html+="<hr>";
        });
        $("#chat-box").html(html);
        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
    });
}

$("#send_reply").click(function(){
    let reply=$("#admin_reply").val();
    let chat_id=$("#chat_id").val();
    if(reply==''||chat_id=='') return;
    $.post("chat_admin_ajax.php?action=send_reply",{chat_id:chat_id,reply:reply},function(res){
        if(res.status){
            $("#admin_reply").val('');
            fetchMessages();
        }
    },"json");
});

$("#chat-box").on('click','span[data-id]',function(){
    let id=$(this).attr('data-id');
    $("#chat_id").val(id);
});

setInterval(fetchMessages,3000);
fetchMessages();
</script>
