
<?php 
    $namePage = "Login";
    include "view/header.php";
?>
<!-- Login -->
<div class="container form-box mt-5">
    <form method="post">
      <h1 class="login text-center mb-4">Login</h1>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" placeholder="Enter your email" />
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" placeholder="Enter your password" />
      </div>

      <div class="mb-3 text-end">
        <a href="forgotpwd.php" id="forgotPassword" class="text-decoration-none">Forgot Password?</a>
      </div>

      <button type="submit" class="btn btn-primary login-btn">Login</button>

      <div class="text-center mt-3">
        <p>Don't have an account? <a href="signup.php" id="openRegisterModal">Sign Up</a></p>
      </div>
    </form>
  </div>
</div>

<?php 
    include "view/footer.php";
?>
