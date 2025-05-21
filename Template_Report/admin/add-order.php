<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = mysqli_connect("localhost", "root", "", "teav_shop1");
    if (!$conn) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }

    // Nhận dữ liệu từ form
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $products = $_POST['products']; // VD: "1:2,3:1" hoặc ['1:2', '3:1']
    $paymentInput = mysqli_real_escape_string($conn, $_POST['payment']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $orderDate = date('Y-m-d H:i:s');

    // Lấy PaymentId từ input
$paymentStatus = '';
$paymentId = null;

if (is_numeric($paymentInput)) {
    $paymentId = (int)$paymentInput;
    $query = "SELECT PaymentStatus FROM Payment WHERE PaymentId = $paymentId";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $paymentStatus = $row['PaymentStatus'];
    } else {
        die("Phương thức thanh toán không hợp lệ.");
    }
} else {
    $query = "SELECT PaymentId, PaymentStatus FROM Payment WHERE PaymentMethod = '$paymentInput'";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $paymentId = $row['PaymentId']; // Gán đúng PaymentId từ phương thức
        $paymentStatus = $row['PaymentStatus'];
    } else {
        die("Không tìm thấy phương thức thanh toán: $paymentInput");
    }
}

    // Chuyển products thành mảng nếu là chuỗi
    $productList = is_array($products) ? $products : explode(",", $products);

    // Tính tổng tiền
    $total = 0;
    foreach ($productList as $item) {
        $parts = explode(":", $item);
        if (count($parts) < 2) continue; // Bỏ qua nếu không hợp lệ

        $pid = (int)$parts[0];
        $qty = (int)$parts[1];

        $res = mysqli_query($conn, "SELECT Price FROM Product WHERE ProductId = $pid");
        if ($row = mysqli_fetch_assoc($res)) {
            $total += $row['Price'] * $qty;
        } else {
            die("Không tìm thấy sản phẩm ID: $pid");
        }
    }

    // Tạo mã OrderId mới theo dạng OD001, OD002, ...
    $prefix = "ORD";
    $newOrderId = "ORD001"; // Mặc định
    $query = mysqli_query($conn, "SELECT OrderId FROM Orders WHERE OrderId LIKE '{$prefix}%' ORDER BY OrderId DESC LIMIT 1");
    if ($row = mysqli_fetch_assoc($query)) {
        $lastNumber = (int)substr($row['OrderId'], strlen($prefix));
        $newOrderId = $prefix . str_pad($lastNumber + 1, 3, "0", STR_PAD_LEFT);
    }

    // Thêm đơn hàng vào bảng Orders
    $insertOrder = mysqli_query($conn, "
        INSERT INTO Orders (OrderId, Email, OrderDate, TotalAmount, PaymentId, StatusOrder)
        VALUES ('$newOrderId', '$email', '$orderDate', $total, '$paymentId', '$status')
    ");

    if (!$insertOrder) {
        die("Lỗi thêm đơn hàng: " . mysqli_error($conn));
    }

    // Thêm từng sản phẩm vào bảng OrderProduct
    foreach ($productList as $item) {
    $parts = explode(":", $item);
    if (count($parts) < 2) continue; // Bỏ qua nếu không hợp lệ

    $pid = (int)$parts[0];
    $qty = (int)$parts[1];

    // Kiểm tra tồn tại trước khi insert
    $checkProduct = mysqli_query($conn, "SELECT 1 FROM Product WHERE ProductId = $pid");
    if (mysqli_num_rows($checkProduct) === 0) {
        die("Không thể thêm sản phẩm không tồn tại (ID: $pid)");
    }

    $insertDetail = mysqli_query($conn, "
        INSERT INTO OrderProduct (OrderId, ProductId, Quantity)
        VALUES ('$newOrderId', $pid, $qty)
    ");

    if (!$insertDetail) {
        die("Lỗi thêm sản phẩm vào đơn hàng: " . mysqli_error($conn));
    }
}

    // Xong
// Xong
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm đơn hàng thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-success shadow p-4 rounded-4 text-center">
        <h2 class="mb-3"><i class="bi bi-check-circle-fill text-success"></i> Đơn hàng đã được thêm thành công!</h2>
        <p class="fs-5">Trạng thái thanh toán: <strong><?= htmlspecialchars($paymentStatus) ?></strong></p>
        <a href="order-admin.php" class="btn btn-outline-primary mt-3">
            <i class="bi bi-arrow-left-circle"></i> Quay lại trang quản lý đơn hàng
        </a>
    </div>
</div>
</body>
</html>
<?php
exit;
}
?>