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
<div class="content-wrapper container-fluid px-4">
  <div class="page-title d-flex justify-content-between align-items-start mb-4">
    <h2 style="color:rgb(10, 119, 52); margin-top: 0;"><strong>Quản lý đơn hàng</strong></h2>
  </div>

  <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap mb-3">
    <form class="d-flex align-items-center gap-2" role="search" method="GET" action="#">
      <input class="form-control" type="search" placeholder="Tìm kiếm..." name="q" aria-label="Search">
      <button class="btn btn-outline-success" type="submit">
        <i class="bi bi-search"></i>
      </button>
    </form>
    <button class="btn btn-primary" type="button" onclick="exportData()">
      <i class="bi bi-download me-1"></i>
    </button>
    <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#addOrderModal">Thêm</button>
    <button class="btn btn-danger" type="button" onclick="deleteSelected()"> Hủy</button>
  </div>

  <!-- Bộ chọn limit -->
  <form method="GET" class="mb-3 d-flex align-items-center gap-2">
    <label for="limit">Hiển thị:</label>
    <select name="limit" id="limit" class="form-select w-auto" onchange="this.form.submit()">
      <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
      <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
      <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
      <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
    </select>
    <input type="hidden" name="page" value="1">
  </form>

<!-- Bảng đơn hàng -->
<div class="table-responsive">
  <table class="table table-striped table-bordered align-middle table-order">
    <thead class="table-success text-center">
      <tr>
        <th class="col-checkbox"><input type="checkbox" id="select-all"></th>
        <th class="col-order">Đơn hàng</th>
        <th class="col-product">Sản phẩm</th>
        <th class="col-customer">Khách hàng</th>
        <th class="col-date">Ngày đặt</th>
        <th class="col-total">Tổng tiền</th>
        <th class="col-payment">Hình thức <br> thanh toán</th>
        <th class="col-status">Trạng thái</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
          <tr class="text-center">
            <td><input type="checkbox" name="select[]" value="<?= $order['OrderId']; ?>"></td>
            <td class="text-start text-truncate"><?= htmlspecialchars($order['OrderId']); ?></td>
            <td class="text-start"><?= $order['Products']; ?></td>
            <td class="text-start text-truncate">
              <?= htmlspecialchars($order['CustomerName']); ?><br>
              <small class="text-muted"><?= htmlspecialchars($order['CustomerEmail']); ?></small>
            </td>
            <td class="text-start">
              <?php
                $parts = explode(' ', $order['OrderDate']);
                echo $parts[0] . '<br><small class="text-muted">' . ($parts[1] ?? '') . '</small>';
              ?>
            </td>
            <td class="text-start text-truncate"><?= number_format($order['TotalAmount'], 0, ',', '.'); ?> đ</td>
            <td class="text-start text-truncate"><?= $order['PaymentId'] ?? '<i>Chưa có</i>'; ?></td>
            <td class="text-start text-truncate"><?= $order['StatusOrder']; ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="8" class="text-center text-muted">Không có dữ liệu</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>


  <!-- PHÂN TRANG -->
  <?php if ($total_pages > 1): ?>
    <nav class="mt-3">
      <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
          <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>">«</a></li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
            <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
          <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>">»</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php endif; ?>
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
<?php include "view/footer-admin.php"; ?>
