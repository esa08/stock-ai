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
      } else {
        alert(data.message || 'Failed to clear all conversations.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to clear all conversations.');
    });
  });
</script>