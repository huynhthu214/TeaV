<?php
session_start();
$namePage = "Reset Password";
include "view/header.php";

// Kiểm tra mã xác minh
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verification_code'])) {
    $code = $_POST['verification_code'];
    if ($code != $_SESSION['verification_code']) {
        echo "<div class='alert alert-danger text-center'>Incorrect verification code!</div>";
        exit;
    }
}
?>

<!-- Form đặt lại mật khẩu -->
<div class="container form-box mt-5">
    <form method="post" action="resetpwd_process.php">
        <h1 class="text-center mb-4">Reset Password</h1>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" name="new_password" required />
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" required />
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
</div>

<?php include "view/footer.php"; ?>
