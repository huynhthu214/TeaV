<?php
  $namePage = "Products";
  include "view/header.php";
?>

<?php
// Kết nối database
$conn = mysqli_connect("localhost", "root", "", "teav_shop");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Truy vấn tất cả sản phẩm
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<main>
    <section class="products py-5">
      <div class="container">
        <h1 class="text-center mb-4">Our Tea Products</h1>

        <!-- Search and Filter Section -->
        <div class="filter-section">
          <div class="row g-3">
            <div class="col-md-4">
              <input
                type="text"
                class="form-control"
                id="searchInput"
                placeholder="Search by name..."
              />
            </div>
            <div class="col-md-3">
              <select class="form-select" id="herbalFilter">
                <option value="">Filter by Herbal Type</option>
                <option value="green">Green Tea</option>
                <option value="herbal">Herbal Tea</option>
                <option value="black">Black Tea</option>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-select" id="priceFilter">
                <option value="">Filter by Price</option>
                <option value="low">Under $10</option>
                <option value="mid">$10 - $20</option>
                <option value="high">Above $20</option>
              </select>
            </div>
            <div class="col-md-2">
              <select class="form-select" id="useFilter">
                <option value="">Filter by Use</option>
                <option value="relax">Relaxation</option>
                <option value="energy">Energy</option>
                <option value="digestion">Digestion</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Product List -->
        <div class="row" id="productList">
          <?php while ($product = mysqli_fetch_assoc($result)) { ?>
            <div
              class="col-md-4 product-card"
              data-type="<?php echo strtolower($product['type']); ?>"
              data-price="<?php echo ($product['price'] < 10 ? 'low' : ($product['price'] <= 20 ? 'mid' : 'high')); ?>"
              data-use="<?php echo strtolower($product['usefor']); ?>"
            >
              <div class="card">
                <img
                  src="<?php echo $product['image']; ?>"
                  class="card-img-top"
                  alt="<?php echo $product['name']; ?>"
                />
                <div class="card-body">
                  <h5 class="card-title"><?php echo $product['name']; ?></h5>
                  <p class="card-ingredients">Ingredients: <?php echo $product['ingredients']?></p>
                  <p class="card-ingredients">Uses: <?php echo $product['ingredients']?></p>
                  <p class="card-ingredients">Ingredients: <?php echo $product['ingredients']</p>
                  <strong>Uses:</strong> <?php echo $product['usefor']; ?><br />
                  <strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?>
                  <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-success">View Details</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </section>
</main>

<?php
// Đóng kết nối
mysqli_close($conn);
?>

<!-- footer -->
<?php 
    include "view/footer.php";
?>