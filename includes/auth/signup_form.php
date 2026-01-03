<!-- Authentication Section - Sign Up -->
<div class="auth-section">
  <div class="auth-card">
    <h2 class="auth-title">Sign up with free trial</h2>
    <p class="auth-subtitle">Empower your stock analysis, sign up for a free account today</p>
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
      
      <div class="form-group">
        <label for="security_question">Security Question*</label>
        <select id="security_question" name="security_question" required>
          <option value="" disabled <?php echo empty($form_values['security_question']) ? 'selected' : ''; ?>>Select a security question</option>
          <option value="pet" <?php echo ($form_values['security_question'] ?? '') === 'pet' ? 'selected' : ''; ?>>Siapa nama hewan peliharaan pertama Anda?</option>
          <option value="city" <?php echo ($form_values['security_question'] ?? '') === 'city' ? 'selected' : ''; ?>>Di kota mana Anda dilahirkan?</option>
          <option value="school" <?php echo ($form_values['security_question'] ?? '') === 'school' ? 'selected' : ''; ?>>Apa nama sekolah dasar Anda?</option>
          <option value="friend" <?php echo ($form_values['security_question'] ?? '') === 'friend' ? 'selected' : ''; ?>>Siapa nama teman masa kecil Anda?</option>
          <option value="food" <?php echo ($form_values['security_question'] ?? '') === 'food' ? 'selected' : ''; ?>>Apa makanan favorit Anda?</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="security_answer">Security Answer*</label>
        <input type="text" id="security_answer" name="security_answer" placeholder="Enter your answer" value="<?php echo htmlspecialchars($form_values['security_answer'] ?? ''); ?>" required>
      </div>
      
      <p class="terms-text">
        By registering for an account, you are consenting to our 
        <a href="#">Terms of Service</a> and confirming that you have reviewed and accepted the 
        <a href="#">Global Privacy Statement</a>.
      </p>
      
      <button type="submit" class="btn-submit">Get started free</button>
    </form>
    
    <p class="login-link">
      Already have an account? <a href="index.php">Login</a>
    </p>
  </div>
</div>
