<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id > 0) {
    $sql = "SELECT * FROM orders WHERE OrderID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if ($order) {
        // Lấy tên khách hàng từ bảng account
        $stmt_name = $conn->prepare("SELECT FullName FROM account WHERE Email = ?");
        $stmt_name->bind_param("s", $order['Email']);
        $stmt_name->execute();
        $result_name = $stmt_name->get_result();
        $acc = $result_name->fetch_assoc();
        $name = $acc['FullName'] ?? "Không rõ";

        echo "<h2>Thông tin đơn hàng</h2>";
        echo "Mã đơn hàng: " . $order['OrderID'] . "<br>";
        echo "Tên khách hàng: " . $name . "<br>";
        echo "Tổng tiền: $" . number_format($order['TotalAmount'], 2) . "<br>";
        echo "Trạng thái đơn hàng: <strong>" . $order['StatusOrder'] . "</strong><br>";
        echo "Trạng thái thanh toán: <strong>" . $order['PaymentStatus'] . "</strong><br>";
    } else {
        echo "Không tìm thấy đơn hàng.";
    }
} else {
    echo "Không có mã đơn hàng.";
}
?>
