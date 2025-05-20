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

    // Xử lý xóa sản phẩm - thêm kiểm tra và xóa các liên kết còn lại
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_products'])) {
        if (isset($_POST['selected_products']) && is_array($_POST['selected_products'])) {
            foreach ($_POST['selected_products'] as $productId) {
                // Bắt đầu giao dịch
                mysqli_begin_transaction($conn);
                
                try {
                    // Xóa các thành phần liên quan trước
                    // 1. Xóa liên kết với nguyên liệu
                    $deleteIngredientsSql = "DELETE FROM ProductIngredient WHERE ProductId = ?";
                    $stmt = mysqli_prepare($conn, $deleteIngredientsSql);
                    mysqli_stmt_bind_param($stmt, "s", $productId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                    // 2. Xóa liên kết với đánh giá sản phẩm
                    $deleteReviewsSql = "DELETE FROM ReviewProduct WHERE ProductId = ?";
                    $stmt = mysqli_prepare($conn, $deleteReviewsSql);
                    mysqli_stmt_bind_param($stmt, "s", $productId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                    // 3. Xóa liên kết với nhập hàng
                    $deleteImportSql = "DELETE FROM ImportProduct WHERE ProductId = ?";
                    $stmt = mysqli_prepare($conn, $deleteImportSql);
                    mysqli_stmt_bind_param($stmt, "s", $productId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                    // 4. Xóa liên kết với đơn hàng
                    $deleteOrderProductSql = "DELETE FROM OrderProduct WHERE ProductId = ?";
                    $stmt = mysqli_prepare($conn, $deleteOrderProductSql);
                    mysqli_stmt_bind_param($stmt, "s", $productId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                    // Sau đó xóa sản phẩm
                    $deleteProductSql = "DELETE FROM Product WHERE ProductId = ?";
                    $stmt = mysqli_prepare($conn, $deleteProductSql);
                    mysqli_stmt_bind_param($stmt, "s", $productId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    // Xác nhận giao dịch
                    mysqli_commit($conn);
                } catch (Exception $e) {
                    // Nếu có lỗi, hủy bỏ giao dịch
                    mysqli_rollback($conn);
                    echo "Lỗi xóa sản phẩm: " . $e->getMessage();
                }
            }
            
            // Chuyển hướng lại trang sau khi xóa
            header("Location: " . $_SERVER['PHP_SELF']);
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
                number_format($product['Price'], 0, ',', '.'),
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
  
  <div class="d-flex gap-2">
    <a href="add-product.php" class="btn btn-success">
      <i class="bi bi-plus-circle me-1"></i>Thêm sản phẩm
    </a>
    <button type="button" id="delete-selected" class="btn btn-danger" disabled data-bs-toggle="modal" data-bs-target="#deleteModal">
      <i class="bi bi-trash me-1"></i>Xóa sản phẩm
    </button>
    <form action="?" method="GET" class="d-inline">
      <button class="btn btn-primary" type="submit" name="export" value="csv">
        <i class="bi bi-download me-1"></i>Xuất CSV
      </button>
    </form>
  </div>
</div>

<form class="d-flex align-items-center gap-2 mb-4" method="GET" action="">
  <input class="form-control" type="search" placeholder="Tìm theo tên hoặc mã sản phẩm..." name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
  
  <select class="form-select" name="category">
    <option value=""> Tất cả danh mục </option>
    <?php foreach ($categories as $category): ?>
      <option value="<?= htmlspecialchars($category['CategoryId']) ?>" <?= (($_GET['category'] ?? '') === $category['CategoryId']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($category['Name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <button class="btn btn-outline-success" type="submit">
    <i class="bi bi-search"></i>
  </button>
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
          <th>Giảm giá</th>
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
              <td><?= number_format($product['Price'], 0, ',', '.'); ?> đ</td>
              <td><?= !empty($product['SaleOff']) ? $product['SaleOff'] . '%' : '0%'; ?></td>
              <td>
                <form method="POST" action="" style="display:inline;">
                  <input type="hidden" name="productid" value="<?= htmlspecialchars($product['ProductId']) ?>">
                  <input type="hidden" name="current_status" value="<?= $product['IsShow'] ?>">
                  <button type="submit" name="toggle_status" class="btn btn-sm <?= $product['IsShow'] === 'Yes' ? 'btn-outline-primary' : 'btn-outline-secondary' ?>" title="<?= $product['IsShow'] === 'Yes' ? 'Đang hiển thị' : 'Đang ẩn' ?>">
                      <i class="fa fa-<?= $product['IsShow'] === 'Yes' ? 'eye' : 'eye-slash' ?>" aria-hidden="true"></i>
                  </button>
                </form>
              </td>
              <td>
                <a href="view-product.php?id=<?= urlencode($product['ProductId']); ?>" class="btn btn-sm btn-info">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="edit-product.php?id=<?= urlencode($product['ProductId']); ?>" class="btn btn-sm btn-primary">
                  <i class="bi bi-pencil"></i>
                </a>
                <button type="button" class="btn btn-sm btn-danger delete-single" data-bs-toggle="modal" data-bs-target="#deleteModal" data-product-id="<?= $product['ProductId'] ?>">
                  <i class="bi bi-trash"></i>
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

  <!-- Modal xác nhận xóa -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Bạn có chắc chắn muốn xóa sản phẩm đã chọn không? Hành động này không thể hoàn tác.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" name="delete_products" class="btn btn-danger">Xóa</button>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý chọn tất cả
    const selectAllCheckbox = document.getElementById('select-all');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const deleteSelectedBtn = document.getElementById('delete-selected');
    
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        
        updateDeleteButtonState();
    });
    
    // Cập nhật trạng thái của nút xóa dựa trên số lượng checkbox được chọn
    function updateDeleteButtonState() {
        const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
        deleteSelectedBtn.disabled = checkedCount === 0;
    }
    
    // Thêm sự kiện cho từng checkbox
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButtonState();
            
            // Kiểm tra xem tất cả có được chọn không
            const allChecked = document.querySelectorAll('.product-checkbox:checked').length === productCheckboxes.length;
            selectAllCheckbox.checked = allChecked;
        });
    });
    
    // Xử lý nút xóa đơn lẻ
    const deleteSingleButtons = document.querySelectorAll('.delete-single');
    deleteSingleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Bỏ chọn tất cả các checkbox khác
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Chỉ chọn checkbox tương ứng với sản phẩm muốn xóa
            const productId = this.getAttribute('data-product-id');
            const correspondingCheckbox = document.querySelector(`.product-checkbox[value="${productId}"]`);
            if (correspondingCheckbox) {
                correspondingCheckbox.checked = true;
            }
        });
    });
    
    // Khởi tạo trạng thái nút xóa
    updateDeleteButtonState();
});
</script>

<?php 
    include "view/footer-admin.php";
?>