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

// Xử lý xóa bài đăng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_posts'])) {
    if (isset($_POST['selected_posts']) && is_array($_POST['selected_posts'])) {
        foreach ($_POST['selected_posts'] as $blogId) {
            // Xóa các bình luận liên quan trước
            $deleteReactionsSql = "DELETE FROM Reaction WHERE BlogId = ?";
            $stmt = mysqli_prepare($conn, $deleteReactionsSql);
            mysqli_stmt_bind_param($stmt, "s", $blogId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            // Sau đó xóa bài đăng
            $deleteBlogSql = "DELETE FROM Blog WHERE BlogId = ?";
            $stmt = mysqli_prepare($conn, $deleteBlogSql);
            mysqli_stmt_bind_param($stmt, "s", $blogId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        // Chuyển hướng lại trang sau khi xóa
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

?>

<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-center mb-4">
  <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong> Quản lý bài đăng</strong></h2>
</div>

<form class="d-flex align-items-center gap-2 mb-4" method="GET" action="">
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

  <div class="col-md-auto">
    <a href="blog-editor.php" class="btn btn-success">
      <i class="bi bi-plus-circle"></i>Thêm
    </a>
  </div>
  
  <div class="col-md-auto">
    <button type="button" id="delete-selected" class="btn btn-danger" disabled>
      <i class="bi bi-trash me-1"></i>Xóa
    </button>
  </div>

</form>

<form id="posts-form" method="POST" action="">
  <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-success text-center">
        <tr>
          <th><input type="checkbox" id="select-all" class="form-check-input"></th>
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
              <td><input type="checkbox" name="selected_posts[]" class="form-check-input post-checkbox" value="<?php echo $blog['BlogId']; ?>"></td>
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

  <!-- Modal xác nhận xóa -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Bạn có chắc chắn muốn xóa bài đăng đã chọn không? Hành động này không thể hoàn tác và sẽ xóa cả các bình luận liên quan.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" name="delete_posts" class="btn btn-danger">Xóa</button>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý chọn tất cả
    const selectAllCheckbox = document.getElementById('select-all');
    const postCheckboxes = document.querySelectorAll('.post-checkbox');
    const deleteSelectedBtn = document.getElementById('delete-selected');
    
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        
        postCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        
        updateDeleteButtonState();
    });
    
    // Cập nhật trạng thái của nút xóa dựa trên số lượng checkbox được chọn
    function updateDeleteButtonState() {
        const checkedCount = document.querySelectorAll('.post-checkbox:checked').length;
        deleteSelectedBtn.disabled = checkedCount === 0;
    }
    
    // Thêm sự kiện cho từng checkbox
    postCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButtonState();
            
            // Kiểm tra xem tất cả có được chọn không
            const allChecked = document.querySelectorAll('.post-checkbox:checked').length === postCheckboxes.length;
            selectAllCheckbox.checked = allChecked;
        });
    });
    
    // Xử lý nút xóa đơn lẻ
    const deleteSingleButtons = document.querySelectorAll('.delete-single');
    deleteSingleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Bỏ chọn tất cả các checkbox khác
            postCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Chỉ chọn checkbox tương ứng với bài đăng muốn xóa
            const blogId = this.getAttribute('data-blog-id');
            const correspondingCheckbox = document.querySelector(`.post-checkbox[value="${blogId}"]`);
            if (correspondingCheckbox) {
                correspondingCheckbox.checked = true;
            }
        });
    });
    
    // Khởi tạo trạng thái nút xóa
    updateDeleteButtonState();
});
</script>

<?php 
include "view/footer-admin.php";
?>