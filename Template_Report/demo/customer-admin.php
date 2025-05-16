<?php 
    session_start();
    $namePage = "Quản lý khách hàng";
    include "view/header-admin.php";
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
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-success text-center">
          <tr>
            <th><input type="checkbox" id="select-all"></th>
            <th>Đơn hàng</th>
            <th>Sản phẩm</th>
            <th>Khách hàng</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Hình thức thanh toán</th>
            <th>Thanh toán</th>
            <th>Trạng thái</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($top_products)): ?>
            <?php foreach ($top_products as $index => $product): ?>
              <tr class="text-center">
                <td><input type="checkbox" name="select[]" value="<?php echo $product['ProductId']; ?>"></td>
                <td><?php echo htmlspecialchars($product['ProductId']); ?></td>
                <td class="text-start"><?php echo htmlspecialchars($product['Name']); ?></td>
                <td><?php echo $product['SoldQuantity']; ?></td>
                <!-- Thêm các cột khác nếu có -->
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="10" class="text-center text-muted">Không có dữ liệu</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
<?php 
    include "view/footer-admin.php";
?>