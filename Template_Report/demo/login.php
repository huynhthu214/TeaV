
<?php 
    session_start();
    $namePage = "Login";
    include "view/header.php";
    
    $conn = mysqli_connect("localhost", "root", "", "teav_shop1");

    if (!$conn) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }
    
    function login($email, $password) { 
      global $conn;
  
      $sql = "SELECT * FROM account WHERE email = ?";
      $stm = $conn->prepare($sql);
      if (!$stm) {
          return 'Can not login, please contact your admin (prepare failed)';
      }
  
      $stm->bind_param('s', $email);
      if (!$stm->execute()) {
          return 'Can not login, please contact your admin (execute failed)';
      }
  
      $result = $stm->get_result();
      if ($result->num_rows !== 1) {
          return 'Can not login, invalid username or password';
      }
  
      $data = $result->fetch_assoc();
      $hashed = $data['password'];
  
      if (!password_verify($password, $hashed)) {
          return 'Can not login, invalid password';
      }
  
      return $data; 
  }
  

    $error = '';
    $email = '';
    $pass = '';

    if (isset($_POST['email']) && isset($_POST['pass'])) {
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        
        if (empty($email)) {
            // echo 'email error';
            $error = 'Please enter your email';
        }
        else if (empty($pass)) {
            // echo 'password error';
            $error = 'Please enter your password';
        }
        else if (strlen($pass) < 8) {
            // echo 'length pass error';
            $error = 'Password must have at least 8 characters';
        }else if (login($email, $pass)) {
          $email = $conn->real_escape_string($email);
          $sql = "SELECT * FROM account WHERE Email = '$email' LIMIT 1";
          $result = $conn->query($sql);
        
          if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
        
            // Thiết lập session
            $_SESSION['email'] = $row['Email'];
            $_SESSION['avatar'] = $row['Avatar']; // Phải đúng tên cột Avatar trong DB
        
            // Điều hướng
            if ($row['Type'] === 'Admin') {
              header('Location: dashboard.php');
            } else {
              header('Location: index.php');
            }
            exit();
          }
        }
    }
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
