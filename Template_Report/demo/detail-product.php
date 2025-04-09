<?php
  $namePage = "Product Detail";
  include "view/header.php";
?>

<?php
    $conn = mysqli_connect("localhost", "root", "", "teav_shop");
    if (!$conn) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
    $query = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
    if (!$product) {
        die("Sản phẩm không tồn tại!");
    }
?>

<main>
    <section class="product-detail py-5">
      <div class="container">
        <h1 class="text-center mb-4"><?php echo $product['name']; ?></h1>
        <div class="row">
          <div class="col-md-6">
            <img
              src="<?php echo $product['image']; ?>"
              class="img-fluid"
              alt="<?php echo $product['name']; ?>"
            />
          </div>
          <div class="col-md-6">
            <h3>Details</h3>
            <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
            <p><strong>Quantity:</strong> <?php echo $product['quantity']; ?></p>
            <p><strong>Type:</strong> <?php echo ucfirst($product['type']); ?></p>
            <p><strong>Ingredients:</strong> <?php echo $product['ingredients']; ?></p>
            <p><strong>Uses:</strong> <?php echo $product['usefor']; ?></p>
            <p><strong>Description:</strong> <?php echo $product['description']; ?></p>
            <a href="products.php" class="btn btn-secondary">Back to Products</a>
          </div>
        </div>
      </div>
    </section>
</main>

<?php
    mysqli_close($conn);
?>

<?php 
    include "view/footer.php";
?>