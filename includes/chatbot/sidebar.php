<aside class="chatbot-sidebar">
  <div class="sidebar-brand">CHAT A.I+</div>

  <button class="new-chat-btn" type="button">
    <span class="new-chat-icon">+</span>
    New chat
  </button>

  <div class="sidebar-section">
    <div class="section-header">
      <div class="section-title">Your conversations</div>
      <button class="clear-all-btn" type="button">Clear All</button>
    </div>
    <ul class="conversation-list" id="conversationList"></ul>
  </div>

  <div class="sidebar-footer">
    <div class="user-info">
      <span class="user-icon" aria-hidden="true">
        <i class="fa-solid fa-user"></i>
      </span>
      <span class="user-name"><?php echo $_SESSION['user']['username']; ?></span>
    </div>
    <button class="logout-btn" type="button" id="logoutBtn">Logout</button>
  </div>
</aside>

<?php
// echo json_encode($_SESSION);
?>
<script>
  // fucntion logout
  document.getElementById('logoutBtn').addEventListener('click', function () {
    fetch('../../function/logout.php', { method: 'POST' })
    .then(() => window.location.href = 'index.php');
  });

  function getConversations() {
    fetch(`../../api/getConversations.php`, { method: 'GET' })
    .then(response => response.json())
    .then(data => {
      const conversationList = document.getElementById('conversationList');
      let item = '';
      if (data.success && Array.isArray(data.data) && data.data.length > 0) {
        data.data.forEach(conv => {
          item += `
            <li class="conversation-row" data-id="${conv.id}">
              <button type="button" class="conversation-item">${conv.title ? conv.title : 'Untitled Conversation'}</button>
              <button type="button" class="conversation-remove" aria-label="Remove conversation">
                <i class="fa-solid fa-trash"></i>
              </button>
            </li>
          `;
        });
      } else {
        item = `
          <li class="conversation-empty">
            <img src="../../assets/images/empty-chat.svg" alt="No conversations" />
            <span style="text-align: center; width: 100%;">No conversations yet</span>
          </li>
        `;
      }
      conversationList.innerHTML = item;
    })
    .catch(error => {
      console.error('Error:', error);
    });
  }

  getConversations();

  // Event delegation for remove conversation buttons
  document.getElementById('conversationList').addEventListener('click', function(e) {
    const removeBtn = e.target.closest('.conversation-remove');
    if (!removeBtn) return;

    const conversationRow = removeBtn.closest('.conversation-row');
    const conversationId = conversationRow.dataset.id;

    fetch('../../api/deleteConversation.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ conversation_id: conversationId })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        getConversations(); // Refresh the conversation list
      } else {
        alert(data.message || 'Failed to delete conversation.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to delete conversation.');
    });
  });

  // Clear all conversations
  document.querySelector('.clear-all-btn').addEventListener('click', function() {
    if (!confirm('Are you sure you want to delete all conversations? This action cannot be undone.')) {
      return;
    }

    fetch('../../api/clearAllConversations.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        getConversations(); // Refresh the conversation list
        // Reset to intro view after clearing all
        showIntroView();
      } else {
        alert(data.message || 'Failed to clear all conversations.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to clear all conversations.');
    });
  });

  // Track active conversation
  let activeConversationId = null;

  // Format time from datetime string to HH:MM
  function formatTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
  }

  // Escape HTML to prevent XSS
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // Parse markdown for bot messages
  function parseMarkdown(text) {
    if (typeof marked !== 'undefined') {
      // Configure marked for safe rendering
      marked.setOptions({
        breaks: true,
        gfm: true
      });
      return marked.parse(text);
    }
    return escapeHtml(text);
  }

  // Show intro view
  function showIntroView() {
    const introView = document.querySelector('[data-view="intro"]');
    const chatView = document.querySelector('[data-view="chat"]');
    if (introView) introView.classList.remove('is-hidden');
    if (chatView) chatView.classList.add('is-hidden');
    activeConversationId = null;
    updateActiveConversationUI();
  }

  // New chat button - switch to intro view
  document.querySelector('.new-chat-btn').addEventListener('click', function() {
    showIntroView();
  });

  // Show chat view
  function showChatView() {
    const introView = document.querySelector('[data-view="intro"]');
    const chatView = document.querySelector('[data-view="chat"]');
    if (introView) introView.classList.add('is-hidden');
    if (chatView) chatView.classList.remove('is-hidden');
  }

  // Update active conversation UI highlight
  function updateActiveConversationUI() {
    document.querySelectorAll('.conversation-item').forEach(item => {
      item.classList.remove('active');
    });
    if (activeConversationId) {
      const activeRow = document.querySelector(`.conversation-row[data-id="${activeConversationId}"] .conversation-item`);
      if (activeRow) {
        activeRow.classList.add('active');
      }
    }
  }

  // Render messages to chat view
  function renderMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;

    if (!messages || messages.length === 0) {
      chatMessages.innerHTML = '<div class="chat-empty">No messages yet. Start the conversation!</div>';
      return;
    }

    let html = '';
    messages.forEach(msg => {
      const roleClass = msg.role === 'user' ? 'from-user' : 'from-bot';
      const content = msg.role === 'user' ? `<p>${escapeHtml(msg.content)}</p>` : `<div class="message-content">${parseMarkdown(msg.content)}</div>`;
      html += `
        <div class="chat-message ${roleClass}">
          ${content}
          <span class="chat-time">${formatTime(msg.created_at)}</span>
        </div>
      `;
    });
    chatMessages.innerHTML = html;

    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  // Load conversation messages
  function loadConversation(conversationId) {
    activeConversationId = conversationId;
    updateActiveConversationUI();

    fetch(`../../api/getMessages.php?conversation_id=${conversationId}`, { method: 'GET' })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        renderMessages(data.data);
        showChatView();
      } else {
        alert(data.message || 'Failed to load conversation.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to load conversation.');
    });
  }

  // Event delegation for conversation item click
  document.getElementById('conversationList').addEventListener('click', function(e) {
    const conversationItem = e.target.closest('.conversation-item');
    if (!conversationItem) return;

    const conversationRow = conversationItem.closest('.conversation-row');
    if (!conversationRow) return;

    const conversationId = conversationRow.dataset.id;
    loadConversation(conversationId);
  });

  // ========== CHAT AI IMPLEMENTATION ==========
  
  let isGenerating = false;

  // Get all send buttons and inputs
  function getSendElements() {
    return {
      introInput: document.querySelector('[data-view="intro"] .chat-input input'),
      introBtn: document.querySelector('[data-view="intro"] .send-btn'),
      chatInput: document.querySelector('[data-view="chat"] .chat-input input'),
      chatBtn: document.querySelector('[data-view="chat"] .send-btn')
    };
  }

  // Set loading state on all send buttons
  function setLoadingState(loading) {
    isGenerating = loading;
    const { introBtn, chatBtn, introInput, chatInput } = getSendElements();
    
    [introBtn, chatBtn].forEach(btn => {
      if (!btn) return;
      if (loading) {
        btn.disabled = true;
        btn.classList.add('is-loading');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
      } else {
        btn.disabled = false;
        btn.classList.remove('is-loading');
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
      }
    });

    [introInput, chatInput].forEach(input => {
      if (input) input.disabled = loading;
    });
  }

  // Add user message to chat UI
  function addUserMessage(content) {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;

    // Remove empty state if present
    const emptyState = chatMessages.querySelector('.chat-empty');
    if (emptyState) emptyState.remove();

    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
    
    const messageHtml = `
      <div class="chat-message from-user">
        <p>${escapeHtml(content)}</p>
        <span class="chat-time">${time}</span>
      </div>
    `;
    chatMessages.insertAdjacentHTML('beforeend', messageHtml);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  // Add or update bot message in chat UI
  function addBotMessage(content, isStreaming = false) {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return null;

    let botMessage = chatMessages.querySelector('.chat-message.from-bot.is-streaming');
    
    if (!botMessage) {
      const now = new Date();
      const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
      
      const messageHtml = `
        <div class="chat-message from-bot ${isStreaming ? 'is-streaming' : ''}">
          <div class="message-content"></div>
          <span class="chat-time">${time}</span>
        </div>
      `;
      chatMessages.insertAdjacentHTML('beforeend', messageHtml);
      botMessage = chatMessages.querySelector('.chat-message.from-bot.is-streaming') || 
                   chatMessages.querySelector('.chat-message.from-bot:last-child');
    }

    const contentEl = botMessage.querySelector('.message-content');
    if (contentEl) {
      contentEl.innerHTML = parseMarkdown(content);
    }

    chatMessages.scrollTop = chatMessages.scrollHeight;
    return botMessage;
  }

  // Finalize bot message (remove streaming class)
  function finalizeBotMessage() {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;

    const streamingMessage = chatMessages.querySelector('.chat-message.from-bot.is-streaming');
    if (streamingMessage) {
      streamingMessage.classList.remove('is-streaming');
    }
  }

  // Send message to AI
  async function sendMessage(message) {
    if (!message.trim() || isGenerating) return;

    setLoadingState(true);

    // Switch to chat view if on intro
    const introView = document.querySelector('[data-view="intro"]');
    if (introView && !introView.classList.contains('is-hidden')) {
      showChatView();
      // Clear chat messages for new conversation
      const chatMessages = document.getElementById('chatMessages');
      if (chatMessages) chatMessages.innerHTML = '';
    }

    // Add user message to UI
    addUserMessage(message);

    // Clear input
    const { introInput, chatInput } = getSendElements();
    if (introInput) introInput.value = '';
    if (chatInput) chatInput.value = '';

    let fullResponse = '';
    
    try {
      const response = await fetch('../../api/sendMessage.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          message: message,
          conversation_id: activeConversationId
        })
      });

      const reader = response.body.getReader();
      const decoder = new TextDecoder();

      while (true) {
        const { done, value } = await reader.read();
        if (done) break;

        const chunk = decoder.decode(value, { stream: true });
        const lines = chunk.split('\n');

        for (const line of lines) {
          if (line.startsWith('event: ')) {
            const eventType = line.substring(7).trim();
            continue;
          }
          
          if (line.startsWith('data: ')) {
            try {
              const data = JSON.parse(line.substring(6));
              
              if (data.conversation_id && !activeConversationId) {
                activeConversationId = data.conversation_id;
                // Refresh conversation list to show new conversation
                getConversations();
                setTimeout(() => updateActiveConversationUI(), 500);
              }

              if (data.text) {
                fullResponse += data.text;
                addBotMessage(fullResponse, true);
              }

              if (data.message) {
                // Error message
                console.error('API Error:', data.message);
                addBotMessage('Sorry, an error occurred: ' + data.message, false);
              }
            } catch (e) {
              // Ignore JSON parse errors for incomplete chunks
            }
          }
        }
      }

      finalizeBotMessage();
      
      // Refresh conversation list to update order
      getConversations();
      setTimeout(() => updateActiveConversationUI(), 500);

    } catch (error) {
      console.error('Error:', error);
      addBotMessage('Sorry, failed to connect to the AI service. Please try again.', false);
      finalizeBotMessage();
    } finally {
      setLoadingState(false);
    }
  }

  // Handle send button clicks
  document.addEventListener('click', function(e) {
    const sendBtn = e.target.closest('.send-btn');
    if (!sendBtn || isGenerating) return;

    const chatInput = sendBtn.closest('.chat-input');
    if (!chatInput) return;

    const input = chatInput.querySelector('input');
    if (!input) return;

    const message = input.value.trim();
    if (message) {
      sendMessage(message);
    }
  });

  // Handle Enter key on inputs
  document.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter' || isGenerating) return;

    const input = e.target;
    if (!input.matches('.chat-input input')) return;

    e.preventDefault();
    const message = input.value.trim();
    if (message) {
      sendMessage(message);
    }
  });
</script>