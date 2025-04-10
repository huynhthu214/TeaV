
<?php 
    $namePage = "Login";
    include "view/header.php";
   /*  require_once 'config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: index.php");
            exit;
        } else {
            echo "<div class='alert alert-danger text-center'>Email hoặc mật khẩu không đúng!</div>";
        }
    } */
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

      <button type="submit" class="btn login-btn">Login</button>

      <div class="text-center mt-3">
        <p>Don't have an account? <a href="signup.php" id="openRegisterModal">Sign Up</a></p>
      </div>
    </form>
  </div>
</div>

<?php 
    include "view/footer.php";
?>
