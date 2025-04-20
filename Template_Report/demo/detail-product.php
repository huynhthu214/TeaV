<?php
$namePage = "Product Details";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$productId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$productId) {
    die("Lỗi: Không tìm thấy ID sản phẩm.");
}

$query = "SELECT ProductId AS id, Name AS name, Price AS price, ImgUrl AS image, Type AS type, Ingredients AS ingredients, Usefor AS usefor, Description AS description, Quantity AS quantity 
          FROM Product 
          WHERE ProductId = ? AND Status = 'Available'";
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
      <h1 class="text-center mb-4"><?php echo htmlspecialchars($product['name']); ?></h1>
      <div class="row">
        <div class="col-md-6">
          <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="d-block w-100" alt="<?php echo $product['name']; ?>">
                    </div>
                    <div class="carousel-item">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="d-block w-100" alt="<?php echo $product['name']; ?>">
                    </div>

                    <div class="carousel-item">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="d-block w-100" alt="<?php echo $product['name']; ?>">
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
          <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
          <p><strong>Quantity:</strong> <?php echo $product['quantity']; ?></p>
          <p><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($product['type'])); ?></p>
          <p><strong>Ingredients:</strong> <?php echo htmlspecialchars($product['ingredients']); ?></p>
          <p><strong>Uses:</strong> <?php echo htmlspecialchars($product['usefor']); ?></p>
          <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
          
          <div class="d-flex gap-2 mb-4">
            <div class="quantity-selector d-flex align-items-center mb-3">
                <div class="input-group" style="width: 130px">
                    <input type="number" id="quantity" class="form-control text-center" value="1" min="0" max="<?php echo $product['quantity']; ?>" step="1">
                </div>
            </div>
            <button class="add-to-cart" id="addItem" data-product-id="<?php echo htmlspecialchars($product['id']); ?>">Add to cart</button>
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