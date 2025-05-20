<?php 
session_start();
$namePage = "Quản lý đơn hàng";
include "view/header-admin.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// ==== Ghi nhớ lựa chọn limit vào SESSION nếu có ====
if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
    $_SESSION['limit'] = intval($_GET['limit']);
}
$limit = isset($_SESSION['limit']) ? $_SESSION['limit'] : 10;

// ==== Lấy trang hiện tại ====
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// ==== Tổng số đơn hàng để phân trang ====
$count_sql = "SELECT COUNT(*) as total FROM Orders";
$count_result = mysqli_query($conn, $count_sql);
$total_orders = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_orders / $limit);

// ==== Truy vấn danh sách đơn hàng có phân trang ====
$sql = "
    SELECT 
        o.OrderId,
        o.OrderDate,
        o.TotalAmount,
        o.PaymentId,
        o.StatusOrder,
        a.FullName AS CustomerName,
        a.Email AS CustomerEmail,
        GROUP_CONCAT(CONCAT(p.Name, ' (x', op.Quantity, ')') SEPARATOR '<br>') AS Products
    FROM Orders o
    LEFT JOIN OrderProduct op ON o.OrderId = op.OrderId
    LEFT JOIN Product p ON op.ProductId = p.ProductId
    LEFT JOIN Account a ON a.Email = o.Email
    GROUP BY o.OrderId
    ORDER BY o.OrderDate DESC
    LIMIT $limit OFFSET $offset
";

$result = mysqli_query($conn, $sql);
$orders = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>

<!---->
<div class="content-wrapper">
  <div class="page-title d-flex justify-content-between align-items-start mb-4">
    <h2 style="color:rgb(10, 119, 52); margin-top: -10px;"><strong>Quản lý đơn hàng</strong></h2>
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

<!-- Bảng đơn hàng -->
<div class="table-responsive">
  <form method="POST" action="">
    <table class="table table-hover table-bordered align-middle">
      <thead class="table-success text-center align-middle">
        <tr>
          <th><input type="checkbox" id="select-all"></th>
          <th>Mã đơn</th>
          <th>Khách hàng</th>
          <th>Sản phẩm</th>
          <th>Ngày đặt</th>
          <th>Tổng tiền</th>
          <th>Thanh toán</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($orders)): ?>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td class="text-center">
                <input type="checkbox" name="select[]" value="<?= $order['OrderId']; ?>">
              </td>
              <td><strong><?= htmlspecialchars($order['OrderId']); ?></strong></td>
              <td>
                <?= htmlspecialchars($order['CustomerName']); ?><br>
                <small class="text-muted"><?= htmlspecialchars($order['CustomerEmail']); ?></small>
              </td>
              <td><?= $order['Products']; ?></td>
              <td>
                <?php
                  $parts = explode(' ', $order['OrderDate']);
                  echo $parts[0] . '<br><small class="text-muted">' . ($parts[1] ?? '') . '</small>';
                ?>
              </td>
              <td><?= number_format($order['TotalAmount'], 0, ',', '.'); ?> VND</td>
              <td><?= $order['PaymentId'] ?? '<i>Chưa có</i>'; ?></td>
              <td>
                <?php
                  $status = $order['StatusOrder'];
                  $badgeClass = match ($status) {
                    'Đã giao' => 'bg-success',
                    'Đang xử lý' => 'bg-warning text-dark',
                    'Đã hủy' => 'bg-danger',
                    default => 'bg-secondary'
                  };
                ?>
                <span class="badge <?= $badgeClass; ?>"><?= $status; ?></span>
              </td>
              <td class="text-center">
                <a href="order-detail.php?id=<?= $order['OrderId']; ?>" class="btn btn-sm btn-info text-white" title="Xem">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="edit-order.php?id=<?= $order['OrderId']; ?>" class="btn btn-sm btn-warning text-white" title="Sửa">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('<?= $order['OrderId']; ?>')" title="Xóa">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="text-center text-muted">Không có dữ liệu</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </form>
</div>

<!-- Modal thêm đơn hàng -->
<div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="add-order.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addOrderModalLabel">Thêm đơn hàng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="order" class="form-label">Mã đơn hàng</label>
            <input type="text" class="form-control" id="order" name="order" required>
          </div>
          <div class="mb-3">
            <label for="products" class="form-label">Danh sách sản phẩm (ID:Số lượng, cách nhau dấu phẩy)</label>
            <input type="text" class="form-control" id="products" name="products" required>
            <small class="text-muted">VD: P001:2,P002:1</small>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email khách hàng</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="order-date" class="form-label">Ngày đặt</label>
            <input type="date" class="form-control" id="order-date" name="order-date" required>
          </div>
          <div class="mb-3">
            <label for="order-price" class="form-label">Tổng tiền</label>
            <input type="text" class="form-control" id="order-price" name="order-price" required>
          </div>
          <div class="mb-3">
            <label for="payment" class="form-label">Hình thức thanh toán</label>
            <input type="text" class="form-control" id="payment" name="payment" required>
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <input type="text" class="form-control" id="status" name="status" required>
          </div>
          
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Lưu</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        </div>
      </div>
    </form>
  </div>
</div>
</div>


<?php include "view/footer-admin.php"; ?>
