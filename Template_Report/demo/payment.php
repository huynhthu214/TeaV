<?php
$namePage = "Thanh toán";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$totalFromCart = isset($_GET['total']) ? floatval($_GET['total']) : 100.00;
$discount = 0;
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'info' && isset($_POST['field'])) {
    $field = $_POST['field'];
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
        $sql = "UPDATE account SET $updateField = ? WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $newValue, $_SESSION['email']);
        $stmt->execute();
        $stmt->close();

        $_SESSION['update_' . $field] = true;
        header("Location: payment.php?total=" . $totalFromCart);
        exit;
    }
}
?>

<div class="payment-container py-5">
    <h2>Thông tin </br> thanh toán</h2>

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
            <form method="POST" action="payment.php?total=<?php echo $totalFromCart; ?>" id="mainForm">
            <input type="hidden" name="step" value="info">
            <input type="hidden" name="field" id="fieldInput">

            <!-- Họ tên -->
            <div class="form-group mb-3">
                <label for="name" class="form-label fw-semibold">Họ và tên</label>
                <div class="input-group">
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" readonly onblur="submitField('name')">
                    <button type="button" class="btn btn-secondary" onclick="enableEdit('name')"><i class="fas fa-pen"></i></button>
                </div>
                <?php if (!empty($_SESSION['update_name'])): ?>
                    <div class="alert alert-success mt-2">Cập nhật họ tên thành công</div>
                    <?php unset($_SESSION['update_name']); ?>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div class="form-group mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly onblur="submitField('email')">
                    <button type="button" class="btn btn-secondary" onclick="enableEdit('email')"><i class="fas fa-pen"></i></button>
                </div>
                <?php if (!empty($_SESSION['update_email'])): ?>
                    <div class="alert alert-success mt-2">Cập nhật email thành công</div>
                    <?php unset($_SESSION['update_email']); ?>
                <?php endif; ?>
            </div>

            <!-- Địa chỉ -->
            <div class="form-group mb-3">
                <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                <div class="input-group">
                    <input type="text" name="address" id="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>" readonly onblur="submitField('address')">
                    <button type="button" class="btn btn-secondary" onclick="enableEdit('address')"><i class="fas fa-pen"></i></button>
                </div>
                <?php if (!empty($_SESSION['update_address'])): ?>
                    <div class="alert alert-success mt-2">Cập nhật địa chỉ thành công</div>
                    <?php unset($_SESSION['update_address']); ?>
                <?php endif; ?>
            </div>

            <!-- Số điện thoại -->
            <div class="form-group mb-3">
                <label for="phone_number" class="form-label fw-semibold">Số điện thoại</label>
                <div class="input-group">
                    <input type="text" name="phone_number" id="phone_number" class="form-control" value="<?php echo htmlspecialchars($phone_number); ?>" readonly onblur="submitField('phone_number')">
                    <button type="button" class="btn btn-secondary" onclick="enableEdit('phone_number')"><i class="fas fa-pen"></i></button>
                </div>
                <?php if (!empty($_SESSION['update_phone_number'])): ?>
                    <div class="alert alert-success mt-2">Cập nhật số điện thoại thành công</div>
                    <?php unset($_SESSION['update_phone_number']); ?>
                <?php endif; ?>
            </div>

            <!-- Các phần còn lại -->
            <div class="order-summary mt-4">
                <p><strong>Tổng tiền:</strong> $<?php echo number_format($finalTotal, 2); ?></p>
            </div>

            <div class="form-buttons mt-3">
                <a href="cart.php" class="btn btn-secondary">Quay lại giỏ hàng</a>
                <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
            </div>
        </form>

        <!-- BƯỚC 2: Hiển thị mã QR -->
        <?php elseif ($step === 'qr'): ?>
            <div class="alert alert-success">
                <strong> Thông tin hợp lệ! </strong> Vui lòng quét mã QR để thanh toán đơn hàng trị giá <strong>$<?php echo number_format($finalTotal, 2); ?></strong>
            </div>
            <div class="text-center">
                <img src="./layout/images/qrcode.png" alt="QR Code" style="max-width: 250px; border: 1px solid #ccc; padding: 10px;">
                <p class="text-muted mt-2">Sau khi thanh toán thành công, nhấn nút bên dưới để tiếp tục</p>
            </div>

            <!-- Giữ thông tin người dùng để gửi tiếp -->
            <input type="hidden" name="Name" value="<?php echo htmlspecialchars($name); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="address" value="<?php echo htmlspecialchars($address); ?>">
            <input type="hidden" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">
            <input type="hidden" name="finalTotal" value="<?php echo $finalTotal; ?>">

            <div class="form-buttons mt-4">
                <button type="submit" class="btn btn-success">Tôi đã thanh toán</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php include "view/footer.php"; ?>
