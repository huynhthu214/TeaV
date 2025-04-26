<?php 
session_start();
$namePage = "Cart";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = floatval($_POST['product_price']);

    $sql = "SELECT ImgUrl FROM product WHERE ProductId = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $product_image);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

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
    <h2>Your Cart</h2>
    <?php if (!empty($_SESSION['cart'])): ?>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price (USD)</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Operation</th>
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
           <td>
                
                   <img src="<?= htmlspecialchars($item['iamge']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="50" height="50">
                   <?= htmlspecialchars($item['name']) ?>
               </td>
               <td>$<?= number_format($price, 2) ?></td>
               <td><?= $quantity ?></td>
               <td>$<?= number_format($subtotal, 2) ?></td>
               <td>
                   <a href="cart.php?delete=<?= $item['id'] ?>" class="btn btn-danger">Delete</a>
               </td>
           </tr>
           <?php endforeach; ?>
           <tr>
               <td colspan="3"><strong>Total</strong></td>
               <td><strong>$<?= number_format($total, 2) ?></strong></td>
               <td></td>
           </tr>
        </tbody>
    </table>
    <div class="col mt-3">
    <a href="payment.php" class="checkout-btn">Proceed to Payment</a>
    </div>
    <?php else: ?>
        <p>Your cart is currently empty.</p>
    <?php endif; ?>
</div>
<?php 
mysqli_close($conn);
include "view/footer.php";
?>
