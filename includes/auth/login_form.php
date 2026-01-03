<!-- Authentication Section - Login -->
<div class="auth-section">
  <div class="auth-card">
    <h2 class="auth-title">Welcome back</h2>
    <p class="auth-subtitle">Login to continue your stock analysis journey</p>
    <?php if (!empty($error) || !empty($success)) : ?>
      <div class="auth-alert <?php echo !empty($error) ? 'auth-alert-error' : 'auth-alert-success'; ?>">
        <?php echo nl2br(htmlspecialchars(!empty($error) ? $error : $success)); ?>
      </div>
    <?php endif; ?>
    
    <form class="auth-form" action="" method="POST">
      <div class="form-group">
        <label for="username">Username*</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php echo htmlspecialchars($form_values['username'] ?? ''); ?>" required>
      </div>
      
      <div class="form-group">
        <label for="password">Password*</label>
        <div class="password-input">
          <input type="password" id="password" name="password" placeholder="Enter password" value="<?php echo htmlspecialchars($form_values['password'] ?? ''); ?>" required>
          <button type="button" class="toggle-password" onclick="togglePassword('password', this)" data-eye="assets/images/eye.svg" data-eye-slash="assets/images/eye-slash.svg">
            <img class="eye-icon" src="assets/images/eye.svg" alt="Toggle password visibility">
          </button>
        </div>
      </div>
      
      <div class="form-options">
        <a href="?page=forgot_password" class="forgot-link">Forgot password?</a>
      </div>
      
      <button type="submit" class="btn-submit">Login</button>
    </form>
    
    <p class="login-link">
      Don't have an account? <a href="?page=register">Sign up</a>
    </p>
  </div>
</div>
