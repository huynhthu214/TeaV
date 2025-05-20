<?php
session_start();
$namePage = "Mã QR";
include "view/header.php";

// Kết nối DB
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Tính tổng tiền từ giỏ hàng
$totalFromCart = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $quantity = $item['quantity'];
        $price = floatval($item['price']);
        $totalFromCart += $price * $quantity;
    }
}
$finalTotal = $totalFromCart;

// Lấy thông tin tài khoản từ session
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
$payment_id = $_GET['payment_id'] ?? '';
if (empty($payment_id)) {
    echo "<div class='alert alert-danger text-center'>Không tìm thấy mã thanh toán hợp lệ.</div>";
    include "view/footer.php";
    exit;
}
?>

<div class="payment-container py-5">
    <h2>Xác nhận thanh toán</h2>

    <div class="alert alert-success">
        <strong>Thông tin hợp lệ!</strong> Vui lòng quét mã QR để thanh toán đơn hàng trị giá 
        <strong><?php echo number_format($finalTotal, 0, ',', '.') ?>₫</strong>
    </div>

    <div class="text-center">
        <img src="./layout/images/qrcode.png" alt="QR Code" style="max-width: 250px; border: 1px solid #ccc; padding: 10px;">
        <p class="text-muted mt-2">Sau khi thanh toán thành công, nhấn nút bên dưới để tiếp tục</p>
        <p class="text-danger fw-bold mt-3" id="countdown">Thời gian còn lại: 15:00</p>
        <script>
    let timeLeft = 15 * 60; // 15 phút (đơn vị giây)
    const countdownEl = document.getElementById('countdown');

    function updateCountdown() {
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        countdownEl.innerText = `Thời gian còn lại: ${minutes}:${seconds}`;
        if (timeLeft <= 0) {
            countdownEl.innerText = "Đã hết thời gian thanh toán!";
            document.querySelector("button[type='submit']").disabled = true;
            document.querySelector("button[type='submit']").innerText = "Hết hạn";
        } else {
            timeLeft--;
            setTimeout(updateCountdown, 1000);
        }
    }

    updateCountdown();
</script>
    </div>

<form method="POST" action="payment-success.php">
    <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($payment_id); ?>">
    <div class="form-buttons mt-4 d-flex justify-content-between">
        <a href="order-detail.php" class="btn btn-secondary">Quay lại</a>
        <button type="submit" class="btn btn-success" id="submitBtn">Tôi đã thanh toán</button>
    </div>
</form>
</div>

<?php 
unset($_SESSION['order_info']);
include "view/footer.php"; 
?>
