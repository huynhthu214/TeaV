<?php
$namePage = "Payment";
include "view/header.php";

$totalFromCart = isset($_GET['total']) ? floatval($_GET['total']) : 100.00;
$discount = 0;
$couponMessage = '';
$couponCode = isset($_POST['coupon']) ? trim($_POST['coupon']) : '';
$finalTotal = $totalFromCart;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($couponCode)) {
    if ($couponCode === 'DISCOUNT10') {
        $discount = $totalFromCart * 0.10; 
        $couponMessage = 'Coupon applied: 10% off!';
    } elseif ($couponCode === 'DISCOUNT20') {
        $discount = $totalFromCart * 0.20; 
        $couponMessage = 'Coupon applied: 20% off!';
    } else {
        $couponMessage = 'Invalid coupon code.';
    }
    $finalTotal = $totalFromCart - $discount;
}
?>

<body>
    <div class="payment-container py-5">
        <h2>PAYMENT INFORMATION</h2>
        
        <!-- Coupon Form -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?total=' . $totalFromCart; ?>" method="POST" class="coupon-form">
            <div class="form-group">
                <label for="coupon">Coupon Code</label>
                <div class="coupon-group">
                    <input type="text" id="coupon" name="coupon" value="<?php echo htmlspecialchars($couponCode); ?>" placeholder="Enter coupon code">
                    <button type="submit">Apply</button>
                </div>
                <?php if ($couponMessage): ?>
                    <p class="coupon-message" style="color: <?php echo strpos($couponMessage, 'Invalid') !== false ? 'red' : 'green'; ?>;">
                        <?php echo htmlspecialchars($couponMessage); ?>
                    </p>
                <?php endif; ?>
            </div>
        </form>

        <!-- Payment Form -->
        <form action="process_payment.php" method="POST">
            <!-- Customer Information -->
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>

            <!-- Payment Method Selection -->
            <div class="form-group">
                <label>Payment Method</label>
                <div class="payment-methods">
                    <label>
                        <input type="radio" name="paymentMethod" value="credit_card" checked>
                        Credit Card
                    </label>
                    <label>
                        <input type="radio" name="paymentMethod" value="paypal">
                        PayPal
                    </label>
                    <label>
                        <input type="radio" name="paymentMethod" value="cod">
                        Cash on Delivery
                    </label>
                </div>
            </div>

            <!-- Credit Card Details -->
            <div id="credit-card-details">
                <div class="form-group">
                    <label for="cardNumber">Card Number</label>
                    <input type="text" id="cardNumber" name="cardNumber" pattern="\d{16}" placeholder="16 digits">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="expiryDate">Expiry Date</label>
                        <input type="text" id="expiryDate" name="expiryDate" pattern="\d{2}/\d{2}" placeholder="MM/YY">
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" pattern="\d{3,4}" placeholder="3-4 digits">
                    </div>
                </div>
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