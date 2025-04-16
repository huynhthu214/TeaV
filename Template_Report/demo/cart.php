<?php 
$namePage = "Cart";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$query = "SELECT ProductId AS id, Name AS name, Price AS price, ImgUrl AS image 
    FROM Product 
    WHERE Status = 'Available' 
    LIMIT 2";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<body>
    <div class="cart-container py-5">
        <h2>YOUR CART</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Operation</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result) > 0) {
                    while ($product = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <span><?php echo htmlspecialchars($product['name']); ?></span>
                            </div>
                        </td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <input type="number" value="1" min="1" style="border:none">
                        </td>
                        <td class="subtotal">$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <button class="delete-btn">Delete</button>
                        </td>
                    </tr>
                <?php 
                    }
                } else {
                    echo '<tr><td colspan="5" class="empty-cart">Your cart is empty!</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <div class="total">Total: <span id="total">$0.00</span></div>
        <div class="col">
          <a href="payment.php" class="checkout-btn">Proceed to Payment</a>
        </div>
     </div>
<?php 
mysqli_close($conn);
include "view/footer.php";
?>
