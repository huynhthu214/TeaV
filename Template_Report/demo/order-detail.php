<?php
session_start();
$namePage = "Chi tiết đặt hàng";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
$cart = $_SESSION['cart'] ?? [];

$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
$errors = [];
$name = '';
$email = '';
$address = '';
$phone_number = '';
$step = $_POST['step'] ?? 'info';

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

    if (isset($_SESSION['order_info'])) {
    $order_info = $_SESSION['order_info'];
    $name = $order_info['fullname'] ?? $name;
    $email = $order_info['email'] ?? $email;
    $address = $order_info['address'] ?? $address;
    $phone_number = $order_info['phone'] ?? $phone_number;
}
}
// Chi phí giả định
$shipping_fee = 30000; 
$final_total = $total * 1000 + $shipping_fee;

// Nếu POST & không có trường 'field' (tức là bấm nút "Xác nhận đặt hàng")
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['field'])) {
    // Lưu thông tin form vào session
    $_SESSION['order_info'] = [
        'fullname' => $_POST['fullname'] ?? '',
        'email' => $_POST['email'] ?? '',
        'address' => $_POST['address'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'payment_method' => $_POST['payment_method'] ?? '',
        'shipping_method' => $_POST['shipping_method'] ?? '',
        'note' => $_POST['note'] ?? '',
    ];

    // Tính lại tổng tiền (hoặc bạn lấy từ POST nếu đã truyền sang)
    $total = $_SESSION['cart'] ?? [];
    $amount = 0;
    foreach ($total as $item) {
        $amount += $item['price'] * $item['quantity'];
    }
    $final_total = $amount * 1000 + 30000;

    // Chuyển sang trang QR
    header("Location: payment-qr.php?total=" . $final_total);
    exit;
}
?>

<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center fw-bold">Chi tiết đơn hàng</h2>

    <form action="place-order.php" method="POST" class="needs-validation" novalidate>

<!-- THÔNG TIN CÁ NHÂN -->
<div class="card mb-4 shadow">
    <div class="card-header bg-primary text-white"><strong>Thông tin cá nhân</strong></div>
    <div class="card-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Họ và tên</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($name) ?>" readonly style="pointer-events: none;">
        </div>
        <div class="col-md-6">
            <label class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($phone_number) ?>" readonly style="pointer-events: none;">
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly style="pointer-events: none;">
        </div>
        <div class="col-md-6">
            <label class="form-label">Địa chỉ giao hàng</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($address) ?>" readonly style="pointer-events: none;">
        </div>
    </div>
</div>

        <!-- DANH SÁCH SẢN PHẨM -->
        <div class="card mb-4 shadow">
            <div class="card-header bg-success text-white"><strong>Danh sách sản phẩm</strong></div>
            <div class="card-body p-0">
                <table class="table table-striped m-0">
                    <thead class="table-light">
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php foreach ($cart as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"
                                        style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                </td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end"><?= number_format($item['price'] * 1000) ?> VND</td>
                                <td class="text-end"><?= number_format($item['price'] * $item['quantity'] * 1000) ?> VND</td>
                            </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PHƯƠNG THỨC THANH TOÁN -->
        <div class="card mb-4 shadow">
            <div class="card-header bg-warning"><strong>Phương thức thanh toán & vận chuyển</strong></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Phương thức thanh toán</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="MoMo" <?= (isset($order_info['payment_method']) && $order_info['payment_method'] === 'MoMo') ? 'selected' : '' ?>>Ví MoMo (QR)</option>
                            <option value="ZaloPay" <?= (isset($order_info['payment_method']) && $order_info['payment_method'] === 'ZaloPay') ? 'selected' : '' ?>>ZaloPay (QR)</option>
                            <option value="VNPay" <?= (isset($order_info['payment_method']) && $order_info['payment_method'] === 'VNPay') ? 'selected' : '' ?>>VNPay (QR)</option>
                        </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phương thức vận chuyển</label>
                        <select name="shipping_method" class="form-select" required>
                            <option value="standard" <?= (isset($order_info['shipping_method']) && $order_info['shipping_method'] === 'standard') ? 'selected' : '' ?>>Giao hàng tiêu chuẩn (3-5 ngày)</option>
                            <option value="express" <?= (isset($order_info['shipping_method']) && $order_info['shipping_method'] === 'express') ? 'selected' : '' ?>>Giao hàng nhanh (1-2 ngày)</option>
                        </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Thời gian giao hàng dự kiến</label>
                    <input type="text" class="form-control" value="Dự kiến 3 - 5 ngày làm việc" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ghi chú đặt hàng (nếu có)</label>
                    <textarea name="note" class="form-control" rows="3" placeholder="Ví dụ: Giao sau giờ hành chính, đóng gói kỹ..."><?= htmlspecialchars($order_info['note'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        <!-- THÔNG TIN THANH TOÁN -->
        <div class="card mb-4 shadow">
    <div class="card-header bg-info text-white"><strong>Thông tin thanh toán</strong></div>
    <div class="card-body">
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between">
                <span>Tổng giá trị sản phẩm</span>
                <strong><?= number_format($total * 1000) ?> VND</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                <span>Phí vận chuyển</span>
                <strong><?= number_format($shipping_fee) ?> VND</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between bg-success text-white">
                <span><strong>Tổng thanh toán</strong></span>
                <strong><?= number_format($final_total) ?> VND</strong>
            </li>
        </ul>
    </div>
</div>

        <!-- NÚT XÁC NHẬN -->
        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-primary px-5">Xác nhận đặt hàng</button>
        </div>
    </form>
</div>


<?php include "view/footer.php"; ?>
