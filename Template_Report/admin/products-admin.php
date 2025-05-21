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
              <td><?= number_format($product['Price'], 3); ?> VND</td>
              <td><?= !empty($product['SaleOff']) ? $product['SaleOff'] . '%' : '0%'; ?></td>
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
                <a href="view-product.php?id=<?= urlencode($product['ProductId']); ?>" class="btn btn-sm btn-info text-white" title="Xem">
                  <i class="fa fa-eye"></i>
                </a>
                <button type="button" class="btn btn-sm btn-warning text-white edit-product" 
                        data-email="<?= htmlspecialchars($product['ProductId']); ?>">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger text-white delete-single" 
                          data-email="<?= htmlspecialchars($product['ProductId']); ?>">
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

 <!-- Modal Thêm Sản phẩm -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="addProductModalLabel">Thêm sản phẩm mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="" id="add-product-form">
        <div class="modal-body">
          <div class="mb-3">
            <label for="add-name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="add-name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="add-category" class="form-label">Danh mục <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="add-category" name="category" required>
          </div>
          <div class="mb-3">
            <label for="add-quantity" class="form-label">Số lượng </label>
            <input type="number" class="form-control" id="add-quantity" name="quantity" min="0">
          </div>
          <div class="mb-3">
            <label for="add-unit" class="form-label">Đơn vị <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="add-unit" name="unit" required>
          </div>
          <div class="mb-3">
            <label for="add-price" class="form-label">Giá bán <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="add-price" name="price" step="0.001" required>
          </div>
          <div class="mb-3">
            <label for="add-sale" class="form-label">Giảm giá (%)</label>
            <input type="number" class="form-control" id="add-sale" name="sale" min="0" max="100">
          </div>
          <div class="mb-3">
            <label for="add-isActive" class="form-label">Trạng thái</label>
            <select class="form-select" id="add-isActive" name="isActive">
              <option value="Yes" selected>Hiển thị</option>
              <option value="No">Ẩn</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" name="add_product" class="btn btn-success">Thêm</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal Chỉnh Sửa Sản Phẩm -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="editProductModalLabel">Chỉnh sửa sản phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="" id="edit-product-form">
        <div class="modal-body">
          <input type="hidden" id="edit-id" name="product_id">
          
          <div class="mb-3">
            <label for="edit-name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit-name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="edit-category" class="form-label">Danh mục</label>
            <input type="text" class="form-control" id="edit-category" name="category">
          </div>
          <div class="mb-3">
            <label for="edit-quantity" class="form-label">Số lượng</label>
            <input type="number" class="form-control" id="edit-quantity" name="quantity" min="0">
          </div>
          <div class="mb-3">
            <label for="edit-unit" class="form-label">Đơn vị</label>
            <input type="text" class="form-control" id="edit-unit" name="unit">
          </div>
          <div class="mb-3">
            <label for="edit-price" class="form-label">Giá bán</label>
            <input type="number" class="form-control" id="edit-price" name="price" step="0.001">
          </div>
          <div class="mb-3">
            <label for="edit-sale" class="form-label">Giảm giá (%)</label>
            <input type="number" class="form-control" id="edit-sale" name="sale" min="0" max="100">
          </div>
          <div class="mb-3">
            <label for="edit-isActive" class="form-label">Trạng thái</label>
            <select class="form-select" id="edit-isActive" name="isActive">
              <option value="Yes">Hiển thị</option>
              <option value="No">Ẩn</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" name="update_product" class="btn btn-warning">Cập nhật</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal Xác Nhận Xóa -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteConfirmModalLabel">Xác nhận xóa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Bạn có chắc chắn muốn xóa (các) <strong>sản phẩm</strong> đã chọn không?</p>
        <p class="text-danger"><small>Lưu ý: Hành động này không thể hoàn tác.</small></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="button" id="confirm-delete" class="btn btn-danger">Xóa</button>
      </div>
    </div>
  </div>
</div>

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

    // ----- MODAL THÊM MỚI SẢN PHẨM -----

    const addButton = document.getElementById('add-product-btn');
    if (addButton) {
        addButton.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
            modal.show();
        });
    }
    
    // ----- MODAL CHỈNH SỬA SẢN PHẨM -----
    const editButtons = document.querySelectorAll('.edit-product');

    editButtons.forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.getAttribute('data-id');
        if (!productId) return;

        const row = this.closest('tr');
        if (!row) return;

        const name = row.cells[2]?.textContent.trim() || '';
        const category = row.cells[3]?.textContent.trim() || '';
        const quantity = row.cells[4]?.textContent.trim() || '';
        const unit = row.cells[5]?.textContent.trim() || '';
        const price = row.cells[6]?.textContent.trim().replace(/\D/g, '') || '';
        const sale = row.cells[7]?.textContent.trim().replace('%', '') || '';
        const isShow = row.querySelector('.btn-sm')?.textContent.trim() === 'Hiển thị' ? 'Yes' : 'No';

        // Gán dữ liệu vào modal
        document.getElementById('edit-product-id').value = productId;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-category').value = category;
        document.getElementById('edit-quantity').value = quantity;
        document.getElementById('edit-unit').value = unit;
        document.getElementById('edit-price').value = price;
        document.getElementById('edit-sale').value = sale;
        document.getElementById('edit-isActive').value = isShow;

        // Hiển thị modal
        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
        modal.show();
    });
  });

});
</script>

<?php 
    include "view/footer-admin.php";
?>