<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin khách hàng từ form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone_number'];
    $total = floatval($_POST['finalTotal']);
    $order_date = date("Y-m-d H:i:s");

    $status_order = "Pending"; // Chờ xác nhận
    $payment_status = "Paid";  // Đã thanh toán


    // Thêm đơn hàng vào bảng orders
// Lấy OrderId mới (tăng dần)
$sql_latest = "SELECT OrderId FROM orders ORDER BY OrderId DESC LIMIT 1";
$result = mysqli_query($conn, $sql_latest);
$lastId = "ORD000";

if ($row = mysqli_fetch_assoc($result)) {
    $lastId = $row['OrderId'];
}

$num = intval(substr($lastId, 3)) + 1;
$newOrderId = 'ORD' . str_pad($num, 3, '0', STR_PAD_LEFT);
$stmt_order = $conn->prepare("INSERT INTO orders (OrderId, Email, OrderDate, TotalAmount, StatusOrder, PaymentStatus) 
                              VALUES (?, ?, ?, ?, ?, ?)");
$stmt_order->bind_param("sssdds", $newOrderId, $email, $order_date, $total, $status_order, $payment_status);
$stmt_order->execute();
    // Thêm từng sản phẩm vào orderproduct
foreach ($_SESSION['cart'] as $item) {
    $product_id = $item['id'];
    $quantity = $item['quantity'];

    $stmt = $conn->prepare("INSERT INTO orderproduct (OrderID, ProductID, Quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $newOrderId, $product_id, $quantity); // dùng 's' cho OrderID vì là string
    $stmt->execute();
    $stmt->close();
}

    // Xóa giỏ hàng
    unset($_SESSION['cart']);

    // Chuyển sang trang trạng thái đơn hàng
    header("Location: status-orders.php?order_id=" . $order_id);
    exit;
}
?>
