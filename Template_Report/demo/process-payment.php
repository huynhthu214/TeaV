<?php
session_start();
require_once "connect.php"; // Kết nối PDO

// Lấy dữ liệu form POST
$Name = $_POST['Name'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$phone_number = $_POST['phone_number'] ?? '';
$finalTotal = floatval($_POST['finalTotal'] ?? 0);
$paymentMethod = 'QR';  // Hoặc lấy từ form nếu có

// Giỏ hàng giả định trong session
$cart = $_SESSION['cart'] ?? []; // ['productId' => qty, ...]

if (empty($cart)) {
    die("Giỏ hàng trống, không thể đặt hàng.");
}

// Tạo OrderId và PaymentId đơn giản (ví dụ tự sinh)
// Bạn nên dùng cơ chế sinh ID chuẩn hơn
function generateId($prefix = '') {
    return $prefix . strtoupper(substr(md5(uniqid()), 0, 8));
}

$orderId = generateId('ORD');
$paymentId = generateId('PAY');

try {
    $conn->beginTransaction();

    // 1. Insert vào Orders
    $stmtOrder = $conn->prepare("INSERT INTO Orders (OrderId, OrderDate, TotalAmount) VALUES (?, NOW(), ?)");
    $stmtOrder->execute([$orderId, $finalTotal]);

    // 2. Insert vào Payment
    $stmtPay = $conn->prepare("INSERT INTO Payment (PaymentId, OrderId, TotalPrice, PaymentMethod) VALUES (?, ?, ?, ?)");
    $stmtPay->execute([$paymentId, $orderId, $finalTotal, $paymentMethod]);

    // 3. Insert chi tiết sản phẩm vào OrderProduct
    $stmtProd = $conn->prepare("INSERT INTO OrderProduct (OrderId, ProductId, Quantity) VALUES (?, ?, ?)");
    foreach ($cart as $productId => $qty) {
        $stmtProd->execute([$orderId, $productId, $qty]);
    }

    $conn->commit();

    // Xóa giỏ hàng sau khi đặt hàng
    unset($_SESSION['cart']);

    // Chuyển sang trang trạng thái đơn hàng
    header("Location: order-status.php?order_id=" . $orderId);
    exit;

} catch (PDOException $e) {
    $conn->rollBack();
    die("Lỗi khi lưu đơn hàng: " . $e->getMessage());
}
