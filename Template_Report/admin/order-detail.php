<?php
// order-detail.php

include "view/header-admin.php";

$dsn = 'mysql:host=localhost;dbname=teav_shop1;charset=utf8';
$username = 'root';
$password = '';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    if (!isset($_GET['id'])) {
        echo "<div class='alert alert-danger'>Không tìm thấy đơn hàng.</div>";
        include "view/footer-admin.php";
        exit;
    }

    $orderId = $_GET['id'];

    // Lấy thông tin đơn hàng
    $stmt = $pdo->prepare("SELECT * FROM Orders WHERE OrderId = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<div class='alert alert-warning'>Đơn hàng không tồn tại.</div>";
        include "view/footer-admin.php";
        exit;
    }

    // Lấy sản phẩm trong đơn hàng (giả sử bạn có bảng Product để lấy tên sản phẩm)
    $sqlProducts = "
        SELECT OP.ProductId, OP.Quantity, P.Name as ProductName
        FROM OrderProduct OP
        LEFT JOIN Product P ON OP.ProductId = P.ProductId
        WHERE OP.OrderId = ?
    ";
    $stmtProducts = $pdo->prepare($sqlProducts);
    $stmtProducts->execute([$orderId]);
    $products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>

<div class="content-wrapper">
  <h2 class="mb-4" style="color:rgb(10, 119, 52);"><strong>Chi tiết đơn hàng</strong></h2>

  <div class="card p-3 mb-4">
    <div><strong>Mã đơn hàng:</strong> <?= htmlspecialchars($order['OrderId']) ?></div>
    <div><strong>Email khách hàng:</strong> <?= htmlspecialchars($order['Email']) ?></div>
    <div><strong>Ngày đặt hàng:</strong> <?= date("d/m/Y H:i", strtotime($order['OrderDate'])) ?></div>
    <div><strong>Tổng tiền:</strong> <?= number_format($order['TotalAmount'], 3) ?> VNĐ</div>
    <div><strong>Trạng thái:</strong> <?= htmlspecialchars($order['StatusOrder']) ?></div>
  </div>

  <div class="card p-3 mb-4">
    <h5>Sản phẩm trong đơn hàng</h5>
    <?php if ($products): ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Mã SP</th>
          <th>Tên sản phẩm</th>
          <th>Số lượng</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['ProductId']) ?></td>
            <td><?= htmlspecialchars($p['ProductName'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($p['Quantity']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>Không có sản phẩm nào trong đơn hàng này.</p>
    <?php endif; ?>
  </div>

  <a href="order-admin.php" class="btn btn-secondary">Quay lại</a>
</div>

<?php include "view/footer-admin.php"; ?>
