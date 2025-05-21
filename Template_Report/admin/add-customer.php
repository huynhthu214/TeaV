<?php
session_start();
$namePage = "Thêm khách hàng mới";
include "view/header-admin.php";

$conn = new mysqli("localhost", "root", "", "teav_shop1");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $isActive = isset($_POST['isActive']) ? 'Yes' : 'No';

    // Kiểm tra email có tồn tại chưa
    $checkSql = "SELECT Email FROM Account WHERE Email='$email'";
    $result = $conn->query($checkSql);
    if ($result->num_rows > 0) {
        $error = "Email đã tồn tại!";
    } else {
        $sql = "INSERT INTO Account (Email, FullName, PhoneNumber, IsActive, CreatedDate) VALUES ('$email', '$fullname', '$phone', '$isActive', NOW())";
        if ($conn->query($sql)) {
            header("Location: customer-admin.php?added=success");
            exit;
        } else {
            $error = "Lỗi khi thêm khách hàng: " . $conn->error;
        }
    }
}
?>

<div class="content-wrapper">
  <h2 class="mb-4" style="color:rgb(10, 119, 52);"><strong>Thêm khách hàng mới</strong></h2>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="card mb-4 p-3">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" required class="form-control" placeholder="Nhập email khách hàng">
      </div>

      <div class="mb-3">
        <label for="fullname" class="form-label">Họ tên</label>
        <input type="text" name="fullname" id="fullname" required class="form-control" placeholder="Nhập họ tên khách hàng">
      </div>

      <div class="mb-3">
        <label for="phone" class="form-label">Số điện thoại</label>
        <input type="text" name="phone" id="phone" class="form-control" placeholder="Nhập số điện thoại">
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" name="isActive" class="form-check-input" id="isActive" checked>
        <label for="isActive" class="form-check-label">Hoạt động</label>
      </div>
    </div>

    <button type="submit" class="btn btn-success me-2">Thêm</button>
    <a href="customer-admin.php" class="btn btn-secondary">Hủy</a>
  </form>
</div>

<?php include "view/footer-admin.php"; ?>
