<?php 
session_start();
include "view/header-admin.php";

// Kết nối CSDL
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Xử lý thêm sản phẩm
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $productId = mysqli_real_escape_string($conn, $_POST['ProductId']);
    $name = mysqli_real_escape_string($conn, $_POST['Name']);
    $categoryId = mysqli_real_escape_string($conn, $_POST['CategoryId']);
    $unitId = mysqli_real_escape_string($conn, $_POST['UnitId']);
    $supplierId = mysqli_real_escape_string($conn, $_POST['SupplierId']);
    $quantity = (int) $_POST['Quantity'];
    $price = (float) $_POST['Price'];
    $updatedAt = date('Y-m-d H:i:s');

    $sql = "INSERT INTO Product (ProductId, Name, CategoryId, UnitId, SupplierId, Quantity, Price, UpdatedAt)
            VALUES ('$productId', '$name', '$categoryId', '$unitId', '$supplierId', $quantity, $price, '$updatedAt')";

    if (mysqli_query($conn, $sql)) {
        $message = "✅ Thêm sản phẩm mới thành công!";
    } else {
        $message = "❌ Lỗi khi thêm sản phẩm: " . mysqli_error($conn);
    }
}

// Lấy danh sách nhà cung cấp
$suppliers = [];
$resSup = mysqli_query($conn, "SELECT SupplierId, SupplierName FROM Suppliers");
while ($row = mysqli_fetch_assoc($resSup)) {
    $suppliers[] = $row;
}

// Lấy danh sách loại sản phẩm
$categories = [];
$resCat = mysqli_query($conn, "SELECT CategoryId, Name FROM Categories");
while ($row = mysqli_fetch_assoc($resCat)) {
    $categories[] = $row;
}

// Lấy danh sách đơn vị
$units = [];
$resUnit = mysqli_query($conn, "SELECT UnitId, Name FROM CalculationUnit");
while ($row = mysqli_fetch_assoc($resUnit)) {
    $units[] = $row;
}

// Lấy danh sách sản phẩm
$sql = "SELECT p.*, s.SupplierName, s.Address AS SupplierAddress, c.Name AS CategoryName, u.Name AS UnitName
        FROM Product p
        LEFT JOIN Suppliers s ON p.SupplierId = s.SupplierId
        LEFT JOIN Categories c ON p.CategoryId = c.CategoryId
        LEFT JOIN CalculationUnit u ON p.UnitId = u.UnitId";

$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content-wrapper">
  <div class="page-title d-flex justify-content-between align-items-start mb-4">
    <h2 class="text-success mt-2"><strong>Quản lý nhập hàng</strong></h2>
    <div>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="bi bi-plus-circle me-1"></i> Thêm sản phẩm mới
      </button>
    </div>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <?= $message ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-success text-center">
        <tr>
          <th>Mã SP</th>
          <th>Tên SP</th>
          <th>Loại</th>
          <th>Đơn vị</th>
          <th>Số lượng</th>
          <th>Giá</th>
          <th>Nhà cung cấp</th>
          <th>Địa chỉ</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $product): ?>
            <tr class="text-center">
              <td><?= htmlspecialchars($product['ProductId']) ?></td>
              <td class="text-start"><?= htmlspecialchars($product['Name']) ?></td>
              <td><?= htmlspecialchars($product['CategoryName']) ?></td>
              <td><?= htmlspecialchars($product['UnitName']) ?></td>
              <td><?= $product['Quantity'] ?></td>
              <td><?= number_format($product['Price'], 0, ',', '.') ?> đ</td>
              <td><?= htmlspecialchars($product['SupplierName']) ?></td>
              <td><?= htmlspecialchars($product['SupplierAddress']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center">Không có sản phẩm nào</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal thêm sản phẩm -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-success">Thêm sản phẩm mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body row g-3">
        <input type="hidden" name="add_product" value="1">
        <div class="col-md-6">
          <label class="form-label">Mã sản phẩm</label>
          <input type="text" class="form-control" name="ProductId" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tên sản phẩm</label>
          <input type="text" class="form-control" name="Name" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Loại sản phẩm</label>
          <select class="form-select" name="CategoryId" required>
            <option value="">-- Chọn loại --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['CategoryId'] ?>"><?= htmlspecialchars($cat['Name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Đơn vị tính</label>
          <select class="form-select" name="UnitId" required>
            <option value="">-- Chọn đơn vị --</option>
            <?php foreach ($units as $unit): ?>
              <option value="<?= $unit['UnitId'] ?>"><?= htmlspecialchars($unit['Name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Nhà cung cấp</label>
          <select class="form-select" name="SupplierId" required>
            <option value="">-- Chọn NCC --</option>
            <?php foreach ($suppliers as $sup): ?>
              <option value="<?= $sup['SupplierId'] ?>"><?= htmlspecialchars($sup['SupplierName']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Số lượng</label>
          <input type="number" class="form-control" name="Quantity" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Giá</label>
          <input type="number" step="0.01" class="form-control" name="Price" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Lưu</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
      </div>
    </form>
  </div>
</div>

<?php include "view/footer-admin.php"; ?>
