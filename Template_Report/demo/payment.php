<?php
$namePage = "Thanh toán";
include "view/header.php";

$totalFromCart = isset($_GET['total']) ? floatval($_GET['total']) : 100.00;
$discount = 0;
$couponMessage = '';
$couponCode = isset($_POST['coupon']) ? trim($_POST['coupon']) : '';
$finalTotal = $totalFromCart;

$errors = [];
$Name = '';
$email = '';
$address = '';
$phone_number = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'payment') {
    $Name = trim($_POST['Name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');

    // Validation
    if (empty($Name)) {
        $errors[] = 'Please enter your name';
    }
    if (empty($email)) {
        $errors[] = 'Please enter your email';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'This is not a valid email address';
    }
    if (empty($address)) {
        $errors[] = 'Please enter your address';
    } elseif (strlen($address) > 300) {
        $errors[] = 'Address must not exceed 300 characters';
    }
    if (empty($phone_number)) {
        $errors[] = 'Please enter your phone number';
    } elseif (!preg_match('/^\d{10}$/', $phone_number)) {
        $errors[] = 'Phone number must be exactly 10 digits';
    }

    if (empty($errors)) {
        header('Location: process_payment.php');
        exit;
    }
}
?>

<body>
    <div class="payment-container py-5">
        <h2>Payment Information</h2>
        
        <!-- Display Errors -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Payment Form -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?total=' . $totalFromCart; ?>" method="POST">
            <input type="hidden" name="form_type" value="payment">
            
            <!-- Customer Information -->
            <div class="form-group">
                <label for="Name">Name</label>
                <input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($Name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
            </div>

            <!-- Product -->
            <div class="form-group">
                <label for="product">Product</label>
                
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($totalFromCart, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Discount:</span>
                    <span>$<?php echo number_format($discount, 2); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($finalTotal, 2); ?></span>
                </div>
                <input type="hidden" name="finalTotal" value="<?php echo $finalTotal; ?>">
            </div>

            <!-- Form Buttons -->
            <div class="form-buttons">
                <a href="cart.php" class="btn btn-cancel">Back to Cart</a>
                <button type="submit" class="btn btn-submit">Confirm Payment</button>
            </div>
        </form>
    </div>

<?php
include "view/footer.php";
?>