<?php session_start(); 

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
    <i class="fa-solid fa-newspaper"></i>
    <span class="nav-text">Bài đăng</span>
  </a>

  <a href="#" class="nav-item">
    <i class="fa-solid fa-mug-hot"></i>
    <span class="nav-text">Sản phẩm</span>
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

<div class="content-wrapper">

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
        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  <?php endif; ?>
</div>
  <div class="page-title">
    <h2><strong>Thống kê</strong></h2>
  </div>

  <div class="card">
    <div class="table-header d-flex justify-content-between align-items-center p-3">
      <h2>DataTables Example</h2>
      <div>
        <label>Search:
          <input type="text" class="form-control d-inline-block w-auto ms-2">
        </label>
      </div>
    </div>

    <div class="entries-control px-3 pb-2">
      Show
      <select class="form-select d-inline-block w-auto mx-2">
        <option>10</option>
        <option>25</option>
        <option>50</option>
        <option>100</option>
      </select>
      entries
    </div>

    <div class="table-wrapper p-3">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Office</th>
            <th>Age</th>
            <th>Start date</th>
            <th>Salary</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Airi Satou</td>
            <td>Accountant</td>
            <td>Tokyo</td>
            <td>33</td>
            <td>2008/11/28</td>
            <td>$162,700</td>
          </tr>
          <tr>
            <td>Angelica Ramos</td>
            <td>Chief Executive Officer (CEO)</td>
            <td>London</td>
            <td>47</td>
            <td>2009/10/09</td>
            <td>$1,200,000</td>
          </tr>
          <tr>
            <td>Ashton Cox</td>
            <td>Junior Technical Author</td>
            <td>San Francisco</td>
            <td>66</td>
            <td>2009/01/12</td>
            <td>$86,000</td>
          </tr>
          <tr>
            <td>Bradley Greer</td>
            <td>Software Engineer</td>
            <td>London</td>
            <td>41</td>
            <td>2012/10/13</td>
            <td>$132,000</td>
          </tr>
          <tr>
            <td>Brenden Wagner</td>
            <td>Software Engineer</td>
            <td>San Francisco</td>
            <td>28</td>
            <td>2011/06/07</td>
            <td>$206,850</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="layout/js/jquery.js"></script>
</body>
</html>
