<section class="chatbot-main">
  <div class="chatbot-view chatbot-view--intro" data-view="intro">
    <div class="main-header">
      <span class="main-label">CHAT A.I+</span>
    </div>

    <h1 class="main-title">Good day! How may I assist you today?</h1>

    <div class="chat-input">
      <input type="text" placeholder="What's in your mind..." aria-label="Chat input">
      <button class="send-btn" type="button" aria-label="Send message">
        <i class="fa-solid fa-paper-plane"></i>
      </button>
    </div>
  </div>

  <div class="chatbot-view chatbot-view--chat is-hidden" data-view="chat">
    <div class="chat-header">
      <span class="main-label">CHAT A.I+</span>
    </div>

    <div class="chat-messages" id="chatMessages">
      <!-- Messages will be rendered dynamically -->
    </div>

    <div class="chat-input chat-input--bottom">
      <input type="text" placeholder="Type your message..." aria-label="Chat input">
      <button class="send-btn" type="button" aria-label="Send message">
        <i class="fa-solid fa-paper-plane"></i>
      </button>
    </div>
  </div>
</section>
