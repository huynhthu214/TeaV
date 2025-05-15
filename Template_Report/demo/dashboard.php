<?php 
session_start(); 
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
//doanh thu hàng tháng
$query1 = $conn->query("SELECT SUM(TotalAmount) AS TotalRevenue FROM Orders WHERE MONTH(OrderDate) = MONTH(CURDATE()) AND YEAR(OrderDate) = YEAR(CURDATE())");
$total_amount = $query1->fetch_assoc()['TotalRevenue'] ?? 0;
//đơn hàng
$query2 = $conn->query("SELECT COUNT(*) AS TotalCustomers FROM Account WHERE Type='Customer'");
$total_customers = $query2->fetch_assoc()['TotalCustomers'] ?? 0;
//khách hàng
$query3 = $conn->query("SELECT COUNT(*) AS TotalOrders FROM Orders");
$total_orders = $query3->fetch_assoc()['TotalOrders'] ?? 0;
//giá trị trung bình
$query4 = $conn->query("SELECT AVG(TotalAmount) AS AvgOrders FROM Orders");
$avg_orders = $query4->fetch_assoc()['AvgOrders'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TeaV - Dashboard</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="layout/css/style_admin.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="sidebar">
  <div class="brand">
    <div class="brand-text">Quản trị</div>
  </div>

  <hr>

  <a href="#" class="nav-item active">
    <i class="fa-solid fa-gauge-high"></i>
    <span class="nav-text">Thống kê</span>
  </a>

  <a href="#" class="nav-item">
    <i class="fa-solid fa-box"></i>
    <span class="nav-text">Đơn hàng</span>
  </a>

    <a href="#" class="nav-item">
    <i class="fa-solid fa-mug-hot"></i>
    <span class="nav-text">Sản phẩm</span>
  </a>

    <a href="#" class="nav-item">
    <i class="fa-solid fa-users"></i>
    <span class="nav-text">Khách hàng</span>
  </a>

  <a href="#" class="nav-item">
    <i class="fa-solid fa-newspaper"></i>
    <span class="nav-text">Bài đăng</span>
  </a>


  <a href="#" class="nav-item">
    <i class="fa-solid fa-store"></i>
    <span class="nav-text">Thông tin</span>
  </a>

  <hr>

  <div class="toggle-btn">
    <div class="toggle-circle">
      <i class="fa-solid fa-chevron-left"></i>
    </div>
  </div>
</div>

<div class="header d-flex justify-content-end align-items-center px-3" style="height: 60px;">
  <?php if (isset($_SESSION['email'])): ?>
    <?php 
      $firstChar = strtoupper(substr($_SESSION['email'], 0, 1)); 
      $userEmail = htmlspecialchars($_SESSION['email']);
    ?>
    <div class="dropdown">
      <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="avatar-circle"><?php echo $firstChar; ?></div>
        <span class="fw-semibold text-dark"><?php echo $userEmail; ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
      </ul>
    </div>
  <?php endif; ?>
</div>
<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-center mb-4">
  <h2 class="m-0" style="color:rgb(10, 119, 52)"><strong>Thống kê</strong></h2>
  
  <form class="d-flex align-items-center gap-2" role="search" method="GET" action="#">
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
                                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($avg_orders, 0, ',', '.'); ?></div>
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
          <h5>Earnings Overview</h5>
          <canvas id="lineChart"></canvas>
        </div>

        <!-- Biểu đồ tròn -->
        <div class="chart-card">
          <h5>Revenue Sources</h5>
          <canvas id="donutChart"></canvas>
          <div class="legend">
            <span><span class="dot dot-direct"></span>Direct</span>
            <span><span class="dot dot-social"></span>Social</span>
            <span><span class="dot dot-referral"></span>Referral</span>
              </div>
            </div>
          </div>
  </div>
<footer>
          <div class="text-center mt-3">
          <p class="mb-0">
            &copy; <span id="year"></span> 2025 TeaV. All rights reserved.
          </p>
        </div>
</footer>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="layout/js/jquery.js"></script>
</body>
</html>
