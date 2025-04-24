<?php
$namePage = "Products";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$query = "SELECT 
            product.ProductId,
            product.Name,
            product.Price,
            product.ImgUrl,
            product.Type,
            GROUP_CONCAT(ingredients.IngreName SEPARATOR ', ') AS ingredients,
            product.Usefor
          FROM product
          LEFT JOIN productingredient ON product.ProductId = productingredient.ProductId
          LEFT JOIN ingredients ON productingredient.IngredientId = ingredients.IngredientId
          WHERE product.IsShow = 'Yes'
          GROUP BY product.ProductId";


$result = mysqli_query($conn, $query);

if (!$result) {
    die("Kết nối thất bại: " . mysqli_error($conn));
}
?>


<main>
    <section class="products py-5">
      <div class="container">
        <h1 class="text-center mb-4">Our Products</h1>

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
            <div class="col-md-2">
              <select class="form-select" id="herbalFilter">
                <option value="">Filter by Herbal Type</option>
                <option value="green">Green Tea</option>
                <option value="herbal">Herbal Tea</option>
                <option value="black">Black Tea (Spiced)</option>
                <option value="blacktea">Black Tea</option>
                <option value="oolong">Oolong Tea</option>
              </select>
            </div>
            <div class="col-md-2">
              <select class="form-select" id="priceFilter">
                <option value="">Filter by Price</option>
                <option value="low">Under $30</option>
                <option value="mid">$30 - $40</option>
                <option value="high">Above $40</option>
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
            <div class="col-md-2">
              <button class="btn btn-primary" id="searchButton" type="button">Search</button>
            </div>
          </div>
        </div>

        <!-- Product List -->
        <div class="row" id="productList">
          <?php while ($product = mysqli_fetch_assoc($result)) { 
            $useCategory = 'energy'; 
            $useforLower = strtolower($product['Usefor']);
            if (strpos($useforLower, 'relax') !== false || strpos($useforLower, 'sleep') !== false || 
                strpos($useforLower, 'unwind') !== false || strpos($useforLower, 'calm') !== false || 
                strpos($useforLower, 'soothe') !== false) {
                $useCategory = 'relax';
            } elseif (strpos($useforLower, 'warm') !== false || strpos($useforLower, 'digestion') !== false || 
                     strpos($useforLower, 'cozy') !== false || strpos($useforLower, 'healthy') !== false) {
                $useCategory = 'digestion';
            }
          ?>
            <div
              class="col-md-4 product-card"
              data-type="<?php 
                $type = strtolower($product['Type']);
                if (strpos($type, 'green') !== false) echo 'green';
                elseif (strpos($type, 'herbal') !== false) echo 'herbal';
                elseif (strpos($type, 'black tea (spiced)') !== false) echo 'black';
                elseif (strpos($type, 'black tea') !== false) echo 'blacktea';
                elseif (strpos($type, 'oolong') !== false) echo 'oolong';
              ?>"
              data-price="<?php 
                echo ($product['Price'] < 30 ? 'low' : ($product['Price'] <= 40 ? 'mid' : 'high')); 
              ?>"
              data-use="<?php echo $useCategory; ?>"
            >
              <div class="card">
                <img
                  src="<?php echo $product['ImgUrl']; ?>"
                  class="card-img-top"
                  alt="<?php echo $product['Name']; ?>"
                />
                <div class="card-body">
                  <h5 class="card-title"><?php echo $product['Name']; ?></h5>
                  <p class="card-text">
                    <strong>Ingredients:</strong> <?php echo $product['ingredients']; ?><br />
                    <strong>Price:</strong> $<?php echo number_format($product['Price'], 2); ?>
                  </p>
                  <a href="detail-product.php?id=<?php echo $product['ProductId']; ?>" class="btn btn-success">View Details</a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </section>
</main>
<!-- footer -->
<?php
  include "view/footer.php";
?>