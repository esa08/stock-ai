<!-- Authentication Section - Forgot Password -->
<div class="auth-section">
  <div class="auth-card">
    <h2 class="auth-title">Reset your password</h2>
    <p class="auth-subtitle">Answer your security question to reset your password</p>
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
        <label for="security_question">Security Question*</label>
        <select id="security_question" name="security_question" required>
          <option value="" disabled <?php echo empty($form_values['security_question']) ? 'selected' : ''; ?>>Select your security question</option>
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
      
      <div class="form-group">
        <label for="new_password">New Password*</label>
        <div class="password-input">
          <input type="password" id="new_password" name="new_password" placeholder="Enter new password" value="<?php echo htmlspecialchars($form_values['new_password'] ?? ''); ?>" required>
          <button type="button" class="toggle-password" onclick="togglePassword('new_password', this)" data-eye="assets/images/eye.svg" data-eye-slash="assets/images/eye-slash.svg">
            <img class="eye-icon" src="assets/images/eye.svg" alt="Toggle password visibility">
          </button>
        </div>
      </div>
      
      <div class="form-group">
        <label for="confirm_password">Confirm Password*</label>
        <div class="password-input">
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" value="<?php echo htmlspecialchars($form_values['confirm_password'] ?? ''); ?>" required>
          <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', this)" data-eye="assets/images/eye.svg" data-eye-slash="assets/images/eye-slash.svg">
            <img class="eye-icon" src="assets/images/eye.svg" alt="Toggle password visibility">
          </button>
        </div>
      </div>
      
      <button type="submit" class="btn-submit">Reset Password</button>
    </form>
    
    <p class="login-link">
      Remember your password? <a href="index.php">Login</a>
    </p>
  </div>
</div>
