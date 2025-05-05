<?php
$namePage = "Product Details";
include "view/header.php";

session_start();

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$productId = isset($_GET['id']) ? $_GET['id'] : null;
if (!$productId) {
    die("Lỗi: Không tìm thấy ID sản phẩm.");
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

    // Quay lại trang hiện tại
    header("Location: detail-product.php?id=$product_id&added=1");
    exit;
}

$query = "SELECT 
            product.ProductId,
            product.Name,
            product.Price,
            product.ImgUrl,
            product.Type,
            GROUP_CONCAT(ingredients.IngreName SEPARATOR ', ') AS ingredients,
            product.Usefor, 
            product.Description, 
            product.Quantity
          FROM product 
          JOIN productingredient ON product.ProductId = productingredient.ProductId
          JOIN ingredients ON productingredient.IngredientId = ingredients.IngredientId
          WHERE product.ProductId = ? AND product.IsShow = 'Yes'
          GROUP BY product.ProductId";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Lỗi: Sản phẩm không tồn tại hoặc không có sẵn.");
}

$product = mysqli_fetch_assoc($result);
?>

<main>
  <section class="product-detail py-5">
    <div class="container">
      <h1 class="text-center mb-4"><?php echo htmlspecialchars($product['Name']); ?></h1>
      <div class="row">
        <div class="col-md-6">
          <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?php echo htmlspecialchars($product['ImgUrl']); ?>" class="d-block w-100" alt="<?php echo $product['Name']; ?>">
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo htmlspecialchars($product['ImgUrl']); ?>" class="d-block w-100" alt="<?php echo $product['Name']; ?>">
                    </div>

                    <div class="carousel-item">
                        <img src="<?php echo htmlspecialchars($product['ImgUrl']); ?>" class="d-block w-100" alt="<?php echo $product['Name']; ?>">
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
            Free Vietnam Delivery on Vietnam Orders Over $35
          </div>
          
          <h3>Details</h3>
          <p><strong>Price:</strong> $<?php echo number_format($product['Price'], 2); ?></p>
          <p><strong>Quantity:</strong> <?php echo $product['Quantity']; ?></p>
          <p><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($product['Type'])); ?></p>
          <p><strong>Ingredients:</strong> <?php echo htmlspecialchars($product['ingredients']); ?></p>
          <p><strong>Description:</strong> <?php echo htmlspecialchars($product['Description']); ?></p>
          
          
          <div class="d-flex gap-2 mb-4">
            <div class="quantity-selector d-flex align-items-center mb-3">
                <div class="input-group" style="width: 130px">
                    <input type="number" id="quantity" class="form-control text-center" value="1" min="0" max="<?php echo $product['Quantity']; ?>" step="1">
                </div>
            </div>
            <form method="post" action="detail-product.php?id=<?= $product['ProductId'] ?>">
              <input type="hidden" name="product_id" value="<?= $product['ProductId'] ?>">
              <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['Name']) ?>">
              <input type="hidden" name="product_price" value="<?= floatval($product['Price']) ?>">
              <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['ImgUrl']) ?>">
              <button type="submit" name="add_to_cart" class="btn btn-primary mt-2">Add to Cart</button>
             </form>

              <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
                  <div id="addedAlert" class="alert mt-2"></div>
              <?php endif; ?>
          </div>
          <a href="product.php" class="back">Back to Products</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
include "view/footer.php";
?>