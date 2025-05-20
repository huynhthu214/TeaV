<?php
ob_start();
$namePage = "Thanh toán";
include "view/header.php";

if (!empty($_SESSION['update_success'])): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
    <div id="liveToast" class="toast show align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Cập nhật thông tin thành công!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php unset($_SESSION['update_success']); ?>
<?php endif; 

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
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'info') {
    $field = $_POST['field'] ?? '';

    if (!empty($field)) {
        // ✅ Xử lý cập nhật từng trường thông tin
        $updateField = '';
        $newValue = '';

        switch ($field) {
            case 'name':
                $updateField = 'FullName';
                $newValue = trim($_POST['name']);
                break;
            case 'email':
                $updateField = 'Email';
                $newValue = trim($_POST['email']);
                break;
            case 'address':
                $updateField = 'Address';
                $newValue = trim($_POST['address']);
                break;
            case 'phone_number':
                $updateField = 'PhoneNumber';
                $newValue = trim($_POST['phone_number']);
                break;
        }

        if ($updateField && isset($_SESSION['email'])) {
            // Lấy giá trị hiện tại
            $sqlCheck = "SELECT $updateField FROM account WHERE Email = ?";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->bind_param("s", $_SESSION['email']);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $row = $resultCheck->fetch_assoc();
            $stmtCheck->close();

            $currentValue = $row[$updateField];

            // Nếu có thay đổi mới cập nhật
            if ($newValue !== $currentValue) {
                $sql = "UPDATE account SET $updateField = ? WHERE Email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $newValue, $_SESSION['email']);
                $stmt->execute();
                $stmt->close();

                $_SESSION['update_success'] = true;
                header("Location: payment.php?total=" . $totalFromCart);
                exit;
            }
        }

    } else {
        // ✅ Xử lý khi bấm nút "Xác nhận thanh toán"
        header("Location: payment-qr.php?total=" . $finalTotal);
        exit;
    }
}
ob_end_flush();
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

    <!-- BƯỚC 1: Nhập thông tin -->
    <?php if ($step === 'info'): ?>
        <form method="POST" action="payment.php" id="mainForm">
            <input type="hidden" name="step" value="info">
            <input type="hidden" name="field" id="fieldInput">

            <!-- Họ tên -->
            <div class="form-group mb-3">
                <label for="name" class="form-label fw-semibold">Họ và tên</label>
                <div class="input-group">
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" readonly onblur="submitField('name')">
                    <button type="button" class="btn btn-secondary" onclick="enableEdit('name')"><i class="fas fa-pen"></i></button>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly onblur="submitField('email')">
                    <button type="button" class="btn btn-secondary" onclick="enableEdit('email')"><i class="fas fa-pen"></i></button>
                </div>
            </div>

            <!-- Địa chỉ -->
            <div class="form-group mb-3">
                <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                <div class="input-group">
                    <input type="text" name="address" id="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>" readonly onblur="submitField('address')">
                    <button type="button" class="btn btn-secondary" onclick="enableEdit('address')"><i class="fas fa-pen"></i></button>
                </div>
            </div>

            <!-- Số điện thoại -->
            <div class="form-group mb-3">
                <label for="phone_number" class="form-label fw-semibold">Số điện thoại</label>
                <div class="input-group">
                    <input type="text" name="phone_number" id="phone_number" class="form-control" value="<?php echo htmlspecialchars($phone_number); ?>" readonly onblur="submitField('phone_number')">
                    <button type="button" class="btn btn-secondary" onclick="enableEdit('phone_number')"><i class="fas fa-pen"></i></button>
                </div>
            </div>

            <!-- Tổng tiền -->
            <div class="order-summary mt-4">
                <p><strong>Tổng tiền:</strong> $<?php echo number_format($finalTotal, 2); ?></p>
            </div>

            <div class="form-buttons mt-3">
                <a href="cart.php" class="btn btn-secondary">Quay lại giỏ hàng</a>
                <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include "view/footer.php"; ?>
