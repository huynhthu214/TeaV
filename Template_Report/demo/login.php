
<?php 
    session_start();
    $namePage = "Login";
    
    $conn = mysqli_connect("localhost", "root", "", "teav_shop1");

    if (!$conn) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }
    
    function login($email, $password) { 
      global $conn;

      $sql = "SELECT * FROM account WHERE Email = ?";
      $stm = $conn->prepare($sql);
      if (!$stm) {
          return false;
      }

      $stm->bind_param('s', $email);
      if (!$stm->execute()) {
          return false;
      }

      $result = $stm->get_result();
      if ($result->num_rows !== 1) {
          return false;
      }

      $data = $result->fetch_assoc();
      if (!isset($data['Password'])) {
          return false;
      }
      $hashed = $data['Password'];

      if (!password_verify($password, $hashed)) {
          return false;
      }

      return $data; 
  }


  $error = '';
  $email = '';
  $pass = '';

  if (isset($_POST['email']) && isset($_POST['pass'])) {
      $email = trim($_POST['email']);
      $pass = trim($_POST['pass']);
      
      if (empty($email)) {
          $error = 'Please enter your email';
      }
      else if (empty($pass)) {
          $error = 'Please enter your password';
      }
      else if (strlen($pass) < 8) {
          $error = 'Password must have at least 8 characters';
      } else {
          $userData = login($email, $pass);
          if ($userData) {  // Nếu trả về dữ liệu người dùng
              // Thiết lập session
              $_SESSION['email'] = $userData['Email'];

              if ($userData['Type'] === 'Admin') {
                  header('Location: dashboard.php');
              } else {
                  header('Location: index.php');
              }
              exit();
          } else {
              $error = 'Invalid email or password';  // Nếu đăng nhập không thành công
          }
      }
  }
  include "view/header.php";
?>


<!-- Login -->
<div class="container form-box mt-5">
    <form method="post">
      <h1 class="login text-center mb-4">Login</h1>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email" />
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="pass" placeholder="Enter your password" />
      </div>

      <div class="mb-3 text-end">
        <a href="forgotpwd.php" id="forgotPassword" class="text-decoration-none">Forgot Password?</a>
      </div>
      <?php
           if (!empty($error)) {
              echo "<div class='alert alert-danger'>$error</div>";
           }
      ?>
      <button type="submit" class="btn btn-outline-primary">Login</button>

      <div class="text-center mt-3">
        <p>Don't have an account? <a href="signup.php" id="openRegisterModal">Sign Up</a></p>
      </div>
    </form>
  </div>
</div>

<?php 
    include "view/footer.php";
?>
