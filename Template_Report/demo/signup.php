<?php 
    $namePage = "Sign up";
    include "view/header.php";
?>

    <!-- Register -->
    <div class="modal-body">
            <form>
              <div class="mt-5">
                <h1 class="sign-up">Sign up</h1>
                <label for="full-name" class="form-label">Full name</label>
                <input
                  type="text"
                  class="form-control"
                  id="full-name"
                  placeholder="Enter your full name"
                />
              </div>
              <div class="mb-3">
                <label for="new-email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="new-email"
                  placeholder="Enter your email"
                />
              </div>
              <div class="mb-3">
                <label for="phone-number" class="form-label">Phone number</label>
                <input
                  type="text"
                  class="form-control"
                  id="phone-number"
                  placeholder="Enter your phone number"
                />
              </div>
              <div class="mb-3">
                <label for="date-of-birth" class="form-label">Date of birth</label>
                <input
                  type="date"
                  class="form-control"
                  id="date-of-birth"
                />
              </div>
              <div class="mb-3">
                <label for="new-password" class="form-label">Password</label>
                <input
                  type="new-password"
                  class="form-control"
                  id="new-password"
                  placeholder="Create a password"
                />
              </div>
              <div class="mb-3">
                <label for="confirm-password" class="form-label">Confirm password</label>
                <input
                  type="password"
                  class="form-control"
                  id="confirm-password"
                  placeholder="Confirm password"
                />
              </div>
              <div class="mb-3">
            <input type="checkbox" class="form-check-input" id="terms" />
            <label class="form-check-label" for="terms">
            By continuing to sign up, I confirm that I have read and agree to <br><a href="term.php" target="_blank">Terms&Conditions</a> of TeaV.
            </label>
        </div>
              <button type="submit" class="btn btn-primary signup-btn">Sign up</button>
              </button>
              <div class="text-center mt-3">
                <p>
                  Already have an account?
                  <a href="login.php" id="openLoginModal">Login</a>
                </p>
              </div>
            </form>
<?php 
    include "view/footer.php"
?>