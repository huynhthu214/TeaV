<?php 
    $namePage = "Sign up";
    include "view/header.php";
?>

    <!-- Register -->
    <div class="container form-box-sign mt-5">
    <form method="post">
      <h1 class="signup text-center mb-4">Sign up</h1>

      <div class="mb-3">
        <label for="full-name" class="form-label">Full name</label>
        <input type="text" class="form-control" id="full-name" placeholder="Enter your full name" />
      </div>

      <div class="mb-3">
        <label for="new-email" class="form-label">Email</label>
        <input type="email" class="form-control" id="new-email" placeholder="Enter your email" />
      </div>

      <div class="mb-3">
        <label for="phone-number" class="form-label">Phone</label>
        <input type="text" class="form-control" id="phone-number" placeholder="Enter your phone number" />
      </div>

      <div class="mb-3">
        <label for="date-of-birth" class="form-label">Date of birth</label>
        <input type="date" class="form-control" id="date-of-birth" />
      </div>

      <div class="mb-3">
        <label for="new-password" class="form-label">Password</label>
        <input type="password" class="form-control" id="new-password" placeholder="Create a password" />
      </div>

      <div class="mb-3">
        <label for="confirm-password" class="form-label">Confirm password</label>
        <input type="password" class="form-control" id="confirm-password" placeholder="Confirm password" />
      </div>

      <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="terms" />
        <label class="form-check-label" for="terms">
          By continuing to sign up, I confirm that I have read and agree to
          <a href="term.php" target="_blank">Terms & Conditions</a> of TeaV.
        </label>
      </div>

      <button type="submit" class="btn btn-primary signup-btn">Sign up</button>

      <div class="text-center mt-3">
        <p>
          Already have an account?
          <a href="login.php" id="openLoginModal">Login</a>
        </p>
      </div>
    </form>
  </div>

<?php 
    include "view/footer.php"
?>