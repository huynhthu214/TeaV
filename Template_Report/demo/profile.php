<?php 
  session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
  $conn = mysqli_connect("localhost", "root", "", "teav_shop1");

  if (!$conn) {
      die("Kết nối thất bại: " . mysqli_connect_error());
  }


$email = $_SESSION['email'];

$sql = "SELECT * FROM account WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$isEditing = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        $isEditing = true;
    }

  if (isset($_POST['update'])) {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];

    $updateSql = "UPDATE account SET FullName=?, PhoneNumber=?, DateOfBirth=?, Address=? WHERE Email=?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssss", $fullname, $phone, $dob, $address, $email);

    if ($updateStmt->execute()) {
        $_SESSION['success'] = "Cập nhật thông tin thành công!";
        $updateStmt->close();
        header("Location: " . $_SERVER['PHP_SELF']); // Chuyển trang để tránh gửi lại form
        exit();
    }

    $updateStmt->close();
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TeaV - Hồ sơ của tôi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="layout/css/style_profile.css"/>
</head>
<body>
  
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light shadow-sm px-4 py-2">
    <div class="container-fluid">
      <div class="d-flex align-items-center ms-auto gap-3">
      <a href="index.php" class="text-dark position-relative" aria-label="Home">
          <i class="bi bi-house-door-fill fs-5" aria-hidden="true"></i>
        </a>
         <a href="cart.php" class="text-dark position-relative" aria-label="Shopping Cart">
        <i class="bi bi-cart3 fs-5" aria-hidden="true"></i>
        <?php
          $orderCount = 0;
          if (isset($_SESSION['cart'])) {
              $orderCount = count($_SESSION['cart']);
          }
          if ($orderCount > 0) {
              echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'
                  . $orderCount . '</span>';
          }
        ?>
      </a>
            <?php if (isset($_SESSION['email'])): ?>
          <?php 
            $firstChar = strtoupper(substr($_SESSION['email'], 0, 1)); 
            $userEmail = htmlspecialchars($_SESSION['email']);
          ?>
          <div class="d-flex align-items-center">
            <div class="avatar-circle"><?php echo $firstChar; ?></div>
            <span class="text-dark fw-semibold"><?php echo $userEmail; ?></span>
                
          <?php if (isset($_SESSION['success'])): ?>
        <div class="position-absolute top-100 end-0 mt-2 me-4 z-3" style="max-width: 300px;">
          <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Đóng"></button>
          </div>
        </div>
      <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row min-vh-100">
      <!-- Sidebar -->
      <div class="sidebar col-auto">
          <div class="logo mb-4">
               <a href="index.php" class="fw-bold fs-4">🍃TeaV</a>
           </div>
        <h4 class="mb-4"><i class="bi bi-person-circle" aria-hidden="true"></i> Hồ sơ của tôi</h4>
        <ul class="list-unstyled">
          <li class="mb-3">
            <a href="#" class="text-decoration-none text-light">
              <i class="bi bi-info-circle me-2" aria-hidden="true"></i> Thông tin tài khoản
            </a>
          </li>
            <li class="mb-3">
            <a href="resetpwd.php" class="text-decoration-none text-light">
              <i class="bi bi-shield-lock me-2" aria-hidden="true"></i> Đổi mật khẩu
            </a>
          </li>
          <li class="mb-3">
            <a href="logout.php" class="text-decoration-none text-danger">
              <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i> Đăng xuất
            </a>
          </li>
        </ul>
      </div>

      <!-- Main Content -->
      <main class="col main-content p-5">

        <!-- Account Details -->
        <h3 class="mb-4"><strong> Thông tin tài khoản</strong></h3>
        <div class="row">
        <form method="post">
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Họ và tên</label>
      <input type="text" class="form-control" name="fullname"
             value="<?php echo htmlspecialchars($user['FullName']); ?>"
             <?php echo $isEditing ? '' : 'readonly'; ?>>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control"
             value="<?php echo htmlspecialchars($user['Email']); ?>" readonly>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Số điện thoại</label>
      <input type="text" class="form-control" name="phone"
             value="<?php echo htmlspecialchars($user['PhoneNumber']); ?>"
             <?php echo $isEditing ? '' : 'readonly'; ?>>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Ngày sinh</label>
      <input type="date" class="form-control" name="dob"
             value="<?php echo htmlspecialchars($user['DateOfBirth']); ?>"
             <?php echo $isEditing ? '' : 'readonly'; ?>>
    </div>
    <div class="col-12 mb-3">
      <label class="form-label">Địa chỉ</label>
      <input type="text" class="form-control" name="address"
             value="<?php echo htmlspecialchars($user['Address']); ?>"
             <?php echo $isEditing ? '' : 'readonly'; ?>>
    </div>
    
  </div>

  <?php if ($isEditing): ?>
  <div class="d-flex justify-content-end">
    <button type="submit" name="update" class="btn btn-success" style="background-color:rgb(235, 163, 40);">Cập nhật</button>
  </div>
<?php else: ?>
  <div class="d-flex justify-content-start">
    <button type="submit" name="edit" class="btn" style="background-color:rgb(202, 199, 194); width: 10%;">Chỉnh sửa</button>
  </div>
<?php endif; ?>
</form> 
      </main>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="layout/js/jquery.js"></script>
</body>
</html>
