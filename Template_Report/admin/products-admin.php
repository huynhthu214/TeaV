<?php 
    session_start();
    $namePage = "Quản lý sản phẩm";
    include "view/header-admin.php";

    // Kết nối CSDL
    $conn = mysqli_connect("localhost", "root", "", "teav_shop1");
    if (!$conn) {
      die("Kết nối thất bại: " . mysqli_connect_error());
    }

    // Xử lý tìm kiếm
    $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
    $categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';
    
    $conditions = [];
    $params = [];

    if (!empty($keyword)) {
        $keyword_like = '%' . $keyword . '%';
        $conditions[] = "(p.Name LIKE ? OR p.ProductId LIKE ?)";
        $params[] = $keyword_like;
        $params[] = $keyword_like;
    }

    if (!empty($categoryFilter)) {
        $conditions[] = "p.CategoryId = ?";
        $params[] = $categoryFilter;
    }

    $where = '';
    if (!empty($conditions)) {
        $where = "WHERE " . implode(" AND ", $conditions);
    }

    // Lấy danh sách sản phẩm - sửa lại truy vấn cho phù hợp với cấu trúc DB
    $sql = "
        SELECT 
            p.ProductId,
            p.Name,
            c.Name AS CategoryName,
            s.SupplierName AS SupplierName,
            p.Quantity,
            p.Price,
            p.SaleOff,
            p.IsShow,
            u.Name AS Unit
        FROM Product p
        LEFT JOIN Categories c ON p.CategoryId = c.CategoryId
        LEFT JOIN Suppliers s ON p.SupplierId = s.SupplierId
        LEFT JOIN CalculationUnit u ON p.UnitId = u.UnitId
        $where
        ORDER BY p.UpdatedAt DESC
    ";

    // Prepare và bind với mysqli
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        die("Chuẩn bị câu lệnh thất bại: " . mysqli_error($conn));
    }

    if (!empty($params)) {
        // Tạo kiểu dữ liệu cho bind_param
        $types = str_repeat('s', count($params));
        $bind_names[] = $types;
        for ($i=0; $i < count($params); $i++) {
            $bind_name = 'bind' . $i;
            $$bind_name = $params[$i];
            $bind_names[] = &$$bind_name;
        }
        call_user_func_array(array($stmt, 'bind_param'), $bind_names);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    // Lấy danh sách danh mục cho bộ lọc - sửa tên bảng từ Category thành Categories
    $categorySql = "SELECT CategoryId, Name FROM Categories ORDER BY Name";
    $categoryResult = mysqli_query($conn, $categorySql);
    $categories = mysqli_fetch_all($categoryResult, MYSQLI_ASSOC);

    // Xử lý chuyển đổi trạng thái hiển thị
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
        $productId = $_POST['productid'] ?? '';
        $currentStatus = $_POST['current_status'] ?? '';

        if ($productId !== '') {
            $newStatus = ($currentStatus === 'Yes') ? 'No' : 'Yes';
            $updateSql = "UPDATE Product SET IsShow = ? WHERE ProductId = ?";
            $stmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($stmt, "ss", $newStatus, $productId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            // Chuyển hướng lại trang để tránh submit form lại khi refresh
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    // Xử lý xuất CSV
    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=san_pham.csv');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8

        fputcsv($output, ['Mã SP', 'Tên sản phẩm', 'Danh mục', 'Nhà cung cấp', 'Số lượng', 'Đơn vị', 'Giá bán', 'Giảm giá', 'Trạng thái']);

        foreach ($products as $product) {
            $status = $product['IsShow'] === 'Yes' ? 'Hiển thị' : 'Ẩn';
            $discount = !empty($product['SaleOff']) ? $product['SaleOff'] . '%' : '0%';
            
            fputcsv($output, [
                $product['ProductId'],
                $product['Name'],
                $product['CategoryName'],
                $product['SupplierName'],
                $product['Quantity'],
                $product['Unit'],
                number_format($product['Price'], 3),
                $discount,
                $status
            ]);
        }

        fclose($output);
        exit;
    }
?>

<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-center mb-4">
  <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong> Quản lý sản phẩm</strong></h2>
</div>

<form class="d-flex align-items-center gap-2 mb-4" method="GET" action="">
  <div class="col-md">
    <input class="form-control" type="search" placeholder="Tìm theo tên hoặc mã sản phẩm..." name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
  </div>
  
  <div class="col-md-auto">
    <select class="form-select" name="category">
      <option value=""> Tất cả danh mục </option>
      <?php foreach ($categories as $category): ?>
        <option value="<?= htmlspecialchars($category['CategoryId']) ?>" <?= (($_GET['category'] ?? '') === $category['CategoryId']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($category['Name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  
  <div class="col-md-auto">
    <button class="btn btn-outline-success" type="submit">
      <i class="bi bi-search"></i>
    </button>
  </div>
  
  <div class="col-md-auto">
    <button class="btn btn-success" type="button" id="add-product-btn">
      <i class="bi bi-plus-circle me-1"></i>Thêm
    </button>
  </div>

  <div class="col-md-auto">
    <button type="button" id="delete-selected" class="btn btn-danger" disabled>
      <i class="bi bi-trash me-1"></i>Xóa
    </button>
  </div>
    
  <div class="col-md-auto">
    <a href="?export=csv" class="btn btn-primary">
      <i class="bi bi-download me-1"></i>
    </a>
  </div>

</form>

<form id="products-form" method="POST" action="">
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-success text-center">
        <tr>
          <th><input type="checkbox" id="select-all" class="form-check-input"></th>
          <th>Mã SP</th>
          <th>Tên sản phẩm</th>
          <th>Danh mục</th>
          <th>Số lượng</th>
          <th>Đơn vị</th>
          <th>Giá bán</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $index => $product): ?>
            <tr class="text-center">
              <td><input type="checkbox" name="selected_products[]" class="form-check-input product-checkbox" value="<?php echo $product['ProductId']; ?>"></td>
              <td><?= htmlspecialchars($product['ProductId']); ?></td>
              <td class="text-start"><?= htmlspecialchars($product['Name']); ?></td>
              <td><?= htmlspecialchars($product['CategoryName']); ?></td>
              <td><?= $product['Quantity']; ?></td>
              <td><?= htmlspecialchars($product['Unit']); ?></td>
              <td><?= number_format($product['Price'], 3); ?> VND</td>
              <td>
                <form method="POST" action="" style="display:inline;">
                  <input type="hidden" name="productid" value="<?= htmlspecialchars($product['ProductId']) ?>">
                  <input type="hidden" name="current_status" value="<?= $product['IsShow'] ?>">
                  <button type="submit" name="toggle_status" class="btn btn-sm <?= $product['IsShow'] === 'Yes' ? 'btn-success text-white' : 'btn-secondary' ?>" title="<?= $product['IsShow'] === 'Yes' ? 'Đang hiển thị' : 'Đang ẩn' ?>">
                    <?= $product['IsShow'] === 'Yes' ? "Hiển thị" : "Ẩn" ?>
                  </button>
                </form>
              </td>
              <td>
                <a href="product-detail.php?id=<?= urlencode($product['ProductId']); ?>" class="btn btn-sm btn-info text-white" title="Xem">
                  <i class="fa fa-eye"></i>
                </a>
                <button type="button" class="btn btn-sm btn-warning text-white edit-product" 
                        data-email="<?= htmlspecialchars($product['ProductId']); ?>">
                  <i class="bi bi-pencil-square"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="10" class="text-center text-muted">Không có dữ liệu sản phẩm</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<?php 
    include "view/footer-admin.php";
?>