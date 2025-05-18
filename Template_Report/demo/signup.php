<?php 
    session_start();

    $conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

function generateRandomString($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';

  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[random_int(0, $charactersLength - 1)];
  }

  return $randomString;
}

function signup($full_name, $email, $phone, $pass, $dob, $address) {
  global $conn;

  $sql = "SELECT COUNT(*) FROM Account WHERE Email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $count = $result->fetch_array()[0];
  $stmt->close();

  if ($count > 0) {
      return "Email already exists";
  }
  $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

  $insert_sql = "INSERT INTO account 
            (FullName, Email, PhoneNumber, Password, DateOfBirth, Address, CreatedDate, Type, IsActive)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 'Customer', 'Yes')";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param('ssssss', $full_name, $email, $phone, $hashed_pass, $dob, $address);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $error = $stmt->error;
            $stmt->close();
            return "Error: " . $error;
        }
    }

    $error = '';
    $full_name = '';
    $email = '';
    $phone = '';
    $dob = '';
    $pass = '';
    $pass_confirm = '';
    $address = '';

    if (isset($_POST['full']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['dob']) && isset($_POST['address']) && isset($_POST['pass']) && isset($_POST['pass-confirm']))
    {
        $full_name = $_POST['full'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dob = $_POST['dob'];
        $address = $_POST['address'];
        $pass = $_POST['pass'];
        $pass_confirm = $_POST['pass-confirm'];

        if (empty($full_name)) {
            $error = 'Vui lòng nhập họ và tên.';
        }
        else if (empty($email)) {
            $error = 'Vui lòng nhập email.';
        }
        else if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
          $error = 'Email không hợp lệ.';
        }
        else if (empty($phone)) {
            $error = 'Vui lòng nhập số điện thoại.';
        }
        else if (empty($dob)) {
            $error = 'Vui lòng chọn ngày sinh.';
        }
        else if (empty($pass)) {
            $error = 'Vui lòng nhập mật khẩu';
        }else if (empty($address)){
            $error = 'Vui lòng nhập địa chỉ.';
        }else if (strlen($address) > 300) {
              $error = 'Địa chỉ không được quá 300 ký tự.';
        }else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $pass)) {
          $error = 'Mật khẩu phải có ít nhất 8 ký tự và bao gồm chữ thường, chữ hoa, số và ký tự đặc biệt.';
        }
        else if ($pass != $pass_confirm) {
            $error = 'Mật khẩu không khớp.';
        }
        else if (!isset($_POST['terms'])) {
          $error = 'Bạn phải đồng ý các Chính sách & Điều khoản.';
        }
        else {
            $result = signup($full_name, $email, $phone, $pass, $dob, $address);
            if(gettype($result) === 'boolean'){
                header('Location: login.php');
            }else{
                $error = $result;
            }
        }
    }
        $namePage = "Đăng ký";
        include "view/header.php";
?>

    <div class="container form-box-sign mt-5">
    <form method="post">
      <h1 class="signup text-center mb-4">Đăng ký</h1>

      <div class="mb-3">
        <label for="full-name" class="form-label">Họ và tên</label>
        <input type="text" class="form-control" id="full-name" name="full" value="<?php echo htmlspecialchars($full_name); ?>" placeholder="Nhập họ và tên" />
      </div>

      <div class="mb-3">
        <label for="new-email" class="form-label">Email</label>
        <input type="email" class="form-control" id="new-email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Nhập email" />
      </div>

      <div class="mb-3">
        <label for="phone-number" class="form-label">Số điện thoại</label>
        <input type="text" class="form-control" id="phone-number" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="Nhập số điện thoại" />
      </div>

      <div class="mb-3">
        <label for="date-of-birth" class="form-label">Ngày sinh</label>
        <input type="date" class="form-control" id="date-of-birth" name="dob" value="<?php echo htmlspecialchars($dob); ?>"/>
      </div>

      <div class="mb-3">
        <label for="address" class="form-label">Địa chỉ</label>
        <input type="text" class="form-control" id="address" name="address" maxlength="300" value="<?php echo htmlspecialchars($address); ?>" placeholder="Nhập địa chỉ"/>
      </div>

      <!-- New Password -->
      <div class="mb-3">
        <label for="new-password" class="form-label">Mật khẩu</label>
        <div class="input-group">
          <input type="password" class="form-control" id="new-password" name="pass" placeholder="Nhập mật khẩu">
          <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('new-password', 'toggleNewEye')">
            <i class="fa fa-eye text-muted small" id="toggleNewEye"></i>
          </span>
        </div>
      </div>

      <!-- Confirm Password -->
      <div class="mb-3">
        <label for="confirm-password" class="form-label">Nhập lại mật khẩu</label>
        <div class="input-group">
          <input type="password" class="form-control" id="confirm-password" name="pass-confirm" placeholder="Nhập lại mật khẩu">
          <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('confirm-password', 'toggleConfirmEye')">
            <i class="fa fa-eye text-muted small" id="toggleConfirmEye"></i>
          </span>
        </div>
      </div>

      <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="terms" name="terms" />
        <label class="form-check-label" for="terms">
         Bằng cách tiếp tục đăng ký, tôi xác nhận rằng tôi đã đọc và đồng ý với
          <a href="term.php" target="_blank" style="color:rgb(3, 184, 54); text-decoration: underline">Chính sách & Điều khoản</a> của TeaV.
        </label>
      </div>
      <?php
           if (!empty($error)) {
              echo "<div class='alert alert-danger'>$error</div>";
           }
      ?>
      <button type="submit" class="btn btn-outline-primary">Đăng ký</button>

      <div class="text-center mt-3">
      <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
      </div>
    </form>
  </div>

<?php 
    include "view/footer.php"
?>