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
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchSQL = "";
if ($searchQuery !== '') {
    $q = $conn->real_escape_string($searchQuery);
    $searchSQL = "WHERE a.FullName LIKE '%$q%' OR a.Email LIKE '%$q%' OR a.PhoneNumber LIKE '%$q%'";
}
// Lấy danh sách khách hàng, bổ sung tổng đơn hàng và tổng chi tiêu
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

<style>
  .toast-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 250px;
    z-index: 1055; /* cao hơn modal và các phần khác */
  }
</style>

<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-center mb-4">
    <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong>Quản lý khách hàng</strong></h2>
  </div>

  <?php if (isset($_GET['deleted'])): ?>
  <?php if ($_GET['deleted'] === 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show toast-alert" role="alert" id="toast-alert">
      Đã xóa <strong>thành công</strong> khách hàng đã chọn.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php elseif ($_GET['deleted'] === 'error'): ?>
    <div class="alert alert-danger alert-dismissible fade show toast-alert" role="alert" id="toast-alert">
      Có lỗi xảy ra khi xóa khách hàng. <?= isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Vui lòng thử lại.' ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
<?php endif; ?>

<!-- Thông báo kết quả tìm kiếm -->
<?php if ($searchQuery !== ''): ?>
  <div class="alert alert-info alert-dismissible fade show" role="alert">
    Kết quả tìm kiếm cho: <strong>"<?= htmlspecialchars($searchQuery) ?>"</strong>
    <a href="customer-admin.php" class="btn btn-sm btn-outline-secondary ms-2">Xóa bộ lọc</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">

  <!-- Form tìm kiếm (GET) -->
  <form role="search" method="GET" action="" class="d-flex flex-grow-1 me-2">
    <input class="form-control" type="search" placeholder="Tìm kiếm theo tên, email, số điện thoại..." name="q" value="<?= htmlspecialchars($searchQuery) ?>">
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
  <a href="export-customer.php<?= $searchQuery ? '?q='.urlencode($searchQuery) : '' ?>" class="btn btn-primary">
  <i class="bi bi-download me-1"></i>
</a>
  
</div>

<form id="customers-form" method="POST" action="">
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-success text-center">
        <tr>
          <th><input type="checkbox" id="select-all"></th>
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
                <input type="checkbox" name="selected_emails[]" value="<?= htmlspecialchars($customer['Email']) ?>" class="customer-checkbox">
              </td>
              <td class="text-start"><?= htmlspecialchars($customer['FullName']); ?></td>
              <td><?= htmlspecialchars($customer['Email']); ?></td>
              <td><?= htmlspecialchars($customer['PhoneNumber']); ?></td>
              <td><?= date("d-m-Y", strtotime($customer['CreatedDate'])); ?></td>
              <td><?= $customer['TotalOrders']; ?></td>
              <td><?= number_format($customer['TotalSpent'], 3); ?> VND</td>
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
            <td colspan="9" class="text-center text-muted py-4">
              <?php if ($searchQuery !== ''): ?>
                Không tìm thấy khách hàng nào phù hợp với từ khóa "<?= htmlspecialchars($searchQuery) ?>"
              <?php else: ?>
                Không có dữ liệu khách hàng
              <?php endif; ?>
            </td>
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

  // Xử lý chọn khách hàng và hiển thị modal xóa
  document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.customer-checkbox');
    const deleteBtn = document.getElementById('delete-selected');
    const deleteForm = document.getElementById('delete-form');

    // Cập nhật trạng thái nút xóa
    function updateDeleteButton() {
      const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
      deleteBtn.disabled = !anyChecked;
    }

    // Xử lý chọn tất cả
    if (selectAllCheckbox) {
      selectAllCheckbox.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateDeleteButton();
      });
    }

    // Cập nhật trạng thái nút khi checkbox thay đổi
    checkboxes.forEach(cb => {
      cb.addEventListener('change', updateDeleteButton);
    });

    // Trước khi mở modal xóa, thêm các checkbox được chọn vào form
    if (deleteBtn) {
      deleteBtn.addEventListener('click', () => {
        // Xóa input ẩn cũ
        document.querySelectorAll('#delete-form input[name="selected_emails[]"]').forEach(e => e.remove());

        // Tạo input ẩn từ checkbox đã chọn
        checkboxes.forEach(cb => {
          if (cb.checked) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'selected_emails[]';
            hiddenInput.value = cb.value;
            deleteForm.appendChild(hiddenInput);
          }
        });
      });
    }
  });

  // Tự động ẩn thông báo toast sau 3 giây
  window.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('toast-alert');
    if (toast) {
      setTimeout(() => {
        // Dùng Bootstrap để ẩn alert (kích hoạt hiệu ứng fade)
        const alert = bootstrap.Alert.getOrCreateInstance(toast);
        alert.close();
      }, 3000); // 3000ms = 3 giây
    }
  });
</script>

<?php 
    include "view/footer-admin.php";
?>