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
    <ul class="conversation-list">
      <li class="conversation-row">
        <button type="button" class="conversation-item">Create Html Game Environment...</button>
        <button type="button" class="conversation-remove" aria-label="Remove conversation">
          <i class="fa-solid fa-trash"></i>
        </button>
      </li>
      <li class="conversation-row">
        <button type="button" class="conversation-item">Apply To Leave For Emergency</button>
        <button type="button" class="conversation-remove" aria-label="Remove conversation">
          <i class="fa-solid fa-trash"></i>
        </button>
      </li>
      <li class="conversation-row">
        <button type="button" class="conversation-item">What Is UI UX Design?</button>
        <button type="button" class="conversation-remove" aria-label="Remove conversation">
          <i class="fa-solid fa-trash"></i>
        </button>
      </li>
      <li class="conversation-row">
        <button type="button" class="conversation-item">Create POS System</button>
        <button type="button" class="conversation-remove" aria-label="Remove conversation">
          <i class="fa-solid fa-trash"></i>
        </button>
      </li>
      <li class="conversation-row">
        <button type="button" class="conversation-item">What Is UX Audit?</button>
        <button type="button" class="conversation-remove" aria-label="Remove conversation">
          <i class="fa-solid fa-trash"></i>
        </button>
      </li>
      <li class="conversation-row">
        <button type="button" class="conversation-item">Create Chatbot GPT...</button>
        <button type="button" class="conversation-remove" aria-label="Remove conversation">
          <i class="fa-solid fa-trash"></i>
        </button>
      </li>
      <li class="conversation-row">
        <button type="button" class="conversation-item">How Chat GPT Work?</button>
        <button type="button" class="conversation-remove" aria-label="Remove conversation">
          <i class="fa-solid fa-trash"></i>
        </button>
      </li>
    </ul>
  </div>

  <div class="sidebar-footer">
    <div class="user-info">
      <span class="user-icon" aria-hidden="true">
        <i class="fa-solid fa-user"></i>
      </span>
      <span class="user-name"><?php echo $_SESSION['username']; ?></span>
    </div>
    <button class="logout-btn" type="button" id="logoutBtn">Logout</button>
  </div>
</aside>

<?php
// echo json_encode($_SESSION);
?>
<script>
  document.getElementById('logoutBtn').addEventListener('click', function () {
    fetch('../../function/logout.php', { method: 'POST' })
    .then(() => window.location.href = 'index.php');
  });
</script>