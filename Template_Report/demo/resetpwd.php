<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $password = trim($_POST['new_password']); 
    $confirm_pass = trim($_POST['confirm_password']); 
    $email = $_SESSION['email_reset'];

    if($password !== $confirm_pass){
        $error = "Passwords don't match!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 

        $sql = "UPDATE account SET Password = ? WHERE Email = ?";
        $stm = $conn->prepare($sql);
        
        if (!$stm) {
            $error = "Something went wrong. Please try again.";
        } else {
            $stm->bind_param('ss', $hashedPassword, $email); 

            if ($stm->execute()) {
                // Xóa session
                unset($_SESSION['email_reset']);
                unset($_SESSION['code_reset']);
                unset($_SESSION['code_reset_time']);
                
                // Redirect về login
                header('Location: login.php?message=reset_success');
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
$namePage = "Đổi mật khẩu";
include "view/header.php";
?>

<div class="container form-box-reset mt-5">
    <form method="post">
        <h1 class="reset text-center mb-4">Đổi mật khẩu</h1>
        <div class="mb-3">
            <label for="new_password" class="form-label">Mật khẩu mới</label>
            <input type="password" class="form-control" name="new_password" required />
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
            <input type="password" class="form-control" name="confirm_password" required />
        </div>
        <button type="submit" class="btn btn-outline-primary">Đổi mật khẩu</button>
    </form>
</div>

<?php include "view/footer.php"; ?>
