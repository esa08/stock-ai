<link rel="stylesheet" href="assets/chatbot.css">
<div class="container chatbot-layout">
  <?php include 'includes/chatbot/sidebar.php'; ?>
  <?php include 'includes/chatbot/main.php'; ?>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var main = document.querySelector('.chatbot-main');
    if (!main) {
      return;
    }

    var introView = main.querySelector('[data-view="intro"]');
    var chatView = main.querySelector('[data-view="chat"]');
    var hasChats = !!main.querySelector('.chat-message');

    if (hasChats) {
      if (introView) introView.classList.add('is-hidden');
      if (chatView) chatView.classList.remove('is-hidden');
    } else {
      if (chatView) chatView.classList.add('is-hidden');
      if (introView) introView.classList.remove('is-hidden');
    }
  });
</script>