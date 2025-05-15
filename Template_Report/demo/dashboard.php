<?php 
session_start(); 
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
//doanh thu hàng tháng
$query1 = $conn->query("
    SELECT SUM(TotalAmount) AS TotalRevenue 
    FROM Orders 
    WHERE MONTH(OrderDate) = MONTH(CURDATE()) 
    AND YEAR(OrderDate) = YEAR(CURDATE())");
$total_amount = $query1->fetch_assoc()['TotalRevenue'] ?? 0;
//đơn hàng
$query2 = $conn->query("SELECT COUNT(*) AS TotalCustomers FROM Account WHERE Type='Customer'");
$total_customers = $query2->fetch_assoc()['TotalCustomers'] ?? 0;
//khách hàng
$query3 = $conn->query("SELECT COUNT(*) AS TotalOrders FROM Orders");
$total_orders = $query3->fetch_assoc()['TotalOrders'] ?? 0;
//lợi nhuận
$query4 = $conn->query("
    SELECT SUM(ImportProduct.UnitPrice * ImportProduct.Quantity) AS ImportCost
    FROM ImportProduct
    JOIN Import ON ImportProduct.ImportId = Import.ImportId
    WHERE MONTH(Import.ImportDate) = MONTH(CURDATE()) 
      AND YEAR(Import.ImportDate) = YEAR(CURDATE())
");
$expense = $query4->fetch_assoc()['ImportCost'] ?? 0;
$profit = $total_amount - $expense;

$top_products = [];

$query5 = $conn->query("
    SELECT Product.ProductId, Product.Name, SUM(OrderProduct.Quantity) AS SoldQuantity
    FROM OrderProduct
    JOIN Product ON OrderProduct.ProductId = Product.ProductId
    GROUP BY Product.ProductId, Product.Name
    ORDER BY SoldQuantity DESC
    LIMIT 5
");

if ($query5 && $query5->num_rows > 0) {
    while ($row = $query5->fetch_assoc()) {
        $top_products[] = $row;
    }
}

$monthly_data = [];
$query_line = $conn->query("
    SELECT MONTH(OrderDate) AS Thang, SUM(TotalAmount) AS DoanhThu
    FROM Orders
    WHERE YEAR(OrderDate) = YEAR(CURDATE())
    GROUP BY MONTH(OrderDate)
    ORDER BY MONTH(OrderDate)
");
while($row = $query_line->fetch_assoc()){
  $monthly_data[] = [
    'month' => $row['Thang'],
    'revenue' => $row['DoanhThu']
  ];
}

$donut_data = [];
$query_pie = $conn->query("
    SELECT Product.Name, SUM(OrderProduct.Quantity) AS SoLuong 
    FROM OrderProduct
    JOIN Product ON OrderProduct.ProductId = Product.ProductId
    GROUP BY Product.Name
    ORDER BY SoLuong DESC
    LIMIT 5   
");
while($row = $query_pie->fetch_assoc()){
  $donut_data[] = [
    'name' => $row['Name'],
    'quantity' => $row['SoLuong']
  ];
}

  $namePage = "Thống kê";
  include "view/header-admin.php";
?>

<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-start mb-4">
  <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong>Thống kê</strong></h2>
  
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

    <div class="row">
               <!-- Earnings (Monthly) Card Example -->
         <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Doanh thu hàng tháng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($total_amount, 0, ',', '.'); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-solid fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                           

          <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                  <div class="card-body">
                      <div class="row no-gutters align-items-center">
                          <div class="col mr-2">
                              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                  Đơn hàng</div>
                              <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_orders; ?></div>
                          </div>
                          <div class="col-auto">
                              <i class="fa-solid fa-box-open fa-2x text-gray-300"></i>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Khách hàng
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $total_customers?></div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-info" role="progressbar"
                                                style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

              <!-- Pending Requests Card Example -->
              <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card border-left-warning shadow h-100 py-2">
                      <div class="card-body">
                          <div class="row no-gutters align-items-center">
                              <div class="col mr-2">
                                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                      Lợi nhuận</div>
                                  <span class="<?php echo ($profit < 0) ? 'text-danger' : 'text-success'; ?>">
                                      <?php echo number_format($profit, 0, ',', '.'); ?>
                                  </span>
                              </div>
                              <div class="col-auto">
                                  <i class="fa-solid fa-chart-line fa-2x text-gray-300"></i>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

      <div class="row-chart">
        <!-- Biểu đồ đường -->
        <div class="chart-card">
          <h5>Tổng quan doanh thu</h5>
          <canvas id="lineChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Biểu đồ tròn -->
        <div class="chart-card">
          <h5>Nguồn thu nhập</h5>
          <canvas id="donutChart" style="max-height: 300px;"></canvas>
            </div>
          </div>
            <div class="mt-5 mb-5">
                  <h2 class="text-success fw-bold mb-4">Sản phẩm bán chạy</h2>
                  <div class="table-responsive">
                      <table class="table table-striped table-bordered align-middle">
                          <thead class="table-success text-center">
                              <tr>
                                  <th>STT</th>
                                  <th>Mã sản phẩm</th>
                                  <th>Tên sản phẩm</th>
                                  <th>Số lượng đã bán</th>
                      </tr>
                  </thead>
                  <tbody>
              <?php if (!empty($top_products)): ?>
                  <?php foreach ($top_products as $index => $product): ?>
                      <tr class="text-center">
                          <td><?php echo $index + 1; ?></td>
                          <td><?php echo htmlspecialchars($product['ProductId']); ?></td>
                          <td class="text-start"><?php echo htmlspecialchars($product['Name']); ?></td>
                          <td><?php echo $product['SoldQuantity']; ?></td>
                      </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr>
                      <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                  </tr>
              <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
  </div>

<?php 
  include "view/footer-admin.php";
?>