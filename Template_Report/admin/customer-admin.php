<?php 
    session_start();
    $namePage = "Quản lý khách hàng";
    include "view/header-admin.php";

    // Kết nối CSDL
    $conn = mysqli_connect("localhost", "root", "", "teav_shop1");
    if (!$conn) {
      die("Kết nối thất bại: " . mysqli_connect_error());
    }

    // Xử lý thêm khách hàng mới
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_customer'])) {
      $fullName = $_POST['fullName'] ?? '';
      $email = $_POST['email'] ?? '';
      $phoneNumber = $_POST['phoneNumber'] ?? '';
      $password = $_POST['password'] ?? '';
      $confirmPassword = $_POST['confirmPassword'] ?? '';
      $isActive = $_POST['isActive'] ?? 'Yes';
      $createdDate = date('Y-m-d H:i:s');
      
      // Kiểm tra mật khẩu xác nhận
      if ($password !== $confirmPassword) {
          $_SESSION['error_message'] = "Mật khẩu xác nhận không khớp!";
      } else {
          // Mã hóa mật khẩu
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          
          // Kiểm tra email đã tồn tại chưa
          $checkEmailSql = "SELECT Email FROM Account WHERE Email = ?";
          $stmt = mysqli_prepare($conn, $checkEmailSql);
          mysqli_stmt_bind_param($stmt, "s", $email);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_store_result($stmt);
          
          if (mysqli_stmt_num_rows($stmt) > 0) {
              $_SESSION['error_message'] = "Email đã tồn tại trong hệ thống!";
          } else {
              // Thêm khách hàng mới
              $insertSql = "INSERT INTO Account (FullName, Email, PhoneNumber, Password, Type, IsActive, CreatedDate) VALUES (?, ?, ?, ?, 'Customer', ?, ?)";
              $stmt = mysqli_prepare($conn, $insertSql);
              mysqli_stmt_bind_param($stmt, "ssssss", $fullName, $email, $phoneNumber, $hashedPassword, $isActive, $createdDate);
              
              if (mysqli_stmt_execute($stmt)) {
                  $_SESSION['success_message'] = "Thêm khách hàng thành công!";
              } else {
                  $_SESSION['error_message'] = "Lỗi: " . mysqli_error($conn);
              }
          }
          mysqli_stmt_close($stmt);
      }
      // Chuyển hướng để tránh gửi lại form
      header("Location: " . $_SERVER['PHP_SELF']);
      exit;
    }

    // Xử lý sửa thông tin khách hàng
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_customer'])) {
      $email = $_POST['edit_email'] ?? '';
      $fullName = $_POST['edit_fullName'] ?? '';
      $phoneNumber = $_POST['edit_phoneNumber'] ?? '';
      $password = $_POST['edit_password'] ?? '';
      $isActive = $_POST['edit_isActive'] ?? 'Yes';
      
      // Chuẩn bị câu truy vấn cập nhật
      if (!empty($password)) {
          // Nếu có cập nhật mật khẩu
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          $updateSql = "UPDATE Account SET FullName = ?, PhoneNumber = ?, Password = ?, IsActive = ? WHERE Email = ? AND Type = 'Customer'";
          $stmt = mysqli_prepare($conn, $updateSql);
          mysqli_stmt_bind_param($stmt, "sssss", $fullName, $phoneNumber, $hashedPassword, $isActive, $email);
      } else {
          // Nếu không cập nhật mật khẩu
          $updateSql = "UPDATE Account SET FullName = ?, PhoneNumber = ?, IsActive = ? WHERE Email = ? AND Type = 'Customer'";
          $stmt = mysqli_prepare($conn, $updateSql);
          mysqli_stmt_bind_param($stmt, "ssss", $fullName, $phoneNumber, $isActive, $email);
      }
      
      if (mysqli_stmt_execute($stmt)) {
          $_SESSION['success_message'] = "Cập nhật thông tin khách hàng thành công!";
      } else {
          $_SESSION['error_message'] = "Lỗi: " . mysqli_error($conn);
      }
      
      mysqli_stmt_close($stmt);
      
      // Chuyển hướng để tránh gửi lại form
      header("Location: " . $_SERVER['PHP_SELF']);
      exit;
    }

    // API lấy thông tin chi tiết của khách hàng
    if (isset($_GET['action']) && $_GET['action'] === 'get_customer_details' && isset($_GET['email'])) {
        $email = $_GET['email'];
        
        // Lấy thông tin khách hàng - Thêm bảo mật
        $email = mysqli_real_escape_string($conn, $email);
        
        // Lấy thông tin khách hàng
        $customerSql = "
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
            WHERE A.Email = ? AND A.Type = 'Customer'
            GROUP BY A.FullName, A.Email, A.PhoneNumber, A.CreatedDate, A.IsActive
        ";
        
        $stmt = mysqli_prepare($conn, $customerSql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $customer = mysqli_fetch_assoc($result);
        
        // Lấy danh sách đơn hàng gần đây
        $ordersSql = "
            SELECT 
                OrderId,
                OrderDate,
                TotalAmount,
                Status
            FROM Orders
            WHERE Email = ?
            ORDER BY OrderDate DESC
            LIMIT 5
        ";
        
        $stmt = mysqli_prepare($conn, $ordersSql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $recentOrders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $recentOrders[] = $row;
        }
        
        // Tạo response JSON
        $response = [
            'customer' => $customer,
            'recentOrders' => $recentOrders
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Xử lý chuyển đổi trạng thái IsActive
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
        $email = $_POST['email'] ?? '';
        $currentStatus = $_POST['current_status'] ?? '';

        if (!empty($email)) {
            $newStatus = ($currentStatus === 'Yes') ? 'No' : 'Yes';
            $updateSql = "UPDATE Account SET IsActive = ? WHERE Email = ? AND Type = 'Customer'"; // Thêm điều kiện Type
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
                // Xóa tài khoản chỉ khi là khách hàng (thêm điều kiện bảo mật)
                $deleteAccountSql = "DELETE FROM Account WHERE Email = ? AND Type = 'Customer'";
                $stmt = mysqli_prepare($conn, $deleteAccountSql);
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            
            $_SESSION['success_message'] = "Đã xóa khách hàng thành công!";
            // Chuyển hướng lại trang sau khi xóa
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['error_message'] = "Không có khách hàng nào được chọn để xóa!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    // Thêm code để lấy danh sách khách hàng
    $search = isset($_GET['q']) ? $_GET['q'] : '';
    
    // Tạo truy vấn SQL để lấy danh sách khách hàng với thông tin đơn hàng
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
    ";
    
    // Thêm điều kiện tìm kiếm nếu có
    if (!empty($search)) {
        $search = '%' . $search . '%';
        $sql .= " AND (A.FullName LIKE ? OR A.Email LIKE ? OR A.PhoneNumber LIKE ?)";
    }
    
    // Thêm GROUP BY để tính tổng đơn hàng và tổng chi tiêu
    $sql .= " GROUP BY A.FullName, A.Email, A.PhoneNumber, A.CreatedDate, A.IsActive ORDER BY A.CreatedDate DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    // Bind parameters nếu có tìm kiếm
    if (!empty($search)) {
        mysqli_stmt_bind_param($stmt, "sss", $search, $search, $search);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $customers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $customers[] = $row;
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
    <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong>Quản lý khách hàng</strong></h2>
  </div>
  <form class="d-flex align-items-center gap-2 mb-4" role="search" method="GET" action="#">
    <div class="col-md">
      <input class="form-control" type="search" placeholder="Tìm kiếm..." name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    </div>

    <div class="col-md-auto">
      <button class="btn btn-outline-success" type="submit">
        <i class="bi bi-search"></i>
      </button>
    </div>

    <div class="col-md-auto">
      <button class="btn btn-success" type="button" id="add-customer-btn">
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
                    class="btn btn-sm btn-info text-white">
                    <i class="fa fa-eye"></i>
                  </a>
                  <button type="button" class="btn btn-sm btn-warning text-white edit-customer" 
                          data-email="<?= htmlspecialchars($customer['Email']); ?>">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-danger text-white delete-single" 
                          data-email="<?= htmlspecialchars($customer['Email']); ?>">
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
  </form>
</div>

<!-- Modal Xem Chi Tiết Khách Hàng -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="viewCustomerModalLabel">Chi tiết khách hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <p><strong>Họ tên:</strong> <span id="view-fullName"></span></p>
            <p><strong>Email:</strong> <span id="view-email"></span></p>
            <p><strong>Số điện thoại:</strong> <span id="view-phoneNumber"></span></p>
            <p><strong>Ngày đăng ký:</strong> <span id="view-createdDate"></span></p>
          </div>
          <div class="col-md-6">
            <p><strong>Trạng thái:</strong> <span id="view-isActive"></span></p>
            <p><strong>Tổng đơn hàng:</strong> <span id="view-totalOrders"></span></p>
            <p><strong>Tổng chi tiêu:</strong> <span id="view-totalSpent"></span></p>
          </div>
        </div>
        <h6 class="border-bottom pb-2 mb-3">Đơn hàng gần đây</h6>
        <div class="table-responsive">
          <table class="table table-sm table-bordered">
            <thead class="table-light">
              <tr class="text-center">
                <th>Mã đơn</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
              </tr>
            </thead>
            <tbody id="recent-orders">
              <!-- Dữ liệu đơn hàng sẽ được thêm vào đây bằng JavaScript -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Thêm Khách Hàng -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="addCustomerModalLabel">Thêm khách hàng mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="" id="add-customer-form">
        <div class="modal-body">
          <div class="mb-3">
            <label for="add-email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="add-email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="add-fullName" class="form-label">Họ tên <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="add-fullName" name="fullName" required>
          </div>
          <div class="mb-3">
            <label for="add-phoneNumber" class="form-label">Số điện thoại</label>
            <input type="tel" class="form-control" id="add-phoneNumber" name="phoneNumber">
          </div>
          <div class="mb-3">
            <label for="add-password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="add-password" name="password" required>
          </div>
          <div class="mb-3">
            <label for="add-isActive" class="form-label">Trạng thái</label>
            <select class="form-select" id="add-isActive" name="isActive">
              <option value="Yes" selected>Hoạt động</option>
              <option value="No">Ngừng hoạt động</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" name="add_customer" class="btn btn-success">Thêm</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Chỉnh Sửa Khách Hàng -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="editCustomerModalLabel">Chỉnh sửa khách hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="" id="edit-customer-form">
        <div class="modal-body">
          <input type="hidden" id="edit-email" name="email">
          <div class="mb-3">
            <label for="edit-fullName" class="form-label">Họ tên <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit-fullName" name="fullName" required>
          </div>
          <div class="mb-3">
            <label for="edit-phoneNumber" class="form-label">Số điện thoại</label>
            <input type="tel" class="form-control" id="edit-phoneNumber" name="phoneNumber">
          </div>
          <div class="mb-3">
            <label for="edit-password" class="form-label">Mật khẩu mới <small class="text-muted">(để trống nếu không đổi)</small></label>
            <input type="password" class="form-control" id="edit-password" name="password">
          </div>
          <div class="mb-3">
            <label for="edit-isActive" class="form-label">Trạng thái</label>
            <select class="form-select" id="edit-isActive" name="isActive">
              <option value="Yes">Hoạt động</option>
              <option value="No">Ngừng hoạt động</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" name="update_customer" class="btn btn-warning">Cập nhật</button>
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
        <p>Bạn có chắc chắn muốn xóa (các) khách hàng đã chọn không?</p>
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
/**
 * Script quản lý khách hàng - Phiên bản tối ưu
 * File: customers.js
 */
document.addEventListener('DOMContentLoaded', function() {
    // ----- CÁC HÀM TIỆN ÍCH -----
    
    // Hàm định dạng ngày tháng
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }
    
    // Hàm định dạng tiền tệ
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', { 
            style: 'currency', 
            currency: 'VND' 
        }).format(amount);
    }
    
    // Hiển thị thông báo
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const contentWrapper = document.querySelector('.content-wrapper');
        contentWrapper.insertBefore(alertDiv, contentWrapper.firstChild);
        
        // Tự động ẩn sau 5 giây
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }

    // ----- QUẢN LÝ CHECKBOX VÀ XÓA -----
    
    const selectAllCheckbox = document.getElementById('select-all');
    const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
    const deleteSelectedBtn = document.getElementById('delete-selected');
    
    // Cập nhật trạng thái của nút xóa dựa trên số lượng checkbox được chọn
    function updateDeleteButtonState() {
        const checkedCount = document.querySelectorAll('.customer-checkbox:checked').length;
        deleteSelectedBtn.disabled = checkedCount === 0;
    }
    
    // Thiết lập sự kiện cho checkbox "Chọn tất cả"
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            
            customerCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            updateDeleteButtonState();
        });
    }
    
    // Thiết lập sự kiện cho từng checkbox khách hàng
    customerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButtonState();
            
            // Kiểm tra xem tất cả có được chọn không
            if (selectAllCheckbox) {
                const allChecked = document.querySelectorAll('.customer-checkbox:checked').length === customerCheckboxes.length;
                selectAllCheckbox.checked = allChecked;
            }
        });
    });
    
    // ----- XỬ LÝ XÓA KHÁCH HÀNG -----
    
    // Xử lý nút xóa đơn lẻ
    const deleteSingleButtons = document.querySelectorAll('.delete-single');
    deleteSingleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Bỏ chọn tất cả các checkbox
            customerCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Chỉ chọn checkbox tương ứng với khách hàng muốn xóa
            const email = this.getAttribute('data-email');
            const correspondingCheckbox = document.querySelector(`.customer-checkbox[value="${email}"]`);
            if (correspondingCheckbox) {
                correspondingCheckbox.checked = true;
            }
            
            updateDeleteButtonState();
            
            // Hiển thị modal xác nhận xóa
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        });
    });
    
    // Xử lý nút xác nhận xóa trong modal
    const confirmDeleteButton = document.getElementById('confirm-delete');
    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', function() {
            // Tạo form ẩn để submit
            const form = document.getElementById('customers-form');
            if (form) {
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_customers';
                form.appendChild(actionInput);
                
                form.submit();
            }
        });
    }
    
    // ----- MODAL THÊM MỚI KHÁCH HÀNG -----
    
    const addButton = document.getElementById('add-customer-btn');
    if (addButton) {
        addButton.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('addCustomerModal'));
            modal.show();
        });
    }
    
    // ----- MODAL CHỈNH SỬA KHÁCH HÀNG -----
    
    const editButtons = document.querySelectorAll('.edit-customer');
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const email = this.getAttribute('data-email');
            if (!email) return;
            
            // Tìm dòng dữ liệu tương ứng
            const row = this.closest('tr');
            if (!row) return;
            
            const fullName = row.cells[1]?.textContent || '';
            const phoneNumber = row.cells[3]?.textContent || '';
            const isActive = row.querySelector('.btn-sm')?.textContent.trim() === 'Hoạt động' ? 'Yes' : 'No';
            
            // Điền dữ liệu vào modal
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-fullName').value = fullName;
            document.getElementById('edit-phoneNumber').value = phoneNumber;
            
            const isActiveSelect = document.getElementById('edit-isActive');
            if (isActiveSelect) {
                isActiveSelect.value = isActive;
            }
            
            // Xóa giá trị mật khẩu cũ
            const passwordField = document.getElementById('edit-password');
            if (passwordField) {
                passwordField.value = '';
            }
            
            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
            modal.show();
        });
    });
    
    // Khởi tạo trạng thái nút xóa
    updateDeleteButtonState();
    
    // ----- XỬ LÝ THÔNG BÁO PHP SESSION -----
    
    // PHP code sẽ thêm vào đây khi render
    /* 
    <?php if (isset($_SESSION['success_message'])): ?>
        showAlert('<?= $_SESSION['success_message'] ?>', 'success');
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        showAlert('<?= $_SESSION['error_message'] ?>', 'danger');
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    */
});
</script>

<?php 
    include "view/footer-admin.php";
?>