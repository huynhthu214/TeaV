<?php
session_start();
$namePage = "Đặt hàng thành công";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy payment_id từ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'])) {
    $paymentId = $_POST['payment_id'];

    $email = $_SESSION['email'] ?? '';
    $order = null;
    if ($email) {
        $sql = "SELECT * FROM Orders WHERE Email = ? ORDER BY OrderDate DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();
    }

    if ($order) {
        $orderId = $order['OrderId'];

        // Cập nhật trạng thái thanh toán
        $update = $conn->prepare("UPDATE Orders SET PaymentStatus = 'Đã thanh toán' WHERE OrderId = ?");
        $update->bind_param("s", $orderId);
        $update->execute();
        $update->close();

        unset($_SESSION['cart']);
    }
}
?>

<div class="container py-5">
    <div class="alert alert-success">
        <h3>🎉 Đặt hàng thành công!</h3>
        <p>Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>
    </div>

    <?php if (isset($order)): ?>
        <h4>Thông tin đơn hàng</h4>
        <ul>
            <li><strong>Mã đơn:</strong> <?= $order['OrderId'] ?></li>
            <li><strong>Ngày đặt:</strong> <?= $order['OrderDate'] ?></li>
            <li><strong>Tổng tiền:</strong> <?= number_format($order['TotalAmount'] * 1000, 0, ',', '.') ?> VND</li>
            <li><strong>Trạng thái:</strong> <?= $order['StatusOrder'] ?></li>
            <li><strong>Thanh toán:</strong> <?= $order['PaymentStatus'] ?></li>
        </ul>

        <h5 class="mt-4">Sản phẩm đã đặt</h5>
        <table class="table table-bordered mt-2">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalAmount = 0;
                $sql = "SELECT p.Name, p.ImgUrl, op.Quantity, p.Price
                        FROM OrderProduct op
                        JOIN product p ON op.ProductId = p.ProductId
                        WHERE op.OrderId = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $order['OrderId']);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()):
                    $subtotal = $row['Quantity'] * $row['Price'] * 1000;
                    $totalAmount += $subtotal;
                ?>
                    <tr class="align-middle text-center">
                        <td><img src="<?= htmlspecialchars($row['ImgUrl']) ?>" alt="Ảnh sản phẩm" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"></td>
                        <td><?= htmlspecialchars($row['Name']) ?></td>
                        <td><?= $row['Quantity'] ?></td>
                        <td><?= number_format($row['Price'] * 1000, 0, ',', '.') ?> VND</td>
                    </tr>
                <?php endwhile; ?>
                <?php $stmt->close(); ?>
                <!-- Tổng cộng -->
                <tr class="text-end fw-bold">
                    <td colspan="3">Tổng cộng:</td>
                    <td class="text-center"><?= number_format($totalAmount, 0, ',', '.') ?> VND</td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>Không tìm thấy đơn hàng để hiển thị.</p>
    <?php endif; ?>
</div>
<div class="d-flex justify-content-end mt-1">
    <a href="index.php" class="btn btn-primary">
            Quay lại trang chủ
    </a>
</div>
<?php include "view/footer.php"; ?>
