<?php
ob_start();
session_start();

if (!isset($_SESSION['email'])) {
    $returnUrl = urlencode($_SERVER['REQUEST_URI']); // ví dụ: detail-product.php?id=5
    header("Location: login.php?msg=login_required&return=" . $returnUrl);
    exit();
}

$namePage = "Chi tiết sản phẩm";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$productId = isset($_GET['id']) ? $_GET['id'] : null;
if (!$productId) {
    die("Lỗi: Không tìm thấy ID sản phẩm.");
}

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = floatval($_POST['product_price']);
    $product_image = $_POST['product_image'];

    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $max_quantity = isset($_POST['max_quantity']) ? intval($_POST['max_quantity']) : 1;
    $quantity = max(1, min($quantity, $max_quantity));

    $product = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price,
        'quantity' => $quantity,
        'image' => $product_image
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product['id']) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = $product;
    }

    $_SESSION['success_message'] = "Sản phẩm đã được thêm vào giỏ hàng!";
    header("Location: detail-product.php?id=$product_id");
    exit();
}

$query = "SELECT 
            product.ProductId,
            product.Name,
            product.Price,
            product.ImgUrl,
            categories.Name AS CategoryName,
            GROUP_CONCAT(ingredients.IngreName SEPARATOR ', ') AS ingredients,
            product.Usefor, 
            product.Description, 
            product.Quantity
          FROM product
          JOIN categories ON product.Categoryid = categories.Categoryid
          JOIN productingredient ON product.ProductId = productingredient.ProductId
          JOIN ingredients ON productingredient.IngredientId = ingredients.IngredientId
          WHERE product.ProductId = ? AND product.IsShow = 'Yes'
          GROUP BY product.ProductId";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    echo "Lỗi: Sản phẩm không tồn tại hoặc không có sẵn.";
    mysqli_close($conn);
    include "view/footer.php";
    exit();
}

$product = mysqli_fetch_assoc($result);
?>

<?php if (isset($_SESSION['success_message'])): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <?= $_SESSION['success_message']; ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<main>
    <section class="product-detail py-5">
        <div class="container">
            <h1 class="text-center mb-4"><?php echo htmlspecialchars($product['Name']); ?></h1>
            <div class="row">
                <div class="col-md-6">
                    <div id="carouselExample" class="carousel slide">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="<?php echo htmlspecialchars($product['ImgUrl']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($product['Name']); ?>">
                            </div>
                            <div class="carousel-item">
                                <img src="<?php echo htmlspecialchars($product['ImgUrl']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($product['Name']); ?>">
                            </div>
                            <div class="carousel-item">
                                <img src="<?php echo htmlspecialchars($product['ImgUrl']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($product['Name']); ?>">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="delivery-notice mb-3 p-2 bg-light text-success fw-bold">
                        Giao hàng miễn phí với đơn hàng trên 1.000.000 VND
                    </div>
                    
                    <h3>Chi tiết</h3>
                    <p><strong>Giá:</strong> <?php echo number_format($product['Price'], 3); ?> VND</p>
                    <p><strong>Số lượng sẵn có:</strong> <?php echo htmlspecialchars($product['Quantity']); ?></p>
                    <p><strong>Loại:</strong> <?php echo htmlspecialchars($product['CategoryName']); ?></p>
                    <p><strong>Thành phần:</strong> <?php echo htmlspecialchars($product['ingredients']); ?></p>
                    <p><strong>Công dụng:</strong> <?php echo htmlspecialchars($product['Usefor']); ?></p>
                    <p><strong>Mô tả:</strong> <?php echo htmlspecialchars($product['Description']); ?></p>

                    <div class="d-flex gap-2 mb-4">
                        <form method="post" action="detail-product.php?id=<?= htmlspecialchars($product['ProductId']) ?>">
                            <div class="quantity-selector d-flex align-items-center mb-3">
                                <div class="input-group" style="width: 130px">
                                    <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="<?php echo htmlspecialchars($product['Quantity']); ?>" step="1">
                                </div>
                            </div>
                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['ProductId']) ?>">
                            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['Name']) ?>">
                            <input type="hidden" name="product_price" value="<?= floatval($product['Price']) ?>">
                            <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['ImgUrl']) ?>">
                            <input type="hidden" name="max_quantity" value="<?= htmlspecialchars($product['Quantity']) ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">Thêm vào giỏ hàng</button>
                        </form>
                    </div>
                    <a href="products.php" class="btn btn-secondary">Trở về trang sản phẩm</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
include "view/footer.php";
ob_end_flush();
?>