<?php
session_start();
$namePage = "Đặt hàng thành công";
include "view/header.php";
?>

<style>
    html, body {
        height: 100%;
        margin: 0;
    }
    .page-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    main.content {
        flex: 1;
    }
</style>

<div class="page-wrapper">
<main class="content">

<?php
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$order = null;
$payment = null;

// Lấy payment_id từ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'])) {
    $paymentId = $_POST['payment_id'];

    // Cập nhật trạng thái thanh toán trong bảng Payment
    $update = $conn->prepare("UPDATE Payment SET PaymentStatus = 'Đã thanh toán' WHERE PaymentId = ?");
    $update->bind_param("s", $paymentId);
    $update->execute();
    $update->close();

    // Tìm đơn hàng tương ứng với mã thanh toán
    $stmt = $conn->prepare("SELECT * FROM Orders WHERE PaymentId = ? LIMIT 1");
    $stmt->bind_param("s", $paymentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    // Lấy thông tin thanh toán
    $stmt2 = $conn->prepare("SELECT * FROM Payment WHERE PaymentId = ?");
    $stmt2->bind_param("s", $paymentId);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $payment = $result2->fetch_assoc();
    $stmt2->close();

    unset($_SESSION['cart']);
}
?>

<div class="container py-5">
<?php if ($order && $payment): ?>
    <div class="alert alert-success">
        <h3>🎉 Đặt hàng thành công!</h3>
        <p>Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>
    </div>

    <h4>Thông tin đơn hàng</h4>
    <ul>
        <li><strong>Mã đơn:</strong> <?= $order['OrderId'] ?></li>
        <li><strong>Ngày đặt:</strong> <?= date('d-m-Y H:i:s', strtotime($order['OrderDate'])) ?></li>
        <li><strong>Tổng tiền:</strong> <?= number_format($order['TotalAmount'] * 1000, 0, ',', '.') ?> VND</li>
        <li><strong>Trạng thái:</strong> <?= $order['StatusOrder'] ?></li>
        <li><strong>Thanh toán:</strong> <?= $payment['PaymentStatus'] ?></li>
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
            <tr class="text-end fw-bold">
                <td colspan="3">Tổng cộng:</td>
                <td class="text-center"><?= number_format($totalAmount, 0, ',', '.') ?> VND</td>
            </tr>
        </tbody>
    </table>

    <div class="d-flex justify-content-end mt-1 mb-4">
        <a href="index.php" class="btn btn-primary">Quay lại trang chủ</a>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        <h4>Không có đơn hàng nào được xử lý.</h4>
        <p>Bạn chưa thực hiện đặt hàng hoặc đã tải lại trang.</p>
    </div>
<?php endif; ?>
</div>

</main>
</div>

<?php include "view/footer.php"; ?>
