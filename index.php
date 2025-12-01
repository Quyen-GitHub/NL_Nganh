<!DOCTYPE html>
<html>

<head>
    <meta charset=utf-8>
    <title>Literature Lounge</title>
    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/bootstrap/js/bootstrap.bundle.js">
    <link rel="stylesheet" href="./assets/bootstrap/js/bootstrap.bundle.min.js">
    <link
        rel="stylesheet"
        href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
        xintegrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/responsive.css">
</head>

<body>
    <?php
    // Code PHP gốc của bạn
    include("./admincp/config/connection.php");
    session_start();
    ?>
    <?php include("./pages/menu.php") ?>
    <?php include("./pages/main.php") ?>
    <?php include("./pages/footer.php") ?>

    <!-- ===== BẮT ĐẦU CODE CHATBOT (CSS, HTML, JS) ===== -->

    <!-- [PHẦN 1: CSS cho Chatbot] -->
    <style>
        /* Nút bấm mở chat */
        #chat-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #0d6efd;
            /* Màu xanh bootstrap */
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 9998;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Khung chat */
        #chat-container {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            height: 450px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            display: none;
            /* Mặc định ẩn */
            flex-direction: column;
            z-index: 9999;
            overflow: hidden;
        }

        #chat-container.open {
            display: flex;
        }

        /* Tiêu đề chat */
        #chat-header {
            background: #0d6efd;
            color: white;
            padding: 15px;
            font-weight: bold;
            text-align: center;
        }

        /* Nơi chứa tin nhắn */
        #chat-messages {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background-color: #f9f9f9;
        }

        .chat-message {
            padding: 8px 12px;
            border-radius: 18px;
            max-width: 80%;
            word-wrap: break-word;
            line-height: 1.4;
        }

        .chat-message.user {
            background: #e9e9eb;
            color: #000;
            align-self: flex-end;
        }

        .chat-message.bot {
            background: #0d6efd;
            color: white;
            align-self: flex-start;
        }

        /* Khung nhập liệu */
        #chat-input-container {
            display: flex;
            border-top: 1px solid #ddd;
        }

        #chat-input {
            flex-grow: 1;
            border: none;
            padding: 15px;
            font-size: 14px;
            outline: none;
        }

        #chat-send {
            border: none;
            background: #0d6efd;
            color: white;
            padding: 0 15px;
            cursor: pointer;
            font-size: 16px;
        }

        #chat-send:disabled {
            background: #aaa;
            cursor: not-allowed;
        }
    </style>

    <!-- [PHẦN 2: HTML cho Chatbot] -->
    <div id="chat-container">
        <div id="chat-header">Literature Lounge Bot</div>
        <div id="chat-messages">
            <!-- Tin nhắn sẽ được tải bằng JavaScript -->
        </div>
        <div id="chat-input-container">
            <input type="text" id="chat-input" placeholder="Nhập tin nhắn...">
            <button id="chat-send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <button id="chat-toggle">
        <i class="fas fa-comments"></i>
    </button>

    <!-- [PHẦN 3: JavaScript cho Chatbot] -->

    <!-- Tiêm USER_ID từ PHP Session vào JavaScript -->
    <script>
        // Lấy user_id từ PHP Session. Sẽ là null nếu chưa đăng nhập.
        const currentUserId = <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>;
    </script>

    <!-- Code JavaScript điều khiển Chatbot (Stateful) -->
    <script>
        // Đợi cho toàn bộ trang tải xong mới chạy code
        document.addEventListener("DOMContentLoaded", () => {

            // --- DOM Elements ---
            const chatContainer = document.getElementById('chat-container');
            const chatToggle = document.getElementById('chat-toggle');
            const chatMessages = document.getElementById('chat-messages');
            const chatInput = document.getElementById('chat-input');
            const chatSend = document.getElementById('chat-send');

            // --- API URLs ---
            const API_BASE_URL = 'http://127.0.0.1:8000'; // Đảm bảo server FastAPI chạy ở cổng này
            const START_SESSION_URL = `${API_BASE_URL}/start_session`;
            const GET_HISTORY_URL = `${API_BASE_URL}/get_history`;
            const CHAT_URL = `${API_BASE_URL}/chat`;

            // --- State ---
            let chatSessionId = null; // Quản lý session ID
            let isLoading = false; // Ngăn gửi tin nhắn khi đang xử lý

            // --- Functions ---

            // 1. Mở/đóng khung chat
            chatToggle.addEventListener('click', () => {
                chatContainer.classList.toggle('open');
                if (chatContainer.classList.contains('open')) {
                    // Khi mở, bắt đầu hoặc tải phiên chat
                    initializeChat();
                }
            });

            // 2. Bắt đầu phiên chat (chỉ gọi 1 lần khi mở)
            // 2. Bắt đầu phiên chat
            // Trong thẻ <script> của file index.php

            async function initializeChat() {
                if (isLoading) return;
                chatContainer.classList.add('open');

                // Nếu biến global đã có giá trị (do phiên chạy hiện tại)
                if (chatSessionId) return;

                isLoading = true;
                chatSend.disabled = true;
                chatMessages.innerHTML = '';
                addMessageToUI('bot', 'Đang kết nối...');

                try {
                    // Chuẩn bị dữ liệu gửi lên
                    // Lấy session_id cũ từ trình duyệt (nếu có) để phục vụ khách vãng lai
                    const localSessionId = localStorage.getItem('chat_session_id');

                    const payload = {
                        user_id: currentUserId // Có thể là số (User) hoặc null (Khách)
                    };

                    // Nếu là Khách (null), gửi kèm token cũ để Server kiểm tra
                    if (!currentUserId && localSessionId) {
                        payload.client_session_id = localSessionId;
                    }

                    console.log('Gửi yêu cầu start_session:', payload);

                    const response = await fetch(START_SESSION_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    if (data.session_id) {
                        chatSessionId = data.session_id;

                        // LUÔN LUÔN cập nhật lại localStorage để dùng cho lần sau
                        localStorage.setItem('chat_session_id', chatSessionId);

                        console.log(`Đã kết nối Session: ${chatSessionId}`);
                        await loadHistory();
                    } else {
                        throw new Error('Server không trả về session ID');
                    }

                } catch (error) {
                    console.error(error);
                    chatMessages.innerHTML = '';
                    addMessageToUI('bot', 'Lỗi kết nối. Vui lòng thử lại.');
                } finally {
                    isLoading = false;
                    chatSend.disabled = false;
                }
            }

            // 3. Tải lịch sử chat
            async function loadHistory() {
                if (!chatSessionId) return;

                try {
                    const response = await fetch(`${GET_HISTORY_URL}/${chatSessionId}`);
                    const history = await response.json();

                    chatMessages.innerHTML = ''; // Xóa tin "Đang kết nối"

                    if (history.length === 0) {
                        addMessageToUI('bot', 'Chào bạn! Tôi có thể giúp gì cho bạn về sách hôm nay?');
                    } else {
                        history.forEach(msg => {
                            addMessageToUI(msg.role, msg.content);
                        });
                    }
                } catch (error) {
                    console.error('Lỗi tải lịch sử:', error);
                    chatMessages.innerHTML = '';
                    addMessageToUI('bot', 'Lỗi tải lịch sử chat.');
                }
            }

            // 4. Gửi tin nhắn
            async function sendMessage() {
                const message = chatInput.value.trim();
                if (message === '' || isLoading || !chatSessionId) return;

                isLoading = true;
                chatSend.disabled = true;
                addMessageToUI('user', message);
                chatInput.value = '';
                addMessageToUI('bot', '...'); // Dấu hiệu bot đang gõ

                try {
                    const response = await fetch(CHAT_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            session_id: chatSessionId,
                            message: message
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    removeTypingIndicator(); // Xóa "..."
                    addMessageToUI('model', data.response); // Hiển thị câu trả lời

                } catch (error) {
                    console.error('Lỗi khi gửi tin nhắn:', error);
                    removeTypingIndicator();
                    addMessageToUI('bot', 'Xin lỗi, tôi đang gặp sự cố kết nối.');
                }
                isLoading = false;
                chatSend.disabled = false;
                chatInput.focus(); // Focus lại ô nhập liệu
            }

            // 5. Gửi bằng Enter hoặc Click
            chatSend.addEventListener('click', sendMessage);
            chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });

            // 6. Các hàm trợ giúp UI
            function addMessageToUI(role, text) {
                const messageElement = document.createElement('div');
                // Đổi 'user' -> 'user', 'model' -> 'bot' cho CSS
                messageElement.className = `chat-message ${role === 'model' ? 'bot' : 'user'}`;

                // Chuyển đổi ký tự xuống dòng (\n) thành thẻ <br>
                messageElement.innerHTML = text.replace(/\n/g, '<br>');

                chatMessages.appendChild(messageElement);
                // Tự động cuộn xuống tin nhắn mới nhất
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function removeTypingIndicator() {
                const messages = chatMessages.querySelectorAll('.chat-message.bot');
                const lastBotMessage = messages[messages.length - 1];
                if (lastBotMessage && lastBotMessage.innerHTML === '...') {
                    lastBotMessage.remove();
                }
            }
        });
    </script>

    <!-- ===== KẾT THÚC CODE CHATBOT ===== -->

</body>

<!-- Script gốc của bạn -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</html>