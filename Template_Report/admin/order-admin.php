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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    // Lấy dữ liệu
    $orderId = $conn->real_escape_string($_POST['order_id']);
    $currentStatus = $_POST['current_status'];

    // Toggle trạng thái
    if ($currentStatus === 'Chưa xử lý') {
        $newStatus = 'Đã xử lý';
    } else {
        $newStatus = 'Chưa xử lý';
    }

    // Câu lệnh update
    $sqlUpdate = "UPDATE Orders SET StatusOrder = '$newStatus' WHERE OrderId = '$orderId'";
    $conn->query($sqlUpdate);

    // Quay lại trang admin
    header("Location: order-admin.php");
    exit;
}


// ==== Truy vấn danh sách đơn hàng có phân trang ====
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$search_safe = mysqli_real_escape_string($conn, $search); // luôn khai báo

$search_sql = '';
if ($search !== '') {
    $search_sql = " AND (
        o.OrderId LIKE '%$search_safe%' OR 
        a.FullName LIKE '%$search_safe%' OR 
        a.Email LIKE '%$search_safe%' OR 
        p.Name LIKE '%$search_safe%'
    )";
}
// Tổng số bản ghi sau tìm kiếm
$count_sql = "
    SELECT COUNT(DISTINCT o.OrderId) as total
    FROM Orders o
    LEFT JOIN Account a ON a.Email = o.Email
    LEFT JOIN OrderProduct op ON o.OrderId = op.OrderId
    LEFT JOIN Product p ON op.ProductId = p.ProductId
    WHERE 1=1 $search_sql
";
$count_result = mysqli_query($conn, $count_sql);
$total_orders = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_orders / $limit);

// Truy vấn chính có tìm kiếm và phân trang
$sql = "
    SELECT 
        o.OrderId,
        o.OrderDate,
        o.TotalAmount,
        o.PaymentId,
        o.StatusOrder,
        p.PaymentStatus,
        a.FullName AS CustomerName,
        a.Email AS CustomerEmail,
        GROUP_CONCAT(CONCAT(pr.Name, ' (x', op.Quantity, ')') SEPARATOR '<br>') AS Products
    FROM Orders o
    LEFT JOIN OrderProduct op ON o.OrderId = op.OrderId
    LEFT JOIN Product pr ON op.ProductId = pr.ProductId
    LEFT JOIN Account a ON a.Email = o.Email
    LEFT JOIN Payment p ON o.PaymentID = p.PaymentID
    WHERE 1=1 $search_sql
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
    <input class="form-control me-2" type="search" placeholder="Tìm kiếm..." name="q" value="<?= htmlspecialchars($search) ?>" aria-label="Search">
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
    <?php if ($search): ?>
      <input type="hidden" name="q" value="<?= htmlspecialchars($search); ?>">
    <?php endif; ?>

      <button class="btn btn-primary" type="button" onclick="exportData(this)" data-type="order">
        <i class="bi bi-download me-1"></i>
      </button>
    <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#addOrderModal">
      <i class="bi bi-plus-circle me-1"></i> Thêm
    </button>
    <button type="button" id="delete" class="btn btn-danger" disabled>
      <i class="bi bi-trash me-1"></i>Xóa
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
                  $formattedDate = date('d-m-Y', strtotime($order['OrderDate']));
                  $time = date('H:i:s', strtotime($order['OrderDate']));
                  echo $formattedDate . '<br><small class="text-muted">' . $time . '</small>';
                ?>
              </td>
              <td><?= number_format($order['TotalAmount'], 3); ?> VND</td>
              <td><?= $order['PaymentStatus']?></td>
              <td>
                <form method="POST" action="">
                  <input type="hidden" name="order_id" value="<?= $order['OrderId'] ?>">
                  <input type="hidden" name="current_status" value="<?= htmlspecialchars($order['StatusOrder']) ?>">
                  <button type="submit" name="toggle_status" class="btn btn-sm <?= $order['StatusOrder'] === 'Chưa xử lý' ? 'btn-warning' : 'btn-success' ?>" title="<?= $order['StatusOrder'] ?>">
                    <?= $order['StatusOrder'] ?>
                  </button>
                </form>
              </td>
              <td class="text-center">
                <a href="order-detail.php?id=<?= $order['OrderId']; ?>" class="btn btn-sm btn-info text-white" title="Xem">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="edit-order.php?id=<?= $order['OrderId']; ?>" class="btn btn-sm btn-warning text-white" title="Sửa">
                  <i class="bi bi-pencil-square"></i>
                </a>
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
  <div class="modal-dialog">
    <form method="POST" action="add-order.php" id="orderForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addOrderModalLabel">Thêm đơn hàng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">
          <!-- Email khách -->
          <div class="mb-3">
            <label for="email" class="form-label">Email khách hàng</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>

          <!-- Mã đơn hàng -->
            <div class="mb-3">
              <label for="order-id" class="form-label">Mã đơn hàng</label>
              <input type="text" id="orderId" name="orderId" class="form-control" readonly>
            </div>
            <!-- Chọn sản phẩm -->
<div class="mb-3">
  <label class="form-label">Sản phẩm</label>
  <div id="product-list">
    <div class="input-group mb-2 product-item">
      <select class="form-select product-select" name="products[]" required>
        <option value="">-- Chọn sản phẩm --</option>
        <?php
          $product_sql = "SELECT ProductId, Name, Price FROM Product";
          $product_result = mysqli_query($conn, $product_sql);
          while ($prod = mysqli_fetch_assoc($product_result)) {
              echo '<option value="'.$prod['ProductId'].'" data-price="'.$prod['Price'].'">'
                  . htmlspecialchars($prod['Name']) . ' - ' . number_format($prod['Price'], 3) . ' VND</option>';
          }
        ?>
      </select>
      <input type="number" class="form-control ms-2 quantity-input" name="quantities[]" min="1" value="1" required>
      <button type="button" class="btn btn-danger ms-2 remove-product"><i class="bi bi-x"></i></button>
    </div>
  </div>
  <button type="button" class="btn btn-outline-primary btn-sm" id="addProductRow"><i class="bi bi-plus"></i> Thêm sản phẩm</button>
</div>
          <!-- Tổng tiền (readonly) -->
          <div class="mb-3">
            <label class="form-label">Tổng tiền (ước tính)</label>
            <input type="text" class="form-control" id="total-amount" readonly>
          </div>

          <!-- Thanh toán -->
          <div class="mb-3">
            <label for="payment" class="form-label">Hình thức thanh toán</label>
            <input type="text" class="form-control" id="payment" name="payment" required>
          </div>

          <!-- Trạng thái -->
          <div class="mb-3">
            <label for="status" class="form-label">Trạng thái đơn hàng</label>
            <select class="form-select" id="status" name="status" required>
              <option value="Đang xử lý">Đang xử lý</option>
              <option value="Đã giao">Đã giao</option>
              <option value="Đã hủy">Đã hủy</option>
            </select>
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
