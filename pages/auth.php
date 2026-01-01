<link rel="stylesheet" href="assets/auth.css">
<div class="container">
  <?php 
    include 'includes/auth/hero.php';

    $page = $_GET["page"] ?? null;
    switch ($page) {
      case 'register':
        include 'includes/auth/signup_form.php';
        break;
      case 'forgot_password':
        include 'includes/auth/forgot_password_form.php';
        break;
      default:
        include 'includes/auth/login_form.php';
        break;
    }

  ?>
</div>