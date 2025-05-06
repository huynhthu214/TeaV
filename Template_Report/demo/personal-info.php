<?php 
  session_start();
  $conn = mysqli_connect("localhost", "root", "", "teav_shop1");

  if (!$conn) {
      die("Kết nối thất bại: " . mysqli_connect_error());
  }

  if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user_email'];

$sql = "SELECT * FROM account WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
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
      background-color:rgb(249, 250, 248);
      padding: 20px;
      border-right: 1px solid #ddd;
    }
    .main-content {
      margin-left: 220px;
    }
  </style>
</head>
<body>
  
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 py-2">
    <div class="container-fluid">
      <div class="d-flex align-items-center ms-auto gap-3">
      <a href="index.html" class="text-dark position-relative" aria-label="Home">
          <i class="bi bi-house-door-fill fs-5" aria-hidden="true"></i>
        </a>
        <a href="cart.php" class="text-dark position-relative" aria-label="Shopping Cart">
          <i class="bi bi-cart3 fs-5" aria-hidden="true"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">2</span>
        </a>
        <span class="text-primary fw-semibold">Douglas McGee</span>
        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="User avatar" width="32" height="32" class="rounded-circle">
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
      <i class="bi bi-info-circle me-2" aria-hidden="true"></i>Account Details
    </a>
  </li>
  <li class="mb-3">
    <a href="vouchers.php" class="text-decoration-none text-dark">
      <i class="bi bi-ticket-perforated me-2" aria-hidden="true"></i>My Vouchers
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
        <h3 class="mb-4">Account Details</h3>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['FullName']); ?>" readonly>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['Email']); ?>" readonly>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['PhoneNumber']); ?>" readonly>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Birthday</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['DateOfBirth']); ?>" readonly>
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['DateOfBirth']); ?>" readonly>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
