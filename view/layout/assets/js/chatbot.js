/**
 * UTHshop AI Chatbot - JavaScript Handler
 * Powered by Google Gemini AI
 */

class UTHChatbot {
  constructor() {
    this.isOpen = false;
    this.isTyping = false;
    this.messageHistory = [];
    this.init();
  }

  init() {
    this.createWidget();
    this.attachEventListeners();
    this.showWelcomeMessage();
  }

  createWidget() {
    const widgetHTML = `
      <div id="uth-chatbot-widget">
        <!-- Chat Icon -->
        <div id="uth-chat-icon" title="Chat với AI">
          🤖
        </div>

        <!-- Chat Box -->
        <div id="uth-chat-box">
          <!-- Header -->
          <div id="uth-chat-header">
            <div class="title">
              <span class="status"></span>
              <span>🤖 Trợ lý AI UTHshop</span>
            </div>
            <div style="display: flex; gap: 10px;">
              <button id="uth-chat-reset" title="Bắt đầu lại" style="background: none; border: none; color: white; cursor: pointer; font-size: 18px;">🔄</button>
              <button id="uth-chat-close">✕</button>
            </div>
          </div>

          <!-- Welcome -->
          <div id="uth-chat-welcome">
            👋 Xin chào! Tôi là trợ lý ảo của UTHshop. Tôi có thể giúp gì cho bạn?
          </div>

          <!-- Messages -->
          <div id="uth-chat-messages"></div>

          <!-- Quick Replies -->
          <div id="uth-quick-replies">
            <button class="uth-quick-reply" data-text="Có sản phẩm nào hot không?">� Sản phẩm hot</button>
            <button class="uth-quick-reply" data-text="Giá cả như thế nào?">💰 Bảng giá</button>
            <button class="uth-quick-reply" data-text="Có những loại áo gì?">📂 Danh mục</button>
            <button class="uth-quick-reply" data-text="Làm sao để đặt hàng?">🛒 Cách đặt</button>
          </div>

          <!-- Input -->
          <div id="uth-chat-input-container">
            <input type="text" id="uth-chat-input" placeholder="Nhập tin nhắn..." autocomplete="off">
            <button id="uth-chat-send">📤</button>
          </div>

          <!-- Footer -->
          <div id="uth-chat-footer">
            Powered by Google Gemini AI
          </div>
        </div>
      </div>
    `;

    document.body.insertAdjacentHTML('beforeend', widgetHTML);
  }

  attachEventListeners() {
    // Toggle chat
    document.getElementById('uth-chat-icon').addEventListener('click', () => this.toggleChat());
    document.getElementById('uth-chat-close').addEventListener('click', () => this.toggleChat());
    
    // Reset chat
    document.getElementById('uth-chat-reset').addEventListener('click', () => this.resetChat());

    // Send message
    document.getElementById('uth-chat-send').addEventListener('click', () => this.sendMessage());
    document.getElementById('uth-chat-input').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') this.sendMessage();
    });

    // Quick replies
    document.querySelectorAll('.uth-quick-reply').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const text = e.target.getAttribute('data-text');
        document.getElementById('uth-chat-input').value = text;
        this.sendMessage();
      });
    });
  }
  
  resetChat() {
    if(confirm('🔄 Bắt đầu cuộc trò chuyện mới?\n\nLịch sử chat sẽ bị xóa.')){
      // Xóa lịch sử trên server (session)
      fetch('chatbot_reset.php', { method: 'POST' })
        .then(() => {
          // Xóa giao diện
          document.getElementById('uth-chat-messages').innerHTML = '';
          this.messageHistory = [];
          this.showWelcomeMessage();
        })
        .catch(err => console.error('Reset error:', err));
    }
  }

  toggleChat() {
    this.isOpen = !this.isOpen;
    const chatBox = document.getElementById('uth-chat-box');
    const chatIcon = document.getElementById('uth-chat-icon');

    if (this.isOpen) {
      chatBox.classList.add('active');
      chatIcon.style.display = 'none';
      document.getElementById('uth-chat-input').focus();
    } else {
      chatBox.classList.remove('active');
      chatIcon.style.display = 'flex';
    }
  }

  showWelcomeMessage() {
    setTimeout(() => {
      this.addMessage('bot', '👋 Chào bạn! Tôi là trợ lý AI của UTHshop!\n\n✅ Tôi biết tất cả sản phẩm trong shop\n✅ Tư vấn size, màu, giá cả\n✅ Hướng dẫn đặt hàng\n\nHãy chat với tôi nhé! 😊');
    }, 500);
  }

  async sendMessage() {
    const input = document.getElementById('uth-chat-input');
    const message = input.value.trim();

    if (!message || this.isTyping) return;

    // Hiển thị tin nhắn user
    this.addMessage('user', message);
    input.value = '';

    // Disable input
    this.setTyping(true);

    try {
      // Gọi API - đơn giản từ root
      const response = await fetch('chatbot_gemini.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'message=' + encodeURIComponent(message)
      });

      console.log('Response status:', response.status); // Debug
      
      if (!response.ok) {
        throw new Error('HTTP ' + response.status);
      }

      const data = await response.json();
      console.log('Response data:', data); // Debug

      if (data.success) {
        this.addMessage('bot', data.reply);
      } else {
        const errorMsg = data.error_detail ? `❌ ${data.error_detail}` : '❌ Xin lỗi, có lỗi xảy ra!';
        this.addMessage('bot', errorMsg);
      }
    } catch (error) {
      console.error('Chatbot error:', error);
      this.addMessage('bot', '❌ Không thể kết nối!\n\n📍 Kiểm tra:\n- File chatbot_gemini.php có tồn tại?\n- XAMPP đang chạy?\n\n🔍 Lỗi: ' + error.message);
    } finally {
      this.setTyping(false);
    }
  }

  addMessage(sender, text) {
    const messagesContainer = document.getElementById('uth-chat-messages');
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `uth-message ${sender}`;

    const avatar = document.createElement('div');
    avatar.className = 'uth-message-avatar';
    avatar.textContent = sender === 'bot' ? '🤖' : '👤';

    const content = document.createElement('div');
    content.className = 'uth-message-content';
    
    // Format text: hỗ trợ line breaks và highlight
    const formattedText = text
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // **bold**
      .replace(/\n/g, '<br>'); // line breaks
    
    content.innerHTML = formattedText;

    messageDiv.appendChild(avatar);
    messageDiv.appendChild(content);

    messagesContainer.appendChild(messageDiv);

    // Scroll to bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // Save to history
    this.messageHistory.push({ sender, text, timestamp: Date.now() });
  }

  setTyping(isTyping) {
    this.isTyping = isTyping;
    const sendBtn = document.getElementById('uth-chat-send');
    const input = document.getElementById('uth-chat-input');
    const messagesContainer = document.getElementById('uth-chat-messages');

    if (isTyping) {
      // Show typing indicator
      const typingDiv = document.createElement('div');
      typingDiv.className = 'uth-message bot';
      typingDiv.id = 'uth-typing-indicator';
      typingDiv.innerHTML = `
        <div class="uth-message-avatar">🤖</div>
        <div class="uth-typing">
          <span></span><span></span><span></span>
        </div>
      `;
      messagesContainer.appendChild(typingDiv);
      messagesContainer.scrollTop = messagesContainer.scrollHeight;

      sendBtn.disabled = true;
      input.disabled = true;
    } else {
      // Remove typing indicator
      const typingIndicator = document.getElementById('uth-typing-indicator');
      if (typingIndicator) typingIndicator.remove();

      sendBtn.disabled = false;
      input.disabled = false;
      input.focus();
    }
  }
}

// Initialize chatbot when page loads
document.addEventListener('DOMContentLoaded', () => {
  window.uthChatbot = new UTHChatbot();
});
