<?php 
    session_start();
    $namePage = "Quản lý khách hàng";
    include "view/header-admin.php";

    // Kết nối CSDL
    $conn = mysqli_connect("localhost", "root", "", "teav_shop1");
    if (!$conn) {
      die("Kết nối thất bại: " . mysqli_connect_error());
    }

    $sql = "
        SELECT 
        A.FullName,
        A.Email,
        A.PhoneNumber,
        A.CreatedDate,
        A.IsActive,
        COUNT(O.OrderId) AS TotalOrders,
        COALESCE(SUM(O.TotalAmount), 0) AS TotalSpent
    FROM Account A
    LEFT JOIN Orders O ON A.Email = O.Email
    WHERE A.Type = 'Customer'
    GROUP BY A.FullName, A.Email, A.PhoneNumber, A.CreatedDate, A.IsActive;
  ";

$result = mysqli_query($conn, $sql);
$customers = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
    }
}

// Xử lý chuyển đổi trạng thái IsActive
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $email = $_POST['email'] ?? '';
    $currentStatus = $_POST['current_status'] ?? '';

    if (!empty($email)) {
        $newStatus = ($currentStatus === 'Yes') ? 'No' : 'Yes';
        $updateSql = "UPDATE Account SET IsActive = ? WHERE Email = ?";
        $stmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($stmt, "ss", $newStatus, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Reload lại để tránh submit lại
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Xử lý xóa khách hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_customers'])) {
    if (isset($_POST['selected_customers']) && is_array($_POST['selected_customers'])) {
        foreach ($_POST['selected_customers'] as $email) {
            // Xóa các đơn hàng liên quan (nếu cần)
            // $deleteOrdersSql = "DELETE FROM Orders WHERE Email = ?";
            // $stmt = mysqli_prepare($conn, $deleteOrdersSql);
            // mysqli_stmt_bind_param($stmt, "s", $email);
            // mysqli_stmt_execute($stmt);
            // mysqli_stmt_close($stmt);
            
            // Sau đó xóa tài khoản
            $deleteAccountSql = "DELETE FROM Account WHERE Email = ? AND Type = 'Customer'";
            $stmt = mysqli_prepare($conn, $deleteAccountSql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // Chuyển hướng lại trang sau khi xóa
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Xử lý xuất CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=khach_hang.csv');

    $output = fopen('php://output', 'w');

    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($output, ['Họ tên', 'Email', 'Số điện thoại', 'Ngày đăng ký', 'Tổng đơn hàng', 'Tổng chi tiêu', 'Trạng thái']);

    foreach ($customers as $row) {
        fputcsv($output, [
            $row['FullName'],
            $row['Email'],
            $row['PhoneNumber'],
            date("d/m/Y", strtotime($row['CreatedDate'])),
            $row['TotalOrders'],
            number_format($row['TotalSpent'], 0, ',', '.'),
            $row['IsActive'] === 'Yes' ? 'Hoạt động' : 'Ngừng'
        ]);
    }

    fclose($output);
    exit;
}


?>


<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-center mb-4">
  <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong> Quản lý khách hàng</strong></h2>
  
  <div class="d-flex gap-2">
    <a href="add-customer.php" class="btn btn-success">
      <i class="bi bi-plus-circle me-1"></i>Thêm khách hàng
    </a>
    <button type="button" id="delete-selected" class="btn btn-danger" disabled data-bs-toggle="modal" data-bs-target="#deleteModal">
      <i class="bi bi-trash me-1"></i>Xóa khách hàng
    </button>
    <form action="?" method="GET" class="d-inline">
      <button class="btn btn-primary" type="submit" name="export" value="csv">
        <i class="bi bi-download me-1"></i>Xuất CSV
      </button>
    </form>
  </div>
</div>
<form class="d-flex align-items-center gap-2 mb-4" role="search" method="GET" action="#">
  <input class="form-control" type="search" placeholder="Tìm kiếm..." name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
  <button class="btn btn-outline-success" type="submit">
    <i class="bi bi-search"></i>
  </button>
</form>

<form id="customers-form" method="POST" action="">
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-success text-center">
        <tr>
          <th><input type="checkbox" id="select-all" class="form-check-input"></th>
          <th>Họ tên</th>
          <th>Email</th>
          <th>Số điện thoại</th>
          <th>Ngày đăng ký</th>
          <th>Tổng đơn hàng</th>
          <th>Tổng chi tiêu</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($customers)): ?>
          <?php foreach ($customers as $index => $customer): ?>
            <tr class="text-center">
              <td><input type="checkbox" name="selected_customers[]" class="form-check-input customer-checkbox" value="<?php echo $customer['Email']; ?>"></td>
              <td class="text-start"><?= htmlspecialchars($customer['FullName']); ?></td>
              <td><?= htmlspecialchars($customer['Email']); ?></td>
              <td><?= htmlspecialchars($customer['PhoneNumber']); ?></td>
              <td><?= date("d/m/Y", strtotime($customer['CreatedDate'])); ?></td>
              <td><?= $customer['TotalOrders']; ?></td>
              <td><?= number_format($customer['TotalSpent'], 0, ',', '.'); ?> đ</td>
              <td>
                <form method="POST" action="" style="display:inline;">
                  <input type="hidden" name="email" value="<?= htmlspecialchars($customer['Email']) ?>">
                  <input type="hidden" name="current_status" value="<?= $customer['IsActive'] ?>">
                  <button type="submit" name="toggle_status" class="btn btn-sm <?= $customer['IsActive'] === 'Yes' ? 'btn-success' : 'btn-secondary' ?>">
                      <?= $customer['IsActive'] === 'Yes' ? 'Hoạt động' : 'Ngừng' ?>
                  </button>
                </form>
              </td>
              <td>
                <a href="view-customer.php?email=<?= urlencode($customer['Email']); ?>" class="btn btn-sm btn-info">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="edit-customer.php?email=<?= urlencode($customer['Email']); ?>" class="btn btn-sm btn-primary">
                  <i class="bi bi-pencil"></i>
                </a>
                <button type="button" class="btn btn-sm btn-danger delete-single" data-bs-toggle="modal" data-bs-target="#deleteModal" data-email="<?= $customer['Email'] ?>">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="10" class="text-center text-muted">Không có dữ liệu khách hàng</td>
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
          Bạn có chắc chắn muốn xóa khách hàng đã chọn không? Hành động này không thể hoàn tác.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" name="delete_customers" class="btn btn-danger">Xóa</button>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý chọn tất cả
    const selectAllCheckbox = document.getElementById('select-all');
    const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
    const deleteSelectedBtn = document.getElementById('delete-selected');
    
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        
        customerCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        
        updateDeleteButtonState();
    });
    
    // Cập nhật trạng thái của nút xóa dựa trên số lượng checkbox được chọn
    function updateDeleteButtonState() {
        const checkedCount = document.querySelectorAll('.customer-checkbox:checked').length;
        deleteSelectedBtn.disabled = checkedCount === 0;
    }
    
    // Thêm sự kiện cho từng checkbox
    customerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButtonState();
            
            // Kiểm tra xem tất cả có được chọn không
            const allChecked = document.querySelectorAll('.customer-checkbox:checked').length === customerCheckboxes.length;
            selectAllCheckbox.checked = allChecked;
        });
    });
    
    // Xử lý nút xóa đơn lẻ
    const deleteSingleButtons = document.querySelectorAll('.delete-single');
    deleteSingleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Bỏ chọn tất cả các checkbox khác
            customerCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Chỉ chọn checkbox tương ứng với khách hàng muốn xóa
            const email = this.getAttribute('data-email');
            const correspondingCheckbox = document.querySelector(`.customer-checkbox[value="${email}"]`);
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