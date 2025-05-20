<?php 
    session_start();
    $namePage = "Thông tin";
    include "view/header-admin.php";
?>

<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-start mb-4">
  <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong> Thông tin</strong></h2>
  
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

   
<?php 
    include "view/footer-admin.php";
?>