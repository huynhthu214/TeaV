<?php 
session_start();
$namePage = "Giỏ hàng";

// Xử lý các hành động giỏ hàng trước khi bất kỳ output nào được gửi
// Đây là quan trọng để tránh lỗi "headers already sent"

// Xóa sản phẩm khỏi giỏ hàng
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $deleteId) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    // Đánh lại chỉ số mảng sau khi xóa
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    
    // Chuyển hướng để tránh việc gửi lại form khi refresh
    header("Location: cart.php");
    exit;
}

// Thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = floatval($_POST['product_price']);
    $product_image = $_POST['product_image'];
    
    $product = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price,
        'quantity' => 1,
        'image' => $product_image
    ];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product['id']) {
            $item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = $product;
    }
    
    header("Location: cart.php");
    exit;
}

// Tăng số lượng sản phẩm
if (isset($_GET['increase'])) {
    $id = $_GET['increase'];
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $id) {
            $item['quantity'] += 1;
            break;
        }
    }
    header("Location: cart.php");
    exit;
}

// Giảm số lượng sản phẩm (xóa nếu số lượng = 0)
if (isset($_GET['decrease'])) {
    $id = $_GET['decrease'];
    foreach ($_SESSION['cart'] as $key => &$item) {
        if ($item['id'] == $id) {
            $item['quantity'] -= 1;
            // Nếu số lượng = 0, xóa sản phẩm
            if ($item['quantity'] <= 0) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
            break;
        }
    }
    header("Location: cart.php");
    exit;
}

include "view/header.php"; 

$conn = mysqli_connect("localhost", "root", "", "teav_shop1"); 
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
?>

<body>
    <div class="cart-container py-5">
        <h2 class="text-center mb-4">Giỏ hàng của bạn</h2>
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="cart-table-container">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá (USD)</th>
                            <th>Số lượng</th>
                            <th>Tổng tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item):
                            $price = floatval(str_replace(',', '', $item['price']));
                            $quantity = intval($item['quantity']);
                            $subtotal = $price * $quantity;
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td class="product-info">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                                <span class="product-name"><?= htmlspecialchars($item['name']) ?></span>
                            </td>
                            <td class="price">
                                <span id="price-<?= $item['id'] ?>" data-price="<?= $price ?>">
                                    <?= number_format($price, 3) ?> VND
                                </span>
                            </td>
                            <td class="quantity">
                                <div class="quantity-control">
                                    <span class="quantity-display" id="qty-<?= $item['id'] ?>"><?= $quantity ?></span>
                                </div>
                            </td>
                            <td class="subtotal">
                                <span id="subtotal-<?= $item['id'] ?>"><?= number_format($subtotal, 3) ?> VND</span>
                            </td>
                            <td class="actions">
                                <a href="cart.php?delete=<?= $item['id'] ?>" class="delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')">Xóa</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Tổng tiền</strong></td>
                            <td colspan="2"><strong class="cart-total" id="cart-total"><?= number_format($total, 3) ?> VND</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="cart-actions">
                <a href="products.php" class="continue-shopping">Tiếp tục mua sắm</a>
                <a href="order-detail.php" class="checkout-btn">Đặt hàng</a>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <p>Giỏ hàng của bạn đang trống.</p>
                <a href="products.php" class="continue-shopping">Quay lại mua sắm</a>
            </div>
        <?php endif; ?>
    </div>

<?php
mysqli_close($conn);
include "view/footer.php";
?>
</body>
</html>