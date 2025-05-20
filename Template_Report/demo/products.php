<?php
$namePage = "Sản phẩm";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy tham số lọc
$category_id = isset($_GET['category_id']) ? mysqli_real_escape_string($conn, $_GET['category_id']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';
$use_filter = isset($_GET['use']) ? $_GET['use'] : '';

// Lấy thông tin danh mục (nếu có category_id)
$category = null;
if ($category_id) {
    $category_query = "SELECT Name, Description FROM Categories WHERE CategoryId = '$category_id'";
    $category_result = mysqli_query($conn, $category_query);
    if (!$category_result) {
        die("Kết nối thất bại: " . mysqli_error($conn));
    }
    $category = mysqli_fetch_assoc($category_result);
}

// Thiết lập phân trang
$products_per_page = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $products_per_page;

// Xây dựng truy vấn sản phẩm
$query = "SELECT 
            product.ProductId,
            product.Name,
            product.Price,
            product.ImgUrl,
            product.Usefor,
            GROUP_CONCAT(ingredients.IngreName SEPARATOR ', ') AS ingredients
          FROM product
          LEFT JOIN productingredient ON product.ProductId = productingredient.ProductId
          LEFT JOIN ingredients ON productingredient.IngredientId = ingredients.IngredientId
          WHERE product.IsShow = 'Yes'";

// Áp dụng bộ lọc
$where_clauses = [];
if ($category_id) {
    $where_clauses[] = "product.CategoryId = '$category_id'";
}
if ($search) {
    $where_clauses[] = "product.Name LIKE '%$search%'";
}
if ($price_filter === 'low') {
    $where_clauses[] = "product.Price < 150";
} elseif ($price_filter === 'mid') {
    $where_clauses[] = "product.Price BETWEEN 150 AND 300";
} elseif ($price_filter === 'high') {
    $where_clauses[] = "product.Price > 300";
}
if ($use_filter === 'relax') {
    $where_clauses[] = "LOWER(product.Usefor) REGEXP 'relax|sleep|unwind|calm|soothe'";
} elseif ($use_filter === 'digestion') {
    $where_clauses[] = "LOWER(product.Usefor) REGEXP 'warm|digestion|cozy|healthy'";
} elseif ($use_filter === 'energy') {
    $where_clauses[] = "LOWER(product.Usefor) NOT REGEXP 'relax|sleep|unwind|calm|soothe|warm|digestion|cozy|healthy'";
}

if (!empty($where_clauses)) {
    $query .= " AND " . implode(" AND ", $where_clauses);
}

$query .= " GROUP BY product.ProductId";

$count_query = "SELECT COUNT(DISTINCT product.ProductId) AS total 
                FROM product 
                LEFT JOIN productingredient ON product.ProductId = productingredient.ProductId
                LEFT JOIN ingredients ON productingredient.IngredientId = ingredients.IngredientId
                WHERE product.IsShow = 'Yes'";
if (!empty($where_clauses)) {
    $count_query .= " AND " . implode(" AND ", $where_clauses);
}
$count_result = mysqli_query($conn, $count_query);
if (!$count_result) {
    die("Kết nối thất bại: " . mysqli_error($conn));
}
$total_products = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_products / $products_per_page);

// Thêm giới hạn và offset cho phân trang
$query .= " LIMIT $products_per_page OFFSET $offset";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Kết nối thất bại: " . mysqli_error($conn));
}

// Tạo chuỗi tham số cho liên kết phân trang
$query_string = http_build_query([
    'category_id' => $category_id,
    'search' => $search,
    'price' => $price_filter,
    'use' => $use_filter
]);
?>

<link rel="stylesheet" href="layout/css/style_products.css"/>

<main>
    <section class="products py-5">
      <div class="container">
        <h1 class="text-center mb-4"><?php echo $category ? htmlspecialchars($category['Name']) : 'Tất cả sản phẩm'; ?></h1>
        <?php if ($category): ?>
          <p class="text-center mb-4"><?php echo htmlspecialchars($category['Description']); ?></p>
        <?php endif; ?>

        <form method="GET" action="">
          <?php if ($category_id): ?>
            <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>">
          <?php endif; ?>
          <div class="filter-section">
            <div class="row g-3">
              <div class="col-md-4">
                <input
                  type="text"
                  class="form-control"
                  name="search"
                  placeholder="Tìm kiếm theo tên..."
                  value="<?php echo htmlspecialchars($search); ?>"
                />
              </div>
              <div class="col-md-3">
                <select class="form-select" name="price">
                  <option value="">Lọc theo giá tiền</option>
                  <option value="low" <?php echo $price_filter === 'low' ? 'selected' : ''; ?>>Dưới 150.000 VND</option>
                  <option value="mid" <?php echo $price_filter === 'mid' ? 'selected' : ''; ?>>150.000 VND - 300.000 VND</option>
                  <option value="high" <?php echo $price_filter === 'high' ? 'selected' : ''; ?>>Trên 300.000 VND</option>
                </select>
              </div>
              <div class="col-md-3">
                <select class="form-select" name="use">
                  <option value="">Lọc theo công dụng</option>
                  <option value="relax" <?php echo $use_filter === 'relax' ? 'selected' : ''; ?>>Thư giãn</option>
                  <option value="energy" <?php echo $use_filter === 'energy' ? 'selected' : ''; ?>>Năng lượng</option>
                  <option value="digestion" <?php echo $use_filter === 'digestion' ? 'selected' : ''; ?>>Tiêu hóa</option>
                </select>
              </div>
              <div class="col-md-2 text-center mt-3">
                <button class="btn btn-primary" type="submit">Lọc</button>
              </div>
            </div>
          </div>
        </form>

        <?php if ($total_products > 0): ?>
          <div class="row" id="productList">
            <?php while ($product = mysqli_fetch_assoc($result)) { ?>
              <div class="col-md-4 product-card">
                <div class="card">
                  <img
                    src="<?php echo htmlspecialchars($product['ImgUrl']); ?>"
                    class="card-img-top"
                    alt="<?php echo htmlspecialchars($product['Name']); ?>"
                  />
                  <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['Name']); ?></h5>
                    <p class="card-text">
                      <strong>Thành phần:</strong> <?php echo htmlspecialchars($product['ingredients']); ?><br />
                      <strong>Giá:</strong> <?php echo number_format($product['Price'], 3); ?> VND
                    </p>
                    <a href="detail-product.php?id=<?php echo htmlspecialchars($product['ProductId']); ?>" class="btn btn-success">Xem chi tiết</a>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        <?php else: ?>
          <p class="text-center mt-4">Không tìm thấy sản phẩm.</p>
        <?php endif; ?>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
          <nav aria-label="Product pagination">
            <ul class="pagination justify-content-center mt-4">
              <!-- Nút trước -->
              <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $query_string ? '&' . $query_string : ''; ?>" aria-label="Previous">
                  <span aria-hidden="true" style="color:rgb(9, 65, 7)">«</span>
                </a>
              </li>
              <!-- Số trang -->
              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                  <a class="page-link" href="?page=<?php echo $i; ?><?php echo $query_string ? '&' . $query_string : ''; ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>
              <!-- Nút sau -->
              <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $query_string ? '&' . $query_string : ''; ?>" aria-label="Next">
                  <span aria-hidden="true" style="color:rgb(9, 65, 7)">»</span>
                </a>
              </li>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    </section>
</main>
<!-- footer -->
<?php 
  include "view/footer.php";
  mysqli_close($conn);
?>