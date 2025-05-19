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

// Xử lý xuất CSV
if (isset($_GET['export']) && $_GET['export'] == '1') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=khach_hang.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Họ tên', 'Email', 'Số điện thoại', 'Ngày đăng ký', 'Tổng đơn hàng', 'Tổng chi tiêu', 'Trạng thái']);

    foreach ($customers as $row) {
        fputcsv($output, [
            $row['FullName'],
            $row['Email'],
            $row['PhoneNumber'],
            $row['CreatedDate'],
            $row['TotalOrders'],
            $row['TotalSpent'],
            $row['IsActive'] === 'Yes' ? 'Hoạt động' : 'Ngừng'
        ]);
    }

    fclose($output);
    exit;
}

?>


<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-start mb-4">
  <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong> Quản lý khách hàng</strong></h2>
  
  <form class="d-flex align-items-center gap-2 mt-4" role="search" method="GET" action="#">
    <input class="form-control" type="search" placeholder="Tìm kiếm..." name="q" aria-label="Search">
    <button class="btn btn-outline-success" type="submit">
      <i class="bi bi-search"></i>
    </button>
    <button class="btn btn-primary" type="button" onclick="exportData()">
      <i class="bi bi-download me-1"></i>
    </button>
  </form>
</div>

   <div class="table-responsive">
    <form method="POST" action="">
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-success text-center">
          <tr>
            <th><input type="checkbox" id="select-all"></th>
            <th>STT</th>
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
                <td><input type="checkbox" name="select[]" value="<?php echo $customer['Email']; ?>"></td>
                <td><?= $index + 1; ?></td>
                <td class="text-start"><?= htmlspecialchars($customer['FullName']); ?></td>
                <td><?= htmlspecialchars($customer['Email']); ?></td>
                <td><?= htmlspecialchars($customer['PhoneNumber']); ?></td>
                <td><?= date("d/m/Y", strtotime($customer['CreatedDate'])); ?></td>
                <td><?= $customer['TotalOrders']; ?></td>
                <td><?= number_format($customer['TotalSpent'], 0, ',', '.'); ?> đ</td>
                <td>
                  <span class="badge bg-<?= $customer['IsActive'] === 'Yes' ? 'success' : 'secondary'; ?>">
                    <?= $customer['IsActive'] === 'Yes' ? 'Hoạt động' : 'Ngừng'; ?>
                  </span>
                </td>
                <td>
                  <a href="view-customer.php?email=<?= urlencode($customer['Email']); ?>" class="btn btn-sm btn-info">Xem</a>
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
    </form>
  </div>
</div>

<?php 
    include "view/footer-admin.php";
?>