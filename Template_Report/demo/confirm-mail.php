<?php 
session_start();
$namePage = "Confirm Email";
include "view/header.php";

if (!isset($_SESSION['email_reset']) || !isset($_SESSION['code_reset']) || !isset($_SESSION['code_reset_time'])) {
  header('Location: forgotpwd.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $user_code = trim($_POST['code']);
  $current_time = time();

  if ($current_time - $_SESSION['code_reset_time'] > 60) {
      $error = "Verification code has expired. Please request a new one.";
      unset($_SESSION['code_reset']);
      unset($_SESSION['code_reset_time']);
      unset($_SESSION['email_reset']);
  } elseif ($user_code == $_SESSION['code_reset']) {
      header('Location: resetpwd.php');
      exit();
  } else {
      // Mã sai
      $error = "Verification code is incorrect. Please try again.";
  }
}
?>

<div class="container form-box-confirmmail mt-5">
    <form method="post">
        <h1 class="confirmmail text-center mb-4">Please check your inbox</h1>
        <p>Please enter the verification code we just sent to your email</p>
        <div class="mb-3">
            <label for="code" class="form-label">Code</label>
            <input type="text" class="form-control" id="code" name="code" required />
        </div>
        <?php
           if (!empty($error)) {
              echo "<div class='alert alert-danger'>$error</div>";
           }
        ?>
        <button type="submit" class="btn btn-outline-primary w-100">Continue</button>
        <div class="text-center mt-3">
            <a href="forgotpwd.php" class="text-decoration-none">Resend code</a>
        </div>
    </form>
</div>

<?php 
include "view/footer.php";
?>
