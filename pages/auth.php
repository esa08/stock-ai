<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once 'config/db.php';

function normalize_input($value) {
  return trim((string) $value);
}

$page = $_GET['page'] ?? null;
$action = $page ?? 'login';
$error = '';
$success = '';
$form_values = [
  'username' => '',
  'password' => '',
  'security_question' => '',
  'security_answer' => ''
];

if (isset($_GET['reset']) && $action === 'login') {
  $success = 'Password updated. Please login with your new password.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  switch ($action) {
    case 'register':
      $username = normalize_input($_POST['username'] ?? '');
      $password = (string) ($_POST['password'] ?? '');
      $security_question = normalize_input($_POST['security_question'] ?? '');
      $security_answer = normalize_input($_POST['security_answer'] ?? '');
      $form_values = [
        'username' => $username,
        'password' => $password,
        'security_question' => $security_question,
        'security_answer' => $security_answer
      ];

      $errors = [];

      if ($username === '' || $password === '' || $security_question === '' || $security_answer === '') {
        $errors[] = 'Please fill in all required fields.';
      }

      if ($username !== '' && mb_strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
      }

      if ($password !== '' && mb_strlen($password) < 5) {
        $errors[] = 'Password must be at least 5 characters.';
      }

      if ($security_answer !== '' && mb_strlen($security_answer) < 3) {
        $errors[] = 'Security answer must be at least 3 characters.';
      }

      if (!empty($errors)) {
        $error = implode("\n", $errors);
        break;
      }

      $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
      if (!$stmt) {
        $error = 'Server error. Please try again.';
        break;
      }
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $stmt->store_result();

      if ($stmt->num_rows > 0) {
        $error = 'Username already exists.';
        $stmt->close();
        break;
      }
      $stmt->close();

      $password_hash = password_hash($password, PASSWORD_DEFAULT);
      $answer_hash = password_hash($security_answer, PASSWORD_DEFAULT);
      $stmt = $mysqli->prepare('INSERT INTO users (username, password_hash, security_question, security_answer_hash) VALUES (?, ?, ?, ?)');
      if (!$stmt) {
        $error = 'Server error. Please try again.';
        break;
      }
      $stmt->bind_param('ssss', $username, $password_hash, $security_question, $answer_hash);
      if ($stmt->execute()) {
        $_SESSION['is_login'] = true;
        $_SESSION['username'] = $username;
        $stmt->close();
        header('Location: index.php');
        exit;
      }
      $stmt->close();
      $error = 'Failed to create account.';
      break;

    case 'forgot_password':
      $username = normalize_input($_POST['username'] ?? '');
      $security_question = normalize_input($_POST['security_question'] ?? '');
      $security_answer = normalize_input($_POST['security_answer'] ?? '');
      $new_password = (string) ($_POST['new_password'] ?? '');
      $confirm_password = (string) ($_POST['confirm_password'] ?? '');
      $form_values = [
        'username' => $username,
        'security_question' => $security_question,
        'security_answer' => $security_answer,
        'new_password' => $new_password,
        'confirm_password' => $confirm_password
      ];

      if ($username === '' || $security_question === '' || $security_answer === '' || $new_password === '' || $confirm_password === '') {
        $error = 'Please fill in all required fields.';
        break;
      }

      if ($new_password !== $confirm_password) {
        $error = 'Password confirmation does not match.';
        break;
      }

      $stmt = $mysqli->prepare('SELECT id, security_question, security_answer_hash FROM users WHERE username = ? LIMIT 1');
      if (!$stmt) {
        $error = 'Server error. Please try again.';
        break;
      }
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $result = $stmt->get_result();
      $user = $result ? $result->fetch_assoc() : null;
      $stmt->close();

      if (!$user || $user['security_question'] !== $security_question) {
        $error = 'Invalid credentials.';
        break;
      }

      if (!password_verify($security_answer, $user['security_answer_hash'])) {
        $error = 'Invalid credentials.';
        break;
      }

      $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
      $stmt = $mysqli->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
      if (!$stmt) {
        $error = 'Server error. Please try again.';
        break;
      }
      $stmt->bind_param('si', $new_hash, $user['id']);
      if ($stmt->execute()) {
        $stmt->close();
        header('Location: index.php?reset=1');
        exit;
      }
      $stmt->close();
      $error = 'Failed to reset password.';
      break;

    case 'login':
    default:
      $username = normalize_input($_POST['username'] ?? '');
      $password = (string) ($_POST['password'] ?? '');
      $form_values = [
        'username' => $username,
        'password' => $password
      ];

      if ($username === '' || $password === '') {
        $error = 'Please fill in all required fields.';
        break;
      }

      $stmt = $mysqli->prepare('SELECT id, password_hash FROM users WHERE username = ? LIMIT 1');
      if (!$stmt) {
        $error = 'Server error. Please try again.';
        break;
      }
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $result = $stmt->get_result();
      $user = $result ? $result->fetch_assoc() : null;
      $stmt->close();

      if (!$user || !password_verify($password, $user['password_hash'])) {
        $error = 'Invalid username or password.';
        break;
      }

      $_SESSION['is_login'] = true;
      $_SESSION['username'] = $username;
      header('Location: index.php');
      exit;
  }
}
?>
<link rel="stylesheet" href="assets/auth.css">
<div class="container">
  <?php 
    include 'includes/auth/hero.php';

    switch ($action) {
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
<script>
  function togglePassword(inputId, buttonEl) {
    var input = document.getElementById(inputId);
    if (!input) {
      return;
    }
    input.type = input.type === 'password' ? 'text' : 'password';

    if (!buttonEl) {
      return;
    }
    var img = buttonEl.querySelector('img');
    if (!img) {
      return;
    }
    var eye = buttonEl.getAttribute('data-eye');
    var eyeSlash = buttonEl.getAttribute('data-eye-slash');
    if (!eye || !eyeSlash) {
      return;
    }
    img.src = input.type === 'password' ? eye : eyeSlash;
  }
</script>