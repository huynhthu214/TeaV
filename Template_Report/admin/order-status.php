<?php
require_once "connect.php";
include "view/header.php";

$orderId = $_GET['order_id'] ?? '';
if (!$orderId) {
    echo "<p>Không có mã đơn hàng.</p>";
    include "view/footer.php";
    exit;
}

// Lấy thông tin đơn hàng và thanh toán
$stmt = $conn->prepare("
    SELECT O.OrderId, O.OrderDate, O.TotalAmount, P.PaymentMethod
    FROM Orders O
    LEFT JOIN Payment P ON O.OrderId = P.OrderId
    WHERE O.OrderId = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<p>Đơn hàng không tồn tại.</p>";
    include "view/footer.php";
    exit;
}

// Lấy chi tiết sản phẩm
$stmtProd = $conn->prepare("
    SELECT OP.ProductId, OP.Quantity, Pr.Name
    FROM OrderProduct OP
    LEFT JOIN Products Pr ON OP.ProductId = Pr.ProductId
    WHERE OP.OrderId = ?
");
$stmtProd->execute([$orderId]);
$products = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Trạng thái đơn hàng #<?php echo htmlspecialchars($order['OrderId']); ?></h2>
<p>Ngày đặt: <?php echo htmlspecialchars($order['OrderDate']); ?></p>
<p>Tổng tiền: $<?php echo number_format($order['TotalAmount'], 2); ?></p>
<p>Phương thức thanh toán: <?php echo htmlspecialchars($order['PaymentMethod']); ?></p>

<h3>Sản phẩm trong đơn hàng</h3>
<table class="table">
    <thead>
        <tr><th>Sản phẩm</th><th>Số lượng</th></tr>
    </thead>
    <tbody>
        <?php foreach ($products as $prod): ?>
            <tr>
                <td><?php echo htmlspecialchars($prod['Name'] ?? $prod['ProductId']); ?></td>
                <td><?php echo $prod['Quantity']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Bạn có thể thêm phần trạng thái đơn hàng nếu bạn thêm cột Status -->

<?php include "view/footer.php"; ?>
