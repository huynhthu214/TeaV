<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TeaV - <?php echo $namePage ?></title>
  <link rel="icon" type="image/png" href="../demo/layout/images/tea2.png">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="layout/css/style_admin_dashboard.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="layout/css/style_admin.css"/>
  <link rel="stylesheet" href="layout/css/style_admin_order.css"/>
  <link rel="stylesheet" href="layout/css/style_admin_import.css"/>
  <link rel="stylesheet" href="layout/css/style_footer_admin.css"/>
  <link rel="stylesheet" href="layout/css/style_header_admin.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body id="mainBody">
<div class="sidebar" id="sidebar">
  <div class="brand">
    <div class="brand-text">Quản trị</div>
  </div>

  <hr>

  <a href="dashboard.php" class="nav-item <?php if($namePage === 'Thống kê') echo 'active'; ?>">
    <i class="fa-solid fa-gauge-high"></i>
    <span class="nav-text">Thống kê</span>
  </a>

  <a href="order-admin.php" class="nav-item <?php if($namePage === 'Quản lý đơn hàng') echo 'active'; ?>">
    <i class="fa-solid fa-box"></i>
    <span class="nav-text">Quản lý đơn hàng</span>
  </a>

  <a href="import.php" class="nav-item <?php if($namePage === 'Quản lý nhập hàng') echo 'active'; ?>">
   <i class="fa-solid fa-truck-loading"></i>
    <span class="nav-text">Quản lý nhập hàng</span>
  </a>

    <a href="products-admin.php" class="nav-item <?php if($namePage === 'Quản lý sản phẩm') echo 'active'; ?>">
    <i class="fa-solid fa-mug-hot"></i>
    <span class="nav-text">Quản lý sản phẩm</span>
  </a>

    <a href="customer-admin.php" class="nav-item <?php if($namePage === 'Quản lý khách hàng') echo 'active'; ?>">
    <i class="fa-solid fa-users"></i>
    <span class="nav-text">Quản lý khách hàng</span>
  </a>

  <a href="blog-admin.php" class="nav-item <?php if($namePage === 'Quản lý bài đăng') echo 'active'; ?>">
    <i class="fa-solid fa-newspaper"></i>
    <span class="nav-text">Quản lý bài đăng</span>
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
        <li><a class="dropdown-item text-danger" href="../demo/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
      </ul>
    </div>
  <?php endif; ?>
</div>