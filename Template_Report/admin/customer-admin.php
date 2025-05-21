<?php
session_start();
$namePage = "Quản lý khách hàng";
include "view/header-admin.php";

$conn = new mysqli("localhost", "root", "", "teav_shop1");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

// Xử lý toggle trạng thái khách hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $current_status = $_POST['current_status'] === 'Yes' ? 'No' : 'Yes';

    $sqlUpdate = "UPDATE Account SET IsActive='$current_status' WHERE Email='$email'";
    $conn->query($sqlUpdate);
    header("Location: customer-admin.php");
    exit;
}

// Xử lý tìm kiếm
$searchQuery = trim($_GET['q'] ?? '');
$searchSQL = "";
if ($searchQuery !== '') {
    $q = $conn->real_escape_string($searchQuery);
    $searchSQL = "WHERE FullName LIKE '%$q%' OR Email LIKE '%$q%' OR PhoneNumber LIKE '%$q%'";
}

// Lấy danh sách khách hàng, bổ sung tổng đơn hàng và tổng chi tiêu (giả sử bảng Orders có Email và TotalAmount)
$sql = "
SELECT 
    a.Email, a.FullName, a.PhoneNumber, a.IsActive, a.CreatedDate,
    COUNT(o.OrderId) AS TotalOrders,
    COALESCE(SUM(o.TotalAmount), 0) AS TotalSpent
FROM Account a
LEFT JOIN Orders o ON a.Email = o.Email
$searchSQL
GROUP BY a.Email, a.FullName, a.PhoneNumber, a.IsActive, a.CreatedDate
ORDER BY a.CreatedDate DESC
";

$result = $conn->query($sql);
$customers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}
?>

<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-center mb-4">
    <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong>Quản lý khách hàng</strong></h2>
  </div>

  <?php if (isset($_GET['deleted']) && $_GET['deleted'] === 'success'): ?>
    <div class="alert alert-success">Xóa khách hàng thành công!</div>
  <?php elseif (isset($_GET['deleted']) && $_GET['deleted'] === 'error'): ?>
    <div class="alert alert-danger">Lỗi xảy ra khi xóa khách hàng.</div>
  <?php endif; ?>

<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">

  <!-- Form tìm kiếm (GET) -->
  <form role="search" method="GET" action="" class="d-flex flex-grow-1 me-2">
    <input class="form-control" type="search" placeholder="Tìm kiếm..." name="q" value="<?= htmlspecialchars($searchQuery ?? '') ?>">
    <button class="btn btn-outline-success ms-2" type="submit">
      <i class="bi bi-search"></i>
    </button>
  </form>

  <!-- Nút Thêm -->
  <a href="add-customer.php" class="btn btn-success">
    <i class="bi bi-plus-circle"></i> Thêm
  </a>

  <!-- Form Xóa khách hàng + Modal -->
  <form id="delete-form" action="delete-customer.php" method="POST" class="m-0 p-0 d-inline">
    <!-- Nút Xóa (kích hoạt modal) -->
    <button type="button" id="delete-selected" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" disabled>
      <i class="bi bi-trash me-1"></i> Xóa
    </button>

    <!-- Modal xác nhận xóa -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Bạn có chắc chắn muốn xóa các khách hàng đã chọn không? Hành động này không thể hoàn tác.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" name="delete_customers" class="btn btn-danger">Xóa</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- Nút xuất CSV -->
  <a href="?export=csv" class="btn btn-primary">
    <i class="bi bi-download me-1"></i> Xuất CSV
  </a>
  
</div>



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
            <?php foreach ($customers as $customer): ?>
              <tr class="text-center">
                <td>
                  <input type="checkbox" name="selected_customers[]" class="form-check-input customer-checkbox" value="<?= htmlspecialchars($customer['Email']); ?>">
                </td>
                <td class="text-start"><?= htmlspecialchars($customer['FullName']); ?></td>
                <td><?= htmlspecialchars($customer['Email']); ?></td>
                <td><?= htmlspecialchars($customer['PhoneNumber']); ?></td>
                <td><?= date("d/m/Y", strtotime($customer['CreatedDate'])); ?></td>
                <td><?= $customer['TotalOrders']; ?></td>
                <td><?= number_format($customer['TotalSpent'], 0, ',', '.'); ?> VND</td>
                <td>
                  <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($customer['Email']) ?>">
                    <input type="hidden" name="current_status" value="<?= $customer['IsActive'] ?>">
                    <button type="submit" name="toggle_status" class="btn btn-sm <?= $customer['IsActive'] === 'Yes' ? 'btn-success' : 'btn-secondary' ?>" title="<?= $customer['IsActive'] === 'Yes' ? 'Đang hoạt động' : 'Ngừng hoạt động' ?>">
                      <?= $customer['IsActive'] === 'Yes' ? 'Hoạt động' : 'Ngừng' ?>
                    </button>
                  </form>
                </td>
                <td>
                  <a href="view-customer.php?email=<?= urlencode($customer['Email']); ?>" 
                    class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                    <i class="fa fa-eye"></i>
                  </a>
                  <a href="edit-customer.php?email=<?= urlencode($customer['Email']); ?>" 
                    class="btn btn-sm btn-warning text-white edit-customer" title="Sửa khách hàng">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center text-muted">Không có dữ liệu khách hàng</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </form>
</div>

<script>
  // Bật/tắt nút Xóa khi checkbox được chọn
  const selectAllCheckbox = document.getElementById('select-all');
  const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
  const deleteBtn = document.getElementById('delete-selected');

  function updateDeleteButton() {
    const anyChecked = Array.from(customerCheckboxes).some(cb => cb.checked);
    deleteBtn.disabled = !anyChecked;
  }

  selectAllCheckbox.addEventListener('change', () => {
    customerCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    updateDeleteButton();
  });

  customerCheckboxes.forEach(cb => {
    cb.addEventListener('change', () => {
      if (!cb.checked) selectAllCheckbox.checked = false;
      else if (Array.from(customerCheckboxes).every(c => c.checked)) selectAllCheckbox.checked = true;
      updateDeleteButton();
    });
  });

  updateDeleteButton();
</script>


<?php 
    include "view/footer-admin.php";
?>
