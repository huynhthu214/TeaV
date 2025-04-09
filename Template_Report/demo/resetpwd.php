<?php 
    $namePage = "Reset Password";
    include "view/header.php";
?>
<div class="container form-box mt-5 d-none" id="resetPasswordForm">
  <form>
    <h1 class="text-center mb-4">Reset Password</h1>

    <div class="mb-3">
      <label for="newPassword" class="form-label">New Password</label>
      <input type="password" class="form-control" id="newPassword" required />
    </div>

    <div class="mb-3">
      <label for="confirmPassword" class="form-label">Confirm Password</label>
      <input type="password" class="form-control" id="confirmPassword" required />
    </div>

    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
  </form>
</div>


<?php 
    include "view/footer.php";
?>