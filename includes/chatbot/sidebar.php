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
      if (data.success && Array.isArray(data.data)) {
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
      }
      conversationList.innerHTML = item;
    })
    .catch(error => {
      console.error('Error:', error);
    });
  }

  getConversations();
</script>