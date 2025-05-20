<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = mysqli_connect("localhost", "root", "", "teav_shop1");
    if (!$conn) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }

    // Nhận dữ liệu từ form
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $products = $_POST['products']; // Định dạng: "1:2,3:1"
    $paymentInput = mysqli_real_escape_string($conn, $_POST['payment']); // Có thể là ID hoặc tên phương thức
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $orderDate = date('Y-m-d H:i:s');

    // Tìm PaymentId
    if (is_numeric($paymentInput)) {
        $paymentId = (int)$paymentInput;
        $checkPayment = mysqli_query($conn, "SELECT 1 FROM Payment WHERE PaymentId = $paymentId");
        if (mysqli_num_rows($checkPayment) === 0) {
            die("Phương thức thanh toán không hợp lệ.");
        }
    } else {
        $result = mysqli_query($conn, "SELECT PaymentId FROM Payment WHERE PaymentMethod = '$paymentInput'");
        if ($row = mysqli_fetch_assoc($result)) {
            $paymentId = $row['PaymentId'];
        } else {
            die("Không tìm thấy phương thức thanh toán: $paymentInput");
        }
    }

    // Tính tổng tiền từ danh sách sản phẩm
    $total = 0;
    $productList = explode(",", $products);
    foreach ($productList as $item) {
        list($pid, $qty) = explode(":", $item);
        $pid = (int)$pid;
        $qty = (int)$qty;

        $res = mysqli_query($conn, "SELECT Price FROM Product WHERE ProductId = $pid");
        if ($row = mysqli_fetch_assoc($res)) {
            $total += $row['Price'] * $qty;
        } else {
            die("Không tìm thấy sản phẩm ID: $pid");
        }
    }

    // Thêm đơn hàng
        $insertOrder = mysqli_query($conn, "
            INSERT INTO Orders (Email, OrderDate, TotalAmount, PaymentId, StatusOrder)
            VALUES ('$email', '$orderDate', $total, '$paymentId', '$status')
        ");

    if (!$insertOrder) {
        die("Lỗi thêm đơn hàng: " . mysqli_error($conn));
    }

    $orderId = mysqli_insert_id($conn); // Lấy OrderId vừa thêm

    // Thêm chi tiết sản phẩm
    foreach ($productList as $item) {
        list($pid, $qty) = explode(":", $item);
        $pid = (int)$pid;
        $qty = (int)$qty;
        mysqli_query($conn, "
            INSERT INTO OrderProduct (OrderId, ProductId, Quantity)
            VALUES ($orderId, $pid, $qty)
        ");
    }

    mysqli_close($conn);
    header("Location: order-admin.php");
    exit;
}
?>
