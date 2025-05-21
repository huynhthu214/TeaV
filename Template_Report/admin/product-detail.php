<?php
session_start();
$namePage = "Chi tiết sản phẩm";
include "view/header-admin.php";

$dsn = 'mysql:host=localhost;dbname=teav_shop1;charset=utf8';
$username = 'root';
$password = '';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    // Kiểm tra và lấy ProductId từ URL
    if (!isset($_GET['id'])) {
        echo "<div class='alert alert-danger'>Không tìm thấy sản phẩm.</div>";
        include "view/footer-admin.php";
        exit;
    }

    $productId = $_GET['id'];

    // Lấy thông tin sản phẩm
    $sqlProduct = "SELECT * FROM Product WHERE ProductId = ?";
    $stmt = $pdo->prepare($sqlProduct);
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "<div class='alert alert-warning'>Không tìm thấy thông tin sản phẩm.</div>";
        include "view/footer-admin.php";
        exit;
    }

    // Lấy danh sách nguyên liệu
    $sqlIngredients = "
        SELECT I.IngreName, I.Origin
        FROM ProductIngredient PI
        JOIN Ingredients I ON PI.IngredientId = I.IngredientId
        WHERE PI.ProductId = ?
    ";
    $stmtIng = $pdo->prepare($sqlIngredients);
    $stmtIng->execute([$productId]);
    $ingredients = $stmtIng->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>

<div class="content-wrapper">
  <h2 class="mb-4" style="color:rgb(10, 119, 52);"><strong>Thông tin sản phẩm</strong></h2>

  <div class="card mb-4 p-3">
    <div class="row">
      <div class="col-md-4 text-center">
        <img src="<?= htmlspecialchars($product['ImgUrl']) ?>" alt="<?= htmlspecialchars($product['Name']) ?>" class="img-fluid rounded shadow" style="max-height: 250px;">
      </div>
      <div class="col-md-8">
        <h5 class="mb-3"><?= htmlspecialchars($product['Name']) ?></h5>
        <div><strong>Mã sản phẩm:</strong> <?= htmlspecialchars($product['ProductId']) ?></div>
        <div><strong>Giá:</strong> <?= number_format($product['Price'], 3) ?> VND</div>
        <div><strong>Số lượng:</strong> <?= htmlspecialchars($product['Quantity']) ?></div>
        <div><strong>Mô tả:</strong> <?= nl2br(htmlspecialchars($product['Description'])) ?></div>
        <div><strong>Công dụng:</strong> <?= nl2br(htmlspecialchars($product['Usefor'])) ?></div>
        <div><strong>Giảm giá:</strong> <?= $product['SaleOff'] ? $product['SaleOff'] . '%' : 'Không' ?></div>
        <div><strong>Trạng thái hiển thị:</strong> 
          <span class="badge bg-<?= $product['IsShow'] === 'Yes' ? 'success' : 'secondary'; ?>">
            <?= $product['IsShow'] === 'Yes' ? 'Hiển thị' : 'Ẩn' ?>
          </span>
        </div>
        <div><strong>Cập nhật gần nhất:</strong> <?= date("d/m/Y H:i", strtotime($product['UpdatedAt'])) ?></div>
      </div>
    </div>
  </div>

  <div class="card p-3 mb-4">
    <h5 class="mb-3">Nguyên liệu</h5>
    <?php if (!empty($ingredients)): ?>
      <ul class="list-group list-group-flush">
        <?php foreach ($ingredients as $ing): ?>
          <li class="list-group-item">
            <?= htmlspecialchars($ing['IngreName']) ?> 
            (Nguồn gốc: <?= htmlspecialchars($ing['Origin']) ?>)
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="text-muted">Chưa có nguyên liệu cho sản phẩm này.</p>
    <?php endif; ?>
  </div>

  <a href="products-admin.php" class="btn btn-secondary mb-3">Quay lại</a>
</div>

<?php include "view/footer-admin.php"; ?>
