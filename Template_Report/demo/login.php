
<?php 
    session_start();
    $namePage = "Đăng nhập";
    
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
          $error = 'Vui lòng nhập email.';
      }
      else if (empty($pass)) {
          $error = 'Vui lòng nhập mật khẩu.';
      }
      else if (strlen($pass) < 8) {
          $error = 'Mật khẩu phải có ít nhất 8 ký tự';
      } else {
          $userData = login($email, $pass);
            if ($userData) {
                $_SESSION['email'] = $userData['Email'];

                // Nếu có giá trị return, chuyển hướng về lại trang đó
                if (isset($_GET['return'])) {
                    $returnUrl = urldecode($_GET['return']);
                    header("Location: $returnUrl");
                } else {
                    // Không có return thì xử lý như bình thường
                    if ($userData['Type'] === 'Admin') {
                        header('Location: ../admin/dashboard.php');
                    } else {
                        header('Location: index.php');
                    }
                }
                exit();
          } else {
              $error = 'Email hoặc mật khẩu không đúng.';  // Nếu đăng nhập không thành công
          }
      }
  }
  include "view/header.php";
?>


<!-- Login -->
<div class="container form-box mt-5">
    <form method="post">
      <h1 class="login text-center mb-4">Đăng nhập</h1>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Nhập email" />
      </div>

    <div class="mb-3">
    <label for="password" class="form-label">Mật khẩu</label>
    <div class="input-group">
        <input type="password" class="form-control" id="password" name="pass" placeholder="Nhập mật khẩu">
        <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('password', 'toggleEye')">
        <i class="fa fa-eye text-muted small" id="toggleEye"></i>
        </span>
    </div>
    </div>

      <div class="mb-3 text-end">
        <a href="forgotpwd.php" id="forgotPassword" class="text-decoration-none">Quên mật khẩu?</a>
      </div>
      <?php
           if (!empty($error)) {
              echo "<div class='alert alert-danger'>$error</div>";
           }
      ?>
      <button type="submit" class="btn btn-outline-primary">Đăng nhập</button>

      <div class="text-center mt-3">
        <p>Chưa có tài khoản?<a href="signup.php" id="openRegisterModal">Đăng ký ngay</a></p>
      </div>
    </form>
  </div>
</div>

<?php 
    include "view/footer.php";
?>
