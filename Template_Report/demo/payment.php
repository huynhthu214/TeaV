<?php
$namePage = "Thanh toán";
include "view/header.php";

$totalFromCart = isset($_GET['total']) ? floatval($_GET['total']) : 100.00;
$discount = 0;
$finalTotal = $totalFromCart;

$errors = [];
$Name = '';
$email = '';
$address = '';
$phone_number = '';
$step = $_POST['step'] ?? 'info'; // default step

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Name = trim($_POST['Name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');

    // Validate ở bước 1 (thông tin người dùng)
    if ($step === 'info') {
        if (empty($Name)) $errors[] = 'Vui lòng nhập họ tên';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
        if (empty($address) || strlen($address) > 300) $errors[] = 'Địa chỉ không hợp lệ';
        if (empty($phone_number) || !preg_match('/^\d{10}$/', $phone_number)) $errors[] = 'Số điện thoại không hợp lệ';

        if (empty($errors)) {
            $step = 'qr'; // chuyển bước sang hiển thị mã QR
        }
    }

    // Xử lý khi đã quét QR
    if ($step === 'done') {
        // Sau khi nhấn "Tôi đã thanh toán", chuyển đến xử lý đơn hàng
        header("Location: process-payment.php");
        exit;
    }
}
?>

<div class="payment-container py-5">
    <h2>Thông tin thanh toán</h2>

    <!-- Hiển thị lỗi -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form action="payment.php?total=<?php echo $totalFromCart; ?>" method="POST">
        <input type="hidden" name="step" value="<?php echo $step === 'qr' ? 'done' : 'info'; ?>">

        <!-- BƯỚC 1: Nhập thông tin -->
        <?php if ($step === 'info'): ?>
            <div class="form-group">
                <label for="Name">Họ tên</label>
                <input type="text" name="Name" id="Name" value="<?php echo htmlspecialchars($Name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($address); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Số điện thoại</label>
                <input type="text" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
            </div>

            <div class="order-summary mt-4">
                <p><strong>Tổng tiền:</strong> $<?php echo number_format($finalTotal, 2); ?></p>
            </div>

            <div class="form-buttons mt-3">
                <a href="cart.php" class="btn btn-secondary">Quay lại giỏ hàng</a>
                <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
            </div>

        <!-- BƯỚC 2: Hiển thị mã QR -->
        <?php elseif ($step === 'qr'): ?>
            <div class="alert alert-success">
                <strong> Thông tin hợp lệ! </strong> Vui lòng quét mã QR để thanh toán đơn hàng trị giá <strong>$<?php echo number_format($finalTotal, 2); ?></strong>
            </div>
            <div class="text-center">
                <img src="./layout/images/qrcode.png" alt="QR Code" style="max-width: 250px; border: 1px solid #ccc; padding: 10px;">
                <p class="text-muted mt-2">Sau khi thanh toán thành công, nhấn nút bên dưới để tiếp tục</p>
            </div>

            <!-- Giữ thông tin người dùng để gửi tiếp -->
            <input type="hidden" name="Name" value="<?php echo htmlspecialchars($Name); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="address" value="<?php echo htmlspecialchars($address); ?>">
            <input type="hidden" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">
            <input type="hidden" name="finalTotal" value="<?php echo $finalTotal; ?>">

            <div class="form-buttons mt-4">
                <button type="submit" class="btn btn-success">Tôi đã thanh toán</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include "view/footer.php"; ?>
