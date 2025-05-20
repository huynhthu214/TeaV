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

// Bây giờ mới bắt đầu xuất nội dung HTML
include "view/header.php"; 

$conn = mysqli_connect("localhost", "root", "", "teav_shop1"); 
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style-cart.css">
    <title><?= $namePage ?></title>
    <script>
    // Hàm JavaScript để tăng/giảm số lượng mà không cần tải lại trang
    function updateQuantity(action, productId) {
        const quantityElement = document.getElementById('qty-' + productId);
        let currentQty = parseInt(quantityElement.textContent);
        
        if (action === 'increase') {
            currentQty += 1;
        } else if (action === 'decrease') {
            currentQty -= 1;
            if (currentQty <= 0) {
                if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    window.location.href = 'cart.php?delete=' + productId;
                    return;
                } else {
                    currentQty = 1; // Nếu người dùng hủy xóa, giữ số lượng tối thiểu là 1
                }
            }
        }
        
        // Gửi request AJAX để cập nhật session
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'update-cart.php?action=' + action + '&id=' + productId, true);
        xhr.onload = function() {
            if (this.status === 200) {
                try {
                    const response = JSON.parse(this.responseText);
                    
                    if (response.status === 'success') {
                        // Cập nhật số lượng hiển thị
                        quantityElement.textContent = currentQty;
                        
                        // Cập nhật tổng tiền của sản phẩm này
                        const priceElement = document.getElementById('price-' + productId);
                        const price = parseFloat(priceElement.getAttribute('data-price'));
                        const subtotalElement = document.getElementById('subtotal-' + productId);
                        subtotalElement.textContent = '$' + (price * currentQty).toFixed(2);
                        
                        // Cập nhật tổng tiền giỏ hàng
                        updateCartTotal();
                    } else if (response.status === 'deleted') {
                        // Nếu sản phẩm bị xóa (số lượng = 0), tải lại trang
                        window.location.reload();
                    } else {
                        console.error('Lỗi: ' + response.message);
                    }
                } catch (e) {
                    console.error('Lỗi xử lý phản hồi: ' + e.message);
                    console.log('Phản hồi nhận được: ' + this.responseText);
                }
            }
        };
        xhr.onerror = function() {
            console.error('Lỗi kết nối tới server');
        };
        xhr.send();
    }
    
    // Hàm tính lại tổng tiền giỏ hàng
    function updateCartTotal() {
        let total = 0;
        const subtotals = document.querySelectorAll('[id^="subtotal-"]');
        subtotals.forEach(function(element) {
            const value = parseFloat(element.textContent.replace('$', ''));
            total += value;
        });
        document.getElementById('cart-total').textContent = '$' + total.toFixed(2);
    }
    
    // Thêm sự kiện DOMContentLoaded để đảm bảo tất cả các phần tử đều đã tải
    document.addEventListener('DOMContentLoaded', function() {
        // Gắn sự kiện cho các nút tăng giảm
        const plusButtons = document.querySelectorAll('.plus-btn');
        const minusButtons = document.querySelectorAll('.minus-btn');
        
        plusButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                updateQuantity('increase', productId);
            });
        });
        
        minusButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                updateQuantity('decrease', productId);
            });
        });
    });
    </script>
</head>
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
                                    $<?= number_format($price, 2) ?>
                                </span>
                            </td>
                            <td class="quantity">
                                <div class="quantity-control">
                                    <button type="button" class="quantity-btn minus-btn" onclick="updateQuantity('decrease', <?= $item['id'] ?>)">-</button>
                                    <span class="quantity-display" id="qty-<?= $item['id'] ?>"><?= $quantity ?></span>
                                    <button type="button" class="quantity-btn plus-btn" onclick="updateQuantity('increase', <?= $item['id'] ?>)">+</button>
                                </div>
                            </td>
                            <td class="subtotal">
                                <span id="subtotal-<?= $item['id'] ?>">$<?= number_format($subtotal, 2) ?></span>
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
                            <td colspan="2"><strong class="cart-total" id="cart-total">$<?= number_format($total, 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="cart-actions">
                <a href="products.php" class="continue-shopping">Tiếp tục mua sắm</a>
                <a href="payment.php" class="checkout-btn">Thanh toán</a>
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