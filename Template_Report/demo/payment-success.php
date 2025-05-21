<?php
session_start();
$namePage = "Đặt hàng thành công";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if (!isset($_SESSION['email'])) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Bạn cần đăng nhập để xem đơn hàng.</div></div>";
    include "view/footer.php";
    exit;
}

// Gán biến email
$email = $_SESSION['email'];

// Nếu có dữ liệu POST thì xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'])) {
    $_SESSION['payment_id'] = $_POST['payment_id'];
    $_SESSION['order_success'] = true;

    $update = $conn->prepare("UPDATE Payment SET PaymentStatus = 'Đã thanh toán' WHERE PaymentId = ?");
    $update->bind_param("s", $_SESSION['payment_id']);
    $update->execute();
    $update->close();

    unset($_SESSION['cart']);
}

// Lấy tất cả đơn hàng của người dùng hiện tại
$stmt = $conn->prepare("SELECT * FROM Orders WHERE Email = ? ORDER BY OrderDate DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$orders = $stmt->get_result();
?>


<div class="container py-5">
<?php if ($_SESSION['order_success'] ?? false): ?>
    <div class="alert alert-success">
        <h3>Đặt hàng thành công!</h3>
        <p>Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>
    </div>
    <?php unset($_SESSION['order_success'], $_SESSION['payment_id']); ?>
<?php endif; ?>

<?php if ($orders->num_rows > 0): ?>
    <?php while ($order = $orders->fetch_assoc()): ?>
        <?php
            // Lấy thông tin thanh toán của đơn
            $stmt2 = $conn->prepare("SELECT * FROM Payment WHERE PaymentId = ?");
            $stmt2->bind_param("s", $order['PaymentId']);
            $stmt2->execute();
            $payment = $stmt2->get_result()->fetch_assoc();
            $stmt2->close();
        ?>
        <div class="mb-5 border p-3 rounded shadow-sm">
            <h4>Đơn hàng #<?= $order['OrderId'] ?></h4>
            <ul>
                <li><strong>Ngày đặt:</strong> <?= date('d-m-Y H:i:s', strtotime($order['OrderDate'])) ?></li>
                <li><strong>Tổng tiền:</strong> <?= number_format($order['TotalAmount'] * 1000, 0, ',', '.') ?> VND</li>
                <li><strong>Trạng thái:</strong> <?= $order['StatusOrder'] ?></li>
                <li><strong>Thanh toán:</strong> <?= $payment['PaymentStatus'] ?? 'Chưa có' ?></li>
            </ul>

            <h6 class="mt-3">Sản phẩm đã đặt</h6>
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
                            JOIN Product p ON op.ProductId = p.ProductId
                            WHERE op.OrderId = ?";
                    $stmt3 = $conn->prepare($sql);
                    $stmt3->bind_param("s", $order['OrderId']);
                    $stmt3->execute();
                    $items = $stmt3->get_result();

                    while ($item = $items->fetch_assoc()):
                        $subtotal = $item['Quantity'] * $item['Price'] * 1000;
                        $totalAmount += $subtotal;
                    ?>
                        <tr class="text-center align-middle">
                            <td><img src="<?= htmlspecialchars($item['ImgUrl']) ?>" style="width:60px; height:60px; object-fit:cover;"></td>
                            <td><?= htmlspecialchars($item['Name']) ?></td>
                            <td><?= $item['Quantity'] ?></td>
                            <td><?= number_format($item['Price'] * 1000, 0, ',', '.') ?> VND</td>
                        </tr>
                    <?php endwhile; ?>
                    <tr class="fw-bold text-end">
                        <td colspan="3">Tổng cộng:</td>
                        <td class="text-center"><?= number_format($totalAmount, 0, ',', '.') ?> VND</td>
                    </tr>
                    <?php $stmt3->close(); ?>
                </tbody>
            </table>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="alert alert-warning">Bạn chưa có đơn hàng nào.</div>
<?php endif; ?>
</div>
    <div class="d-flex justify-content-end mt-1 mb-4">
        <a href="index.php" class="btn btn-primary">Quay lại trang chủ</a>
    </div>

</main>
</div>

<?php include "view/footer.php"; ?>
