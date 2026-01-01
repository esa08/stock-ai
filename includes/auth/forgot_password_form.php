<!-- Authentication Section - Forgot Password -->
<div class="auth-section">
  <div class="auth-card">
    <h2 class="auth-title">Reset your password</h2>
    <p class="auth-subtitle">Answer your security question to reset your password</p>
    
    <form class="auth-form" action="" method="POST">
      <div class="form-group">
        <label for="username">Username*</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required>
      </div>
      
      <div class="form-group">
        <label for="security_question">Security Question*</label>
        <select id="security_question" name="security_question" required>
          <option value="" disabled selected>Select your security question</option>
          <option value="pet">Siapa nama hewan peliharaan pertama Anda?</option>
          <option value="city">Di kota mana Anda dilahirkan?</option>
          <option value="school">Apa nama sekolah dasar Anda?</option>
          <option value="friend">Siapa nama teman masa kecil Anda?</option>
          <option value="food">Apa makanan favorit Anda?</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="security_answer">Security Answer*</label>
        <input type="text" id="security_answer" name="security_answer" placeholder="Enter your answer" required>
      </div>
      
      <div class="form-group">
        <label for="new_password">New Password*</label>
        <div class="password-input">
          <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
          <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
          </button>
        </div>
      </div>
      
      <div class="form-group">
        <label for="confirm_password">Confirm Password*</label>
        <div class="password-input">
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
          <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
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
