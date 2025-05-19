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

<div class="content-wrapper">
<div class="page-title d-flex justify-content-between align-items-start mb-4">
  <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong> Quản lý bài đăng</strong></h2>
  
  <form class="d-flex align-items-center gap-2 mt-4" method="GET" action="">
    <input class="form-control" type="search" placeholder="Tìm theo tiêu đề hoặc người viết..." name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    
    <select class="form-select" name="isshow">
      <option value=""> Tất cả trạng thái </option>
      <option value="Yes" <?= (($_GET['isshow'] ?? '') === 'Yes') ? 'selected' : '' ?>>Hiển thị</option>
      <option value="No" <?= (($_GET['isshow'] ?? '') === 'No') ? 'selected' : '' ?>>Ẩn</option>
    </select>

    <button class="btn btn-outline-success" type="submit">
      <i class="bi bi-search"></i>
    </button>
  </form>
</div>

<div class="table-responsive">
  <table class="table table-striped table-bordered align-middle">
    <thead class="table-success text-center">
      <tr>
        <th>#</th>
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
            <td><?= $index + 1 ?></td>
            <td class="text-start"><?= htmlspecialchars($blog['Title']) ?></td>
            <td><?= htmlspecialchars($blog['Author']) ?></td>
            <td><?= date('d/m/Y', strtotime($blog['DateUpload'])) ?></td>
            <td><?= $blog['CommentCount'] ?></td>
            <td>
              <span class="badge bg-<?= $blog['IsShow'] === 'Yes' ? 'success' : 'secondary' ?>">
                <?= $blog['IsShow'] === 'Yes' ? 'Hiển thị' : 'Ẩn' ?>
              </span>
            </td>
            <td>
              <a href="comment-admin.php?blogid=<?= urlencode($blog['BlogId']) ?>" class="btn btn-sm btn-warning">Xem bình luận</a>
              <a href="edit_blog.php?blogid=<?= urlencode($blog['BlogId']) ?>" class="btn btn-sm btn-primary">Sửa</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" class="text-center text-muted">Không có bài đăng nào phù hợp</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php 
include "view/footer-admin.php";
?>
