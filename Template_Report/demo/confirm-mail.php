<?php  
session_start();

if (!isset($_SESSION['email_reset']) || !isset($_SESSION['code_reset']) || !isset($_SESSION['code_reset_time'])) {
    header('Location: forgotpwd.php');
    exit();
}

$error = "";  // Đảm bảo biến $error được khởi tạo

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_code = trim($_POST['code']);
    $current_time = time();

    // Kiểm tra mã xác nhận
    if (empty($user_code)) {
        $error = "Please enter the verification code.";
    } elseif ($current_time - $_SESSION['code_reset_time'] > 60) {
        $error = "Verification code has expired. Please request a new one.";
        unset($_SESSION['code_reset']);
        unset($_SESSION['code_reset_time']);
        unset($_SESSION['email_reset']);
    } elseif ($user_code == $_SESSION['code_reset']) {
        header('Location: resetpwd.php');
        exit();
    } else {
        $error = "Verification code is incorrect. Please try again.";
    }
}

$namePage = "Confirm Email";
include "view/header.php";
?>

<div class="container form-box-confirmmail mt-5">
    <form method="post">
        <h1 class="confirmmail text-center mb-4">Please check your inbox</h1>
        <p>Please enter the verification code we just sent to your email</p>
        <div class="mb-3">
            <label for="code" class="form-label">Code</label>
            <input type="text" class="form-control" id="code" name="code" />
        </div>
        <?php
           // Kiểm tra và hiển thị lỗi
           if (!empty($error)) {
              echo "<div class='alert alert-danger'>$error</div>";
           }
           if (!empty($success_message)) {
              echo "<div class='alert alert-success'>$success_message</div>";
           }
        ?>
        <button type="submit" class="btn btn-outline-primary w-100">Continue</button>
        <div class="text-center mt-3">
            <a href="#" class="text-decoration-none">Resend code</a>
        </div>
    </form>
</div>

<?php 
include "view/footer.php";
?>
