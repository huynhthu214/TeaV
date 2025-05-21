<?php
session_start();
$namePage = "Sửa thông tin khách hàng";
include "view/header-admin.php";

$conn = new mysqli("localhost", "root", "", "teav_shop1");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$error = "";
$email = $_GET['email'] ?? '';
if (!$email) {
    header("Location: customer-admin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $isActive = isset($_POST['isActive']) ? 'Yes' : 'No';

    $sql = "UPDATE Account SET FullName='$fullname', PhoneNumber='$phone', IsActive='$isActive' WHERE Email='$email'";
    if ($conn->query($sql)) {
        header("Location: customer-admin.php?edited=success");
        exit;
    } else {
        $error = "Lỗi khi cập nhật khách hàng: " . $conn->error;
    }
}

// Lấy thông tin khách hàng để hiển thị form
$sql = "SELECT * FROM Account WHERE Email='$email'";
$result = $conn->query($sql);
if ($result->num_rows === 0) {
    header("Location: customer-admin.php");
    exit;
}
$customer = $result->fetch_assoc();
?>

<div class="content-wrapper">
  <h2 class="mb-4" style="color:rgb(10, 119, 52);"><strong>Sửa thông tin khách hàng</strong></h2>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="card mb-4 p-3">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" readonly class="form-control" value="<?= htmlspecialchars($customer['Email']) ?>" />
      </div>

      <div class="mb-3">
        <label for="fullname" class="form-label">Họ tên</label>
        <input type="text" name="fullname" id="fullname" required class="form-control" value="<?= htmlspecialchars($customer['FullName']) ?>" />
      </div>

      <div class="mb-3">
        <label for="phone" class="form-label">Số điện thoại</label>
        <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($customer['PhoneNumber']) ?>" />
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" name="isActive" class="form-check-input" id="isActive" <?= $customer['IsActive'] === 'Yes' ? 'checked' : '' ?>>
        <label for="isActive" class="form-check-label">Hoạt động</label>
      </div>
    </div>

    <button type="submit" class="btn btn-success me-2">Lưu</button>
    <a href="customer-admin.php" class="btn btn-secondary">Hủy</a>
  </form>
</div>

<?php include "view/footer-admin.php"; ?>
