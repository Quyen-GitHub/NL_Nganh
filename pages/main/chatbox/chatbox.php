<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<style>
.chatbot-container { position: fixed; bottom:25px; right:25px; z-index:9999; font-family:Arial; }
.chatbot-toggle { background:#ae2170; color:#fff; border:none; border-radius:50%; width:60px; height:60px; font-size:28px; cursor:pointer; }
.chatbot-box { display:none; flex-direction:column; width:340px; height:470px; background:#fff; border-radius:12px; overflow:hidden; }
.chatbot-header { background:#ae2170; color:#fff; padding:10px; text-align:center; font-weight:bold; position:relative; }
.chatbot-close { position:absolute; top:8px; right:10px; background:none; border:none; color:white; font-size:20px; cursor:pointer; }
.chatbot-messages { flex:1; padding:10px; overflow-y:auto; background:#f8f9fa; display:flex; flex-direction:column; }
.chatbot-message { margin-bottom:10px; padding:8px 12px; border-radius:8px; max-width:80%; word-wrap:break-word; }
.user-message { background:#dcf8c6; align-self:flex-end; text-align:right; }
.bot-message { background:#f1f0f0; align-self:flex-start; text-align:left; }
.chatbot-input { display:flex; border-top:1px solid #ddd; }
.chatbot-input input { flex:1; padding:8px; border:none; outline:none; }
.chatbot-input button { background:#ae2170; color:#fff; border:none; padding:8px 15px; cursor:pointer; }
</style>

<div class="chatbot-container">
  <button id="chatbotToggle" class="chatbot-toggle">💬</button>
  <div id="chatbotBox" class="chatbot-box">
    <div class="chatbot-header">
      Chat LiteratureLounge
      <button class="chatbot-close" id="closeChatbot">&times;</button>
    </div>
    <div id="chatbotMessages" class="chatbot-messages">
      <div class="bot-message chatbot-message">
        Xin chào! Bạn có thể hỏi 'gợi ý sách <tên thể loại>' hoặc 'đơn hàng #<mã đơn>'.
      </div>
    </div>
    <div class="chatbot-input">
      <input id="chatbotInput" type="text" placeholder="Nhập tin nhắn...">
      <button id="chatbotSend">Gửi</button>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const chatbotToggle = document.getElementById('chatbotToggle');
const chatbotBox = document.getElementById('chatbotBox');
const chatbotInput = document.getElementById('chatbotInput');
const chatbotSend = document.getElementById('chatbotSend');
const chatbotMessages = document.getElementById('chatbotMessages');
const closeChatbot = document.getElementById('closeChatbot');

chatbotToggle.addEventListener('click', () => { chatbotBox.style.display='flex'; chatbotToggle.style.display='none'; });
closeChatbot.addEventListener('click', () => { chatbotBox.style.display='none'; chatbotToggle.style.display='block'; });

function appendMessage(text, sender='bot') {
  const msg = document.createElement('div');
  msg.classList.add('chatbot-message', sender==='user'?'user-message':'bot-message');
  msg.innerHTML = text;
  chatbotMessages.appendChild(msg);
  chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
}

chatbotSend.addEventListener('click', sendMessage);
chatbotInput.addEventListener('keypress', (e)=>{ if(e.key==='Enter') sendMessage(); });

function sendMessage() {
  const message = chatbotInput.value.trim();
  if(!message) return;
  appendMessage(message,'user');
  chatbotInput.value='';

  fetch('/LiteratureLounge/pages/main/chatbox/chat_handler.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'message='+encodeURIComponent(message)
  })
  .then(res=>res.text())
  .then(reply=>appendMessage(reply,'bot'))
  .catch(()=>appendMessage('Lỗi kết nối chatbot.','bot'));
}
</script>
