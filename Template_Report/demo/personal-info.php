<?php 
  session_start();
  $conn = mysqli_connect("localhost", "root", "", "teav_shop1");

  if (!$conn) {
      die("Kết nối thất bại: " . mysqli_connect_error());
  }

  if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
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
            // Cập nhật dữ liệu lại để hiển thị
            $sql = "SELECT * FROM account WHERE Email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
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
  <title>TeaV - My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <style>
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 220px;
      height: 100%;
      background-color: beige;
      padding: 20px;
      border-right: 1px solid #ddd;
    }
    .main-content {
      margin-left: 220px;
    }
    .avatar-circle {
      width: 36px;
      height: 36px;
      background-color:rgb(17, 101, 36);
      border-radius: 50%;
      color: white;
      font-weight: bold;
      font-size: 18px;
      text-align: center;
      line-height: 36px;
      display: inline-block;
      margin-right: 8px;
    }
    body {
      font-family: "Lora", sans-serif;
    }
    
    nav.navbar {
    background-color: #0d371e; 
     }

   input.form-control{
    border-color:rgb(34, 131, 158);
   }
  </style>
</head>
<body>
  
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light shadow-sm px-4 py-2">
    <div class="container-fluid">
      <div class="d-flex align-items-center ms-auto gap-3">
      <a href="index.php" class="text-light position-relative" aria-label="Home">
          <i class="bi bi-house-door-fill fs-5" aria-hidden="true"></i>
        </a>
        <a href="cart.php" class="text-light position-relative" aria-label="Shopping Cart">
          <i class="bi bi-cart3 fs-5" aria-hidden="true"></i>
            <?php
                $cartCount = 0;
                if (isset($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        $cartCount += $item['quantity'];
                    }
                }
                ?>
<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
  <?php echo $cartCount; ?>
</span>
        </a>
            <?php if (isset($_SESSION['email'])): ?>
          <?php 
            $firstChar = strtoupper(substr($_SESSION['email'], 0, 1)); 
            $userEmail = htmlspecialchars($_SESSION['email']);
          ?>
          <div class="d-flex align-items-center">
            <div class="avatar-circle"><?php echo $firstChar; ?></div>
            <span class="text-light fw-semibold"><?php echo $userEmail; ?></span>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row min-vh-100">
      <!-- Sidebar -->
      <div class="sidebar col-auto">
        <h4 class="mb-4"><i class="bi bi-person-circle" aria-hidden="true"></i> My Profile</h4>
        <ul class="list-unstyled">
  <li class="mb-3">
    <a href="#" class="text-decoration-none text-dark">
      <i class="bi bi-info-circle me-2" aria-hidden="true"></i>Account Infomation
    </a>
  </li>
  <li class="mb-3">
  <a href="resetpwd.php" class="text-decoration-none text-dark">
    <i class="bi bi-shield-lock me-2" aria-hidden="true"></i>Change Password
  </a>
</li>
  <li class="mb-3">
    <a href="logout.php" class="text-decoration-none text-danger">
      <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Logout
    </a>
  </li>
</ul>
      </div>

      <!-- Main Content -->
      <main class="col main-content p-5">

        <!-- Account Details -->
        <h3 class="mb-4"><strong>Account Infomation</strong></h3>
        <div class="row">
        <form method="post">
  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Full Name</label>
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
      <label class="form-label">Phone Number</label>
      <input type="text" class="form-control" name="phone"
             value="<?php echo htmlspecialchars($user['PhoneNumber']); ?>"
             <?php echo $isEditing ? '' : 'readonly'; ?>>
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">Birthday</label>
      <input type="date" class="form-control" name="dob"
             value="<?php echo htmlspecialchars($user['DateOfBirth']); ?>"
             <?php echo $isEditing ? '' : 'readonly'; ?>>
    </div>
    <div class="col-12 mb-3">
      <label class="form-label">Address</label>
      <input type="text" class="form-control" name="address"
             value="<?php echo htmlspecialchars($user['Address']); ?>"
             <?php echo $isEditing ? '' : 'readonly'; ?>>
    </div>
    
  </div>

  <?php if ($isEditing): ?>
  <div class="d-flex justify-content-end">
    <button type="submit" name="update" class="btn btn-success">Update</button>
  </div>
<?php else: ?>
  <div class="d-flex justify-content-start">
    <button type="submit" name="edit" class="btn" style="background-color:rgb(202, 199, 194); width: 10%;">Edit</button>
  </div>
<?php endif; ?>
</form> 
      </main>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
