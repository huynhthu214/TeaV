<?php
session_start();
$namePage = "Reset Password";
include "view/header.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verification_code'])) {
    $code = $_POST['verification_code'];
    if ($code != $_SESSION['verification_code']) {
        echo "<div class='alert alert-danger text-center'>Incorrect verification code!</div>";
        exit;
    }
}
?>

<div class="container form-box-reset mt-5">
    <form method="post">
        <h1 class="reset text-center mb-4">Reset Password</h1>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" name="new_password" required />
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" required />
        </div>
        <button type="submit" class="btn btn-outline-primary">Changing Password</button>
    </form>
</div>

<?php include "view/footer.php"; ?>
