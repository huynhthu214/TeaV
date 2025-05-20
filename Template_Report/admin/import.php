<?php 
session_start();
$namePage = "Quản lý nhập hàng";
include "view/header-admin.php";

// Kết nối CSDL
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$products = [];
$result = mysqli_query($conn, "SELECT * FROM Product");
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
if (isset($_GET['update']) && $_GET['update'] == 'success') {
    echo '<div class="alert alert-success text-center">Cập nhật phiếu nhập thành công!</div>';
}
    // Xử lý thêm phiếu nhập
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_import'])) {
        $importId = 'IMP' . time();
        $importDate = date('Y-m-d H:i:s');
        $note = mysqli_real_escape_string($conn, $_POST['Note'] ?? '');
    $productName = mysqli_real_escape_string($conn, $_POST['ProductName']);
    $quantity = (int)$_POST['Quantity'];
    $unitPrice = (float)$_POST['Price'];

    // Kiểm tra xem sản phẩm đã tồn tại chưa
    $sqlCheck = "SELECT ProductId FROM Product WHERE Name = '$productName'";
    $resultCheck = mysqli_query($conn, $sqlCheck);

    if ($row = mysqli_fetch_assoc($resultCheck)) {
        // Sản phẩm đã tồn tại
        $productId = $row['ProductId'];
        $res0 = true; // Giả sử thành công vì không cần thêm mới
    } else {
        // Thêm sản phẩm mới
        $productId = 'PRD' . time(); // Hoặc có thể dùng UUID nếu sợ trùng
        $sqlProduct = "INSERT INTO Product (ProductId, Name) VALUES ('$productId', '$productName')";
        $res0 = mysqli_query($conn, $sqlProduct);
    }

    // Thêm phiếu nhập
    $sqlImport = "INSERT INTO Import (ImportId, ImportDate, Note) VALUES ('$importId', '$importDate', '$note')";
    $res1 = mysqli_query($conn, $sqlImport);

    // Thêm chi tiết phiếu nhập
    $sqlDetail = "INSERT INTO ImportProduct (ProductId, ImportId, Quantity, UnitPrice)
                  VALUES ('$productId', '$importId', $quantity, $unitPrice)";
    $res2 = mysqli_query($conn, $sqlDetail);

    if ($res0 && $res1 && $res2) {
        $message = "Nhập hàng và tạo sản phẩm mới thành công!";
    } else {
        $message = "Lỗi khi nhập hàng: " . mysqli_error($conn);
    }
}

// Lấy danh sách phiếu nhập và chi tiết
$importList = [];
$sql = "SELECT i.ImportId, i.ImportDate, i.Note, ip.ProductId, p.Name AS ProductName, ip.Quantity, ip.UnitPrice
        FROM Import i
        JOIN ImportProduct ip ON i.ImportId = ip.ImportId
        JOIN Product p ON ip.ProductId = p.ProductId
        ORDER BY i.ImportDate DESC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $importList[] = $row;
}
?>

<div class="content-wrapper">
  <div class="page-title d-flex justify-content-between align-items-start mb-4">
    <h2 style="color:rgb(10, 119, 52); margin-top: -10px;"><strong>Quản lý nhập hàng</strong></h2>
  </div>

 <!-- Thanh điều khiển trên bảng -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <!-- Tìm kiếm -->
  <form class="d-flex" role="search" method="GET" action="#">
    <input class="form-control me-2" type="search" placeholder="Tìm kiếm..." name="q" aria-label="Search">
    <button class="btn btn-outline-success" type="submit">
      <i class="bi bi-search"></i>
    </button>
  </form>

  <!-- Các nút bên phải -->
  <div class="d-flex gap-2">
    <form method="GET" class="d-flex align-items-center">
      <label for="limit" class="me-2">Hiển thị:</label>
      <select name="limit" id="limit" class="form-select w-auto me-3" onchange="this.form.submit()">
        <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
        <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
      </select>
      <input type="hidden" name="page" value="1">
    </form>

    <button class="btn btn-primary" type="button" onclick="exportData()">
      <i class="bi bi-download me-1"></i>
    </button>
    <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#addOrderModal">
      <i class="bi bi-plus-circle me-1"></i> Thêm
    </button>
  </div>
</div>

    <!-- Bảng danh sách nhập hàng -->
    <div class="table-responsive">
    <form method="POST" action="">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-success text-center">
            <tr>
                <th class="col-checkbox"><input type="checkbox" id="select-all"></th>
                <th>Mã phiếu</th>
                <th>Ngày nhập</th>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá nhập</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
            </tr>
        </thead>
<tbody>
<?php 
if (count($importList) > 0):
    foreach ($importList as $row): ?>
        <tr>
            <td><input type="checkbox" name="select[]" value="<?= $row['ImportId']; ?>"></td>
            <td><?= $row['ImportId'] ?></td>
            <td><?= $row['ImportDate'] ?></td>
            <td><?= $row['ProductName'] ?> (<?= $row['ProductId'] ?>)</td>
            <td><?= $row['Quantity'] ?></td>
            <td><?= number_format($row['UnitPrice'], 3) ?> VND</td>
            <td style="width:150px;"><?= nl2br(htmlspecialchars($row['Note'])) ?></td>
            <td class="text-center">
                <a href="#" class="btn btn-sm btn-info text-white" title="Xem"
                  onclick="showImportDetail('<?= $row['ImportId'] ?>')" data-bs-toggle="modal" data-bs-target="#importDetailModal">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="#" class="btn btn-sm btn-warning text-white"ntitle="Sửa" onclick="editImport('<?= $row['ImportId'] ?>')">
                  <i class="bi bi-pencil-square"></i>
                </a>
                  <a href="#" class="btn btn-sm btn-danger text-white" title="Xoá" onclick="confirmDelete('<?= $row['ImportId'] ?>')">
                    <i class="bi bi-trash"></i>
                  </a>
            </td>
        </tr>
<?php 
    endforeach;
else: ?>
    <tr>
        <td colspan="8" class="text-center text-muted">Không có dữ liệu nhập hàng</td>
    </tr>
<?php 
endif; ?>
</tbody>


    </table>
    </form>
  </div>
</div>

<?php
$importDetails = []; // Tạo mảng nhóm dữ liệu
foreach ($importList as $row) {
    $importDetails[$row['ImportId']][] = $row;
}
?>

<?php foreach ($importDetails as $importId => $details): ?>
  <div class="d-none" id="import-detail-<?= $importId ?>">
    <p><strong>Mã phiếu:</strong> <?= $importId ?></p>
    <p><strong>Ngày nhập:</strong> <?= date('d/m/Y', strtotime($details[0]['ImportDate'])) ?></p>
    <p><strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($details[0]['Note'])) ?></p>
    <table class="table table-bordered table-sm mt-2">
      <thead>
        <tr>
          <th>Sản phẩm</th>
          <th>Số lượng</th>
          <th>Đơn giá</th>
          <th>Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        <?php $total = 0; ?>
        <?php foreach ($details as $item): 
              $subtotal = $item['Quantity'] * $item['UnitPrice'];
              $total += $subtotal;
        ?>
          <tr>
            <td><?= $item['ProductName'] ?> (<?= $item['ProductId'] ?>)</td>
            <td><?= $item['Quantity'] ?></td>
            <td><?= number_format($item['UnitPrice'], 0, ',', '.') ?> VND</td>
            <td><?= number_format($subtotal, 0, ',', '.') ?> VND</td>
          </tr>
        <?php endforeach; ?>
        <tr class="table-secondary">
          <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
          <td><strong><?= number_format($total, 0, ',', '.') ?> VND</strong></td>
        </tr>
      </tbody>
    </table>
  </div>
<?php endforeach; ?>

    <!-- Modal thêm phiếu nhập -->
<div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-success" id="addOrderModalLabel">Thêm phiếu nhập hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body row g-3">
        <input type="hidden" name="add_import" value="1">

        <div class="col-md-12">
          <label class="form-label">Ghi chú</label>
          <textarea class="form-control" name="Note" placeholder="Ghi chú thêm nếu có..."></textarea>
        </div>

        <div class="col-md-6">
          <label class="form-label">Tên sản phẩm mới</label>
          <input type="text" class="form-control" name="ProductName" required placeholder="Nhập tên sản phẩm">
        </div>

        <div class="col-md-3">
          <label class="form-label">Số lượng</label>
          <input type="number" class="form-control" name="Quantity" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Giá nhập (VNĐ)</label>
          <input type="number" step="1000" class="form-control" name="Price" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Lưu phiếu</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal chi tiết phiếu nhập -->
<div class="modal fade" id="importDetailModal" tabindex="-1" aria-labelledby="importDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importDetailLabel" style="color:deepskyblue;">Chi tiết phiếu nhập</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body" id="importDetailContent">
        <!-- Nội dung chi tiết sẽ được load ở đây -->
      </div>
    </div>
  </div>
</div>
<!-- Modal Chỉnh sửa Phiếu Nhập -->
<div class="modal fade" id="editImportModal<?= $import['ImportId'] ?>" tabindex="-1" aria-labelledby="editImportLabel<?= $import['ImportId'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="update-import.php" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Chỉnh sửa Phiếu nhập #<?= $import['ImportId'] ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="update_import" value="1">
          <input type="hidden" name="ImportId" value="<?= $import['ImportId'] ?>">

          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $importId = $import['ImportId'];
              $sqlDetail = "SELECT ip.*, p.ProductName 
                            FROM ImportProduct ip 
                            JOIN Product p ON ip.ProductId = p.ProductId 
                            WHERE ip.ImportId = '$importId'";
              $resultDetail = mysqli_query($conn, $sqlDetail);
              while ($row = mysqli_fetch_assoc($resultDetail)) {
              ?>
                <tr>
                  <td>
                    <?= htmlspecialchars($row['ProductName']) ?>
                    <input type="hidden" name="ProductId[]" value="<?= $row['ProductId'] ?>">
                  </td>
                  <td>
                    <input type="number" name="Quantity[]" value="<?= $row['Quantity'] ?>" class="form-control" required>
                  </td>
                  <td>
                    <input type="number" name="UnitPrice[]" value="<?= $row['UnitPrice'] ?>" class="form-control" required step="0.01">
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>

          <div class="mb-3">
            <label for="Note" class="form-label">Ghi chú</label>
            <textarea name="Note" class="form-control" rows="3"><?= htmlspecialchars($import['Note']) ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Cập nhật</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php include "view/footer-admin.php"; ?>
