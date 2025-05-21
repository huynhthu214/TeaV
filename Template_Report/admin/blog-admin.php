<?php 
session_start();
$namePage = "Quản lý bài đăng";
include "view/header-admin.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$isShowFilter = isset($_GET['isshow']) ? trim($_GET['isshow']) : '';

$conditions = [];
$params = [];

if (!empty($keyword)) {
    $keyword_like = '%' . $keyword . '%';
    $conditions[] = "(b.Title LIKE ? OR a.FullName LIKE ?)";
    $params[] = $keyword_like;
    $params[] = $keyword_like;
}

if ($isShowFilter !== '') {
    $conditions[] = "b.IsShow = ?";
    $params[] = $isShowFilter;
}

$where = '';
if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

$sql = "
    SELECT 
        b.BlogId,
        b.Title,
        b.DateUpload,
        b.IsShow,
        a.FullName AS Author,
        COUNT(r.ReactionId) AS CommentCount
    FROM Blog b
    LEFT JOIN Account a ON b.Email = a.Email
    LEFT JOIN Reaction r ON b.BlogId = r.BlogId
    $where
    GROUP BY b.BlogId, b.Title, b.DateUpload, b.IsShow, a.FullName
    ORDER BY b.DateUpload DESC
";

// Prepare và bind với mysqli
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    die("Chuẩn bị câu lệnh thất bại: " . mysqli_error($conn));
}

if (!empty($params)) {
    // Tạo kiểu dữ liệu cho bind_param: tất cả đều string => 's' * số tham số
    $types = str_repeat('s', count($params));
    // bind_param yêu cầu truyền tham chiếu, dùng call_user_func_array
    $bind_names[] = $types;
    for ($i=0; $i < count($params); $i++) {
        $bind_name = 'bind' . $i;
        $$bind_name = $params[$i];
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array(array($stmt, 'bind_param'), $bind_names);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $blogid = $_POST['blogid'] ?? '';
    $current = $_POST['current_status'] ?? '';

    if ($blogid !== '') {
        $newStatus = ($current === 'Yes') ? 'No' : 'Yes';
        $updateSql = "UPDATE Blog SET IsShow = ? WHERE BlogId = ?";
        $stmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($stmt, "ss", $newStatus, $blogid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Chuyển hướng lại trang để tránh submit form lại khi refresh
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

?>

<style>
  .toast-alert {
  position: fixed;
  top: 20px;
  right: 20px;
  min-width: 250px;
  z-index: 1055; /* cao hơn modal và các phần khác */
}
</style>

<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-center mb-4">

  <?php if (isset($_GET['deleted'])): ?>
  <?php if ($_GET['deleted'] === 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show toast-alert" role="alert" id="toast-alert">
      Đã xóa <strong>thành công</strong> các bài viết đã chọn.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php elseif ($_GET['deleted'] === 'error'): ?>
    <div class="alert alert-danger alert-dismissible fade show toast-alert" role="alert" id="toast-alert">
      Có lỗi xảy ra khi xóa bài viết. Vui lòng thử lại.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
<?php endif; ?>

  <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong> Quản lý bài đăng</strong></h2>
</div>

<!-- Thanh tìm kiếm + nút thêm + nút xóa cùng 1 hàng -->
<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
  <form class="d-flex align-items-center gap-2 flex-grow-1" method="GET" action="">
    <div class="col-md">
      <input class="form-control" type="search" placeholder="Tìm theo tiêu đề hoặc người viết..." name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    </div>

    <div class="col-md-auto">
      <select class="form-select" name="isshow">
        <option value=""> Tất cả trạng thái </option>
        <option value="Yes" <?= (($_GET['isshow'] ?? '') === 'Yes') ? 'selected' : '' ?>>Hiển thị</option>
        <option value="No" <?= (($_GET['isshow'] ?? '') === 'No') ? 'selected' : '' ?>>Ẩn</option>
      </select>
    </div>

    <div class="col-md-auto">
      <button class="btn btn-outline-success" type="submit">
        <i class="bi bi-search"></i>
      </button>
    </div>
  </form>

  <!-- Nút Thêm -->
  <a href="blog-editor.php" class="btn btn-success">
    <i class="bi bi-plus-circle"></i> Thêm
  </a>

  <!-- Nút Xóa + Modal trong 1 form riêng -->
  <form id="delete-form" action="delete-blogs.php" method="POST">
    <button type="button" id="delete-selected" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" disabled>
      <i class="bi bi-trash me-1"></i> Xóa
    </button>

    <!-- Modal xác nhận xóa -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Bạn có chắc chắn muốn xóa các bài đăng đã chọn không? Hành động này không thể hoàn tác.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" name="delete_posts" class="btn btn-danger">Xóa</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>


<form id="posts-form" method="POST" action="">
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-success text-center">
        <tr>
          <th><input type="checkbox" id="select-all"></th>
          <th>Tiêu đề</th>
          <th>Người viết</th>
          <th>Ngày đăng</th>
          <th>Bình luận</th>
          <th>Trạng thái</th>
          <th>Thao tác</th> 
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($blogs)): ?>
          <?php foreach ($blogs as $index => $blog): ?>
            <tr class="text-center">
              <td><input type="checkbox" class="post-checkbox" value="<?= $blog['BlogId'] ?>"></td>
              <td class="text-start"><?= htmlspecialchars($blog['Title']) ?></td>
              <td><?= htmlspecialchars($blog['Author']) ?></td>
              <td><?= date('d/m/Y', strtotime($blog['DateUpload'])) ?></td>
              <td><?= $blog['CommentCount'] ?></td>
              <td>
                <form method="POST" action="" style="display:inline;">
                  <input type="hidden" name="blogid" value="<?= htmlspecialchars($blog['BlogId']) ?>">
                  <input type="hidden" name="current_status" value="<?= $blog['IsShow'] ?>">
                  <button type="submit" name="toggle_status" class="btn btn-sm <?= $blog['IsShow'] === 'Yes' ? 'btn-success' : 'btn-secondary' ?>" title="<?= $blog['IsShow'] === 'Yes' ? 'Đang hiển thị' : 'Đang ẩn' ?>">
                      <?= $blog['IsShow'] === 'Yes' ? "Hiển thị" : "Ẩn" ?>
                  </button>
                </form>
              </td>
              <td>
                <!-- Xem bình luận -->
                <a href="comment-admin.php?blogid=<?= urlencode($blog['BlogId']) ?>" class="btn btn-sm btn-primary text-white" title="Xem bình luận">
                  <i class="bi bi-chat-dots"></i>
                </a>
                <!-- Chỉnh sửa bài đăng -->
                <a href="blog-edit.php?blogid=<?= rawurlencode($blog['BlogId']) ?>" class="btn btn-sm btn-warning text-white" title="Sửa">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <!-- Xem bai viet -->
                <a href="blog-detail.php?id=<?= urlencode($blog['BlogId']); ?>" class="btn btn-sm btn-info text-white" title="Xem">
                  <i class="fa fa-eye"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center text-muted">Không có bài đăng nào phù hợp</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const selectAllCheckbox = document.getElementById('select-all');
  const checkboxes = document.querySelectorAll('.post-checkbox');
  const deleteBtn = document.getElementById('delete-selected');
  const deleteForm = document.getElementById('delete-form');

  // Cập nhật trạng thái nút xóa
  function updateDeleteButton() {
    const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
    deleteBtn.disabled = !anyChecked;
  }

  // Xử lý chọn tất cả
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function () {
      checkboxes.forEach(cb => cb.checked = this.checked);
      updateDeleteButton();
    });
  }

  checkboxes.forEach(cb => {
    cb.addEventListener('change', updateDeleteButton);
  });

  // Trước khi mở modal xóa, thêm các checkbox được chọn vào form
  deleteBtn.addEventListener('click', () => {
    // Xóa input ẩn cũ
    document.querySelectorAll('#delete-form input[name="blog_ids[]"]').forEach(e => e.remove());

    // Tạo input ẩn từ checkbox đã chọn
    checkboxes.forEach(cb => {
      if (cb.checked) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'blog_ids[]';
        hiddenInput.value = cb.value;
        deleteForm.appendChild(hiddenInput);
      }
    });
  });
});

window.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('toast-alert');
    if (toast) {
      setTimeout(() => {
        // Dùng Bootstrap để ẩn alert (kích hoạt hiệu ứng fade)
        const alert = bootstrap.Alert.getOrCreateInstance(toast);
        alert.close();
      }, 3000); // 3000ms = 3 giây
    }
  });

</script>

<?php 
include "view/footer-admin.php";
?>