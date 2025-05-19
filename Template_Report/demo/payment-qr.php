<?php

session_start();
$namePage = "Mã QR";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$totalFromCart = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $quantity = $item['quantity'];
        $price = floatval($item['price']);
        $totalFromCart += $price * $quantity;
    }
}
$finalTotal = $totalFromCart;

// Lấy lại thông tin từ session/email
$name = $email = $address = $phone_number = '';
if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];
    $sql = "SELECT * FROM account WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        $name = $user['FullName'];
        $email = $user['Email'];
        $address = $user['Address'];
        $phone_number = $user['PhoneNumber'];
    }
}
?>

<div class="payment-container py-5">
    <h2>Xác nhận thanh toán</h2>

    <div class="alert alert-success">
        <strong>Thông tin hợp lệ!</strong> Vui lòng quét mã QR để thanh toán đơn hàng trị giá <strong>$<?php echo number_format($finalTotal, 2); ?></strong>
    </div>
    <div class="text-center">
        <img src="./layout/images/qrcode.png" alt="QR Code" style="max-width: 250px; border: 1px solid #ccc; padding: 10px;">
        <p class="text-muted mt-2">Sau khi thanh toán thành công, nhấn nút bên dưới để tiếp tục</p>
    </div>

    <form method="POST" action="payment-process.php">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <input type="hidden" name="address" value="<?php echo htmlspecialchars($address); ?>">
        <input type="hidden" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">
        <input type="hidden" name="finalTotal" value="<?php echo $finalTotal; ?>">

       <div class="form-buttons mt-4 d-flex justify-content-between">
            <a href="payment.php" class="btn btn-secondary">Quay lại</a>
           <button type="submit" class="btn btn-success">Tôi đã thanh toán</button>
        </div>
    </form>
</div>

<?php include "view/footer.php"; ?>
