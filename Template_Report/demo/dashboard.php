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
    <div class="brand-text">ADMIN</div>
  </div>

  <hr>

  <a href="#" class="nav-item active">
    <i class="fa-solid fa-gauge-high"></i> Dashboard
  </a>

  <h6>Interface</h6>
  <button class="nav-item d-block btn text-start w-100" data-bs-toggle="collapse" data-bs-target="#componentsCollapse" aria-expanded="false" aria-controls="componentsCollapse">
  <i class="fa-solid fa-gear"></i> Components
  <span style="float:right;"><i class="fa-solid fa-chevron-down"></i></span>
</button>
<div class="collapse" id="componentsCollapse">
  <a class="nav-item ps-4" href="#">Buttons</a>
  <a class="nav-item ps-4" href="#">Cards</a>
</div>

  <hr>

  <h6>Addons</h6>
  <a href="#" class="nav-item">
    <i class="fa-solid fa-folder"></i> Pages
    <span style="margin-left:auto;"><i class="fa-solid fa-chevron-right"></i></span>
  </a>
  <a href="#" class="nav-item">
    <i class="fa-solid fa-chart-column"></i> Charts
  </a>
  <a href="#" class="nav-item active">
    <i class="fa-solid fa-table-cells"></i> Tables
  </a>

  <hr>

  <div class="toggle-btn">
    <div class="toggle-circle">
      <i class="fa-solid fa-chevron-left"></i>
    </div>
  </div>
</div>

<div class="content-wrapper">

  <div class="header">
    <div class="search-bar">
      <input type="text" placeholder="Search for...">
      <button><i class="fas fa-search"></i></button>
    </div>

    <div class="header-actions">
      <div class="icon-button">
        <i class="fas fa-bell"></i>
        <span class="badge">3+</span>
      </div>
      <div class="icon-button">
        <i class="fas fa-envelope"></i>
        <span class="badge">7</span>
      </div>
      <div class="user-info">
        <span class="user-name">Douglas McGee</span>
        <img src="https://img.icons8.com/ios-filled/50/000000/user-male-circle.png" alt="User Avatar">
      </div>
    </div>
  </div>

  <div class="page-title">
    <h1>Tables</h1>
    <p>DataTables is a third party plugin that is used to generate the demo table below.
      For more information about DataTables, please visit the
      <a href="#" style="color:#4e73df;">official DataTables documentation</a>.
    </p>
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
