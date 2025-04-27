<?php
session_start();
$namePage = "Forgot Password";
include "view/header.php";

require "PHPMailer-master/src/PHPMailer.php";
require "PHPMailer-master/src/SMTP.php"; 
require "PHPMailer-master/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Please enter your email';
    } else {
        $query = "SELECT * FROM account WHERE Email='$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 0) {
            $error = 'Email does not exist in the system';
        } else {
            $code = rand(100000, 999999);

            $_SESSION['email_reset'] = $email;
            $_SESSION['code_reset'] = $code;
            $_SESSION['code_reset_time'] = time();

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'minhthuhuynh23@gmail.com'; // tài khoản Gmail
                $mail->Password = 'kapendjgusnxwczc';         // App Password
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                $mail->CharSet = 'UTF-8';
                $mail->setFrom('minhthuhuynh23@gmail.com', 'TeaV Shop');
                $mail->addAddress($email); // gửi tới email người dùng nhập
                $mail->isHTML(true);
                $mail->Subject = 'Password reset verification code:';
                $mail->Body    = 'Your verification code is: <b>' . $code . '</b>';

                $mail->send();

                // Gửi thành công, chuyển tới confirm-mail.php
                header('Location: confirm-mail.php');
                exit();
            } catch (Exception $e) {
                $error = "Gửi mail thất bại: {$mail->ErrorInfo}";
            }
        }
    }
}
?>

<div class="container form-box-forgot mt-5">
    <form id="sendCodeForm" method="post">
        <h1 class="forgotpwd text-center mb-4">Forgot Password</h1>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required />
        </div>
        <?php
           if (!empty($error)) {
              echo "<div class='alert alert-danger'>$error</div>";
           }
      ?>
        <button type="submit" class="btn btn-outline-primary w-100">Send Code</button>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Back to login</a>
        </div>
    </form>
</div>

<?php 
include "view/footer.php";
?>