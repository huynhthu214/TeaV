<?php 
session_start();
$namePage = "Giỏ hàng";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

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

if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $deleteId) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    header("Location: cart.php");
    exit;
}
?>

<body>
    <div class="cart-container py-5">
    <h2>Giỏ hàng của bạn</h2>
    <?php if (!empty($_SESSION['cart'])): ?>
    <table border="1" cellpadding="10">
        
            <tr>
                <th>Sản phẩm</th>
                <th>Giá (USD)</th>
                <th>Số lượng</th>
                <th>Tổng tiền</th>
                <th>Thao tác</th>
            </tr>
        
            <?php
           $total = 0;
           foreach ($_SESSION['cart'] as $item):
               $price = floatval(str_replace(',', '', $item['price']));
               $quantity = intval($item['quantity']);
               $subtotal = $price * $quantity;
               $total += $subtotal;
           ?>
           <tr>
           <td>
                
                   <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="50" height="50">
                   <?= htmlspecialchars($item['name']) ?>
               </td>
               <td>$<?= number_format($price, 2) ?></td>
               <td><?= $quantity ?></td>
               <td>$<?= number_format($subtotal, 2) ?></td>
               <td>
                   <a href="cart.php?delete=<?= $item['id'] ?>" class="btn btn-danger">Xóa</a>
               </td>
           </tr>
           <?php endforeach; ?>
           <tr>
               <td colspan="3"><strong>Total</strong></td>
               <td><strong>$<?= number_format($total, 2) ?></strong></td>
               <td></td>
           </tr>
    </table>
    <div class="col mt-3">
    <a href="payment.php" class="checkout-btn">Thanh toán</a>
    </div>
    <?php else: ?>
        <p>Giỏ hàng của bạn đang trống.</p>
    <?php endif; ?>
</div>
<?php 
    mysqli_close($conn);
    include "view/footer.php";
?>
</body>
