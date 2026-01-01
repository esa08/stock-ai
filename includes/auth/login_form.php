<!-- Authentication Section - Login -->
<div class="auth-section">
  <div class="auth-card">
    <h2 class="auth-title">Welcome back</h2>
    <p class="auth-subtitle">Login to continue your stock analysis journey</p>
    
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
