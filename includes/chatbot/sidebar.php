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
      html += `
        <div class="chat-message ${roleClass}">
          <p>${escapeHtml(msg.content)}</p>
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
</script>