<?php 
session_start();

// Kết nối CSDL
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Hàm tạo mã tự tăng với tiền tố
function generateId($conn, $table, $column, $prefix) {
    $sql = "SELECT $column FROM $table WHERE $column LIKE '$prefix%' ORDER BY $column DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $newNumber = 1;

    if ($row = mysqli_fetch_assoc($result)) {
        $lastId = $row[$column];
        $number = intval(substr($lastId, strlen($prefix)));
        $newNumber = $number + 1;
    }

    return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

// Kiểm tra đăng nhập và giỏ hàng
if (!isset($_SESSION['email'])) {
    die("Vui lòng đăng nhập để đặt hàng.");
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Không có sản phẩm trong giỏ hàng.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['email'];
    $paymentMethod = $_POST['payment_method'] ?? 'Chưa chọn';
    $cart = $_SESSION['cart'];
    $total = 0;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Tạo mã đơn hàng và mã thanh toán
    $orderId = generateId($conn, "Orders", "OrderId", "ORD");
    $paymentId = generateId($conn, "Payment", "PaymentId", "PM");

    // --- 1. Thêm vào bảng Payment ---
    $stmt_payment = $conn->prepare("INSERT INTO Payment (PaymentId, TotalPrice, PaymentMethod) VALUES (?, ?, ?)");
    $stmt_payment->bind_param("sds", $paymentId, $total, $paymentMethod);
    if (!$stmt_payment->execute()) {
        die("Lỗi thêm thanh toán: " . $stmt_payment->error);
    }

    // --- 2. Thêm vào bảng Orders ---
    $statusOrder = "Chưa xử lý";
    $paymentStatus = "Chưa thanh toán";

    $stmt_order = $conn->prepare("INSERT INTO Orders (OrderId, Email, OrderDate, TotalAmount, StatusOrder, PaymentStatus)
                                  VALUES (?, ?, NOW(), ?, ?, ?)");
    $stmt_order->bind_param("ssdss", $orderId, $email, $total, $statusOrder, $paymentStatus);
    if (!$stmt_order->execute()) {
        die("Lỗi thêm đơn hàng: " . $stmt_order->error);
    }

    // --- 3. Thêm vào bảng OrderProduct ---
    $stmt_detail = $conn->prepare("INSERT INTO OrderProduct (OrderId, ProductId, Quantity) VALUES (?, ?, ?)");
    foreach ($cart as $item) {
        $stmt_detail->bind_param("ssi", $orderId, $item['id'], $item['quantity']);
        if (!$stmt_detail->execute()) {
            die("Lỗi thêm chi tiết đơn hàng: " . $stmt_detail->error);
        }
    }

    // --- 5. Chuyển sang trang thanh toán QR ---
    header("Location: payment-qr.php?payment_id=" . urlencode($paymentId));
    exit;
}
?>
