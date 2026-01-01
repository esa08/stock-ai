<!-- Authentication Section - Sign Up -->
<div class="auth-section">
  <div class="auth-card">
    <h2 class="auth-title">Sign up with free trial</h2>
    <p class="auth-subtitle">Empower your stock analysis, sign up for a free account today</p>
    
    <form class="auth-form" action="" method="POST">
      <div class="form-group">
        <label for="username">Username*</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required>
      </div>
      
      <div class="form-group">
        <label for="password">Password*</label>
        <div class="password-input">
          <input type="password" id="password" name="password" placeholder="Enter password" required>
          <button type="button" class="toggle-password" onclick="togglePassword('password')">
            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
          </button>
        </div>
      </div>
      
      <div class="form-group">
        <label for="security_question">Security Question*</label>
        <select id="security_question" name="security_question" required>
          <option value="" disabled selected>Select a security question</option>
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
