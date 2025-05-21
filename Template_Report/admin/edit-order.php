<?php
// edit-order.php

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

    // Lấy đơn hàng
    $stmt = $pdo->prepare("SELECT * FROM Orders WHERE OrderId = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<div class='alert alert-warning'>Đơn hàng không tồn tại.</div>";
        include "view/footer-admin.php";
        exit;
    }

    // Lấy sản phẩm trong đơn hàng
    $stmtProducts = $pdo->prepare("
        SELECT OP.ProductId, OP.Quantity, P.Name as ProductName 
        FROM OrderProduct OP
        LEFT JOIN Product P ON OP.ProductId = P.ProductId
        WHERE OP.OrderId = ?
    ");
    $stmtProducts->execute([$orderId]);
    $orderProducts = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách tất cả sản phẩm để chọn thêm mới
    $stmtAllProducts = $pdo->query("SELECT ProductId, Name FROM Product ORDER BY Name");
    $allProducts = $stmtAllProducts->fetchAll(PDO::FETCH_ASSOC);

    // Xử lý submit form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Cập nhật email và trạng thái đơn hàng
        $email = $_POST['email'] ?? '';
        $status = $_POST['status'] ?? '';

        $updateOrder = $pdo->prepare("UPDATE Orders SET Email = ?, StatusOrder = ? WHERE OrderId = ?");
        $updateOrder->execute([$email, $status, $orderId]);

        // Xử lý cập nhật số lượng từng sản phẩm (có thể có các input dạng quantity[ProductId])
        if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $productId => $qty) {
                $qty = (int)$qty;
                if ($qty <= 0) {
                    // Xóa sản phẩm khỏi đơn nếu số lượng <= 0
                    $del = $pdo->prepare("DELETE FROM OrderProduct WHERE OrderId = ? AND ProductId = ?");
                    $del->execute([$orderId, $productId]);
                } else {
                    // Cập nhật số lượng
                    $upd = $pdo->prepare("UPDATE OrderProduct SET Quantity = ? WHERE OrderId = ? AND ProductId = ?");
                    $upd->execute([$qty, $orderId, $productId]);
                }
            }
        }

        // Xử lý thêm sản phẩm mới
        if (!empty($_POST['new_product_id']) && !empty($_POST['new_quantity'])) {
            $newProductId = $_POST['new_product_id'];
            $newQty = (int)$_POST['new_quantity'];

            // Kiểm tra sản phẩm đã có trong đơn chưa
            $check = $pdo->prepare("SELECT * FROM OrderProduct WHERE OrderId = ? AND ProductId = ?");
            $check->execute([$orderId, $newProductId]);
            if ($check->rowCount() === 0 && $newQty > 0) {
                $insert = $pdo->prepare("INSERT INTO OrderProduct(OrderId, ProductId, Quantity) VALUES (?, ?, ?)");
                $insert->execute([$orderId, $newProductId, $newQty]);
            } else {
                // Nếu đã có sản phẩm, có thể cộng dồn số lượng hoặc báo lỗi
                // Ở đây mình cộng dồn số lượng
                $updQty = $pdo->prepare("UPDATE OrderProduct SET Quantity = Quantity + ? WHERE OrderId = ? AND ProductId = ?");
                $updQty->execute([$newQty, $orderId, $newProductId]);
            }
        }

        // Cập nhật lại đơn hàng sau khi sửa sản phẩm (nếu cần tính lại tổng tiền, tùy logic)
        // Bạn có thể viết thêm đoạn code tính lại TotalAmount nếu muốn

        // Reload trang để tránh resubmit
        header("Location: order-admin.php");
        exit;
    }

} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>

<div class="content-wrapper">
  <h2 class="mb-4" style="color:rgb(10, 119, 52);"><strong>Chỉnh sửa đơn hàng</strong></h2>

  <form action="" method="POST">
    <div class="mb-3">
      <label for="email" class="form-label">Email khách hàng</label>
      <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($order['Email']) ?>">
    </div>

    <div class="mb-3">
      <label for="status" class="form-label">Trạng thái đơn hàng</label>
      <select name="status" id="status" class="form-select" required>
        <option value="Chưa xử lý" <?= $order['StatusOrder'] === 'Chưa xử lý' ? 'selected' : '' ?>>Chưa xử lý</option>
        <option value="Đã xử lý" <?= $order['StatusOrder'] === 'Đã xử lý' ? 'selected' : '' ?>>Đã xử lý</option>
      </select>
    </div>

    <h5>Sản phẩm trong đơn hàng</h5>
    <?php if ($orderProducts): ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Mã SP</th>
            <th>Tên sản phẩm</th>
            <th>Số lượng</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orderProducts as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['ProductId']) ?></td>
            <td><?= htmlspecialchars($p['ProductName'] ?? 'N/A') ?></td>
            <td>
              <input type="number" name="quantity[<?= htmlspecialchars($p['ProductId']) ?>]" min="0" class="form-control" value="<?= htmlspecialchars($p['Quantity']) ?>">
              <small class="text-muted">Nhập 0 để xóa sản phẩm</small>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Đơn hàng chưa có sản phẩm nào.</p>
    <?php endif; ?>

    <h5>Thêm sản phẩm mới</h5>
    <div class="row mb-3">
      <div class="col-md-6">
        <select name="new_product_id" class="form-select">
          <option value="">-- Chọn sản phẩm --</option>
          <?php foreach ($allProducts as $prod): ?>
          <option value="<?= htmlspecialchars($prod['ProductId']) ?>"><?= htmlspecialchars($prod['Name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <input type="number" name="new_quantity" min="1" class="form-control" placeholder="Số lượng">
      </div>
      <div class="col-md-3">
        <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
      </div>
    </div>

    <button type="submit" class="btn btn-success">Lưu thay đổi đơn hàng</button>
    <a href="order-admin.php" class="btn btn-secondary">Hủy</a>
  </form>
</div>

<?php include "view/footer-admin.php"; ?>
