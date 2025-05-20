<?php
session_start();
$namePage = "Chi tiết khách hàng";
include "view/header-admin.php";

$dsn = 'mysql:host=localhost;dbname=teav_shop1;charset=utf8';
$username = 'root';
$password = '';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    // Kiểm tra và lấy Email từ URL
    if (!isset($_GET['email'])) {
        echo "<div class='alert alert-danger'>Không tìm thấy khách hàng.</div>";
        include "view/footer-admin.php";
        exit;
    }

    $email = $_GET['email'];

    // Lấy thông tin khách hàng
    $sqlCustomer = "SELECT * FROM Account WHERE Email = ? AND Type = 'Customer'";
    $stmt = $pdo->prepare($sqlCustomer);
    $stmt->execute([$email]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        echo "<div class='alert alert-warning'>Không tìm thấy thông tin khách hàng.</div>";
        include "view/footer-admin.php";
        exit;
    }

    // Lấy danh sách đơn hàng của khách hàng
    $sqlOrders = "
      SELECT OrderId, OrderDate, TotalAmount, PaymentId
      FROM Orders
      WHERE Email = ?
      ORDER BY OrderDate DESC
    ";

    $stmtOrders = $pdo->prepare($sqlOrders);
    $stmtOrders->execute([$email]);
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>

<div class="content-wrapper">
  <h2 class="mb-4" style="color:rgb(10, 119, 52);"><strong>Thông tin khách hàng</strong></h2>

  
  <div class="card mb-4 p-3">
    <h5 class="mb-3">Thông tin cá nhân</h5>
    <div><strong>Họ tên:</strong> <?= htmlspecialchars($customer['FullName']) ?></div>
    <div><strong>Email:</strong> <?= htmlspecialchars($customer['Email']) ?></div>
    <div><strong>Số điện thoại:</strong> <?= htmlspecialchars($customer['PhoneNumber']) ?></div>
    <div><strong>Địa chỉ:</strong> <?= htmlspecialchars($customer['Address']) ?></div>
    <div><strong>Ngày sinh:</strong> <?= $customer['DateOfBirth'] ? date("d/m/Y", strtotime($customer['DateOfBirth'])) : "Chưa cập nhật" ?></div>
    <div><strong>Ngày đăng ký:</strong> <?= date("d/m/Y", strtotime($customer['CreatedDate'])) ?></div>
    <div><strong>Trạng thái:</strong> 
      <span class="badge bg-<?= $customer['IsActive'] === 'Yes' ? 'success' : 'secondary'; ?>">
        <?= $customer['IsActive'] === 'Yes' ? 'Hoạt động' : 'Ngừng' ?>
      </span>
    </div>
  </div>

  <div class="card p-3">
    <h5 class="mb-3">Lịch sử đơn hàng</h5>
    <?php if (!empty($orders)): ?>
      <table class="table table-bordered text-center">
        <thead class="table-success">
          <tr>
            <th>Mã đơn</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Thanh toán</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td><?= htmlspecialchars($order['OrderId']) ?></td>
              <td><?= date("d/m/Y", strtotime($order['OrderDate'])) ?></td>
              <td><?= number_format($order['TotalAmount'], 0, ',', '.') ?> đ</td>
              <td><?= $order['PaymentId'] ? htmlspecialchars($order['PaymentId']) : 'Chưa có' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="text-muted">Khách hàng chưa có đơn hàng nào.</p>
    <?php endif; ?>
  </div>
  <a href="customer-admin.php" class="btn btn-secondary mb-3">Quay lại</a>
</div>

<?php include "view/footer-admin.php"; ?>
