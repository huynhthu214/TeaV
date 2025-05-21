<?php
session_start();
$namePage = "Chi tiết bài viết";
include "view/header-admin.php";

$dsn = 'mysql:host=localhost;dbname=teav_shop1;charset=utf8';
$username = 'root';
$password = '';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    // Kiểm tra BlogId
    if (!isset($_GET['id'])) {
        echo "<div class='alert alert-danger'>Không tìm thấy bài viết.</div>";
        include "view/footer-admin.php";
        exit;
    }

    $blogId = $_GET['id'];

    // Lấy thông tin bài viết
    $sqlBlog = "SELECT * FROM Blog WHERE BlogId = ?";
    $stmt = $pdo->prepare($sqlBlog);
    $stmt->execute([$blogId]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$blog) {
        echo "<div class='alert alert-warning'>Không tìm thấy nội dung bài viết.</div>";
        include "view/footer-admin.php";
        exit;
    }

    // Lấy tag liên quan
    $sqlTags = "
        SELECT T.Name
        FROM BlogTag BT
        JOIN Tag T ON BT.TagId = T.TagId
        WHERE BT.BlogId = ?
    ";
    $stmtTag = $pdo->prepare($sqlTags);
    $stmtTag->execute([$blogId]);
    $tags = $stmtTag->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>

<div class="content-wrapper">
  <h2 class="mb-4" style="color:rgb(10, 119, 52);"><strong>Chi tiết bài viết</strong></h2>

  <div class="card mb-4 p-3">
    <div class="row">
      <div class="col-md-4 text-center">
        <img src="<?= htmlspecialchars($blog['ImgLink']) ?>" alt="Ảnh blog" class="img-fluid rounded shadow" style="max-height: 250px;">
      </div>
      <div class="col-md-8">
        <h4><?= htmlspecialchars($blog['Title']) ?></h4>
        <div><strong>Mã bài viết:</strong> <?= htmlspecialchars($blog['BlogId']) ?></div>
        <div><strong>Người đăng (email):</strong> <?= htmlspecialchars($blog['Email']) ?></div>
        <div><strong>Ngày đăng:</strong> <?= date("d/m/Y H:i", strtotime($blog['DateUpload'])) ?></div>
        <div><strong>Hiển thị:</strong> 
          <span class="badge bg-<?= $blog['IsShow'] === 'Yes' ? 'success' : 'secondary'; ?>">
            <?= $blog['IsShow'] === 'Yes' ? 'Có' : 'Không' ?>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4 p-3">
    <h5 class="mb-3">Tóm tắt</h5>
    <p><?= nl2br(htmlspecialchars($blog['Summary'])) ?></p>
  </div>

  <div class="card mb-4 p-3">
    <h5 class="mb-3">Nội dung chi tiết</h5>
    <div class="blog-full-content">
        <?= $blog['Content'] ?>
    </div>
  </div>

  <div class="card p-3 mb-4">
    <h5 class="mb-3">Từ khóa (Tags)</h5>
    <?php if (!empty($tags)): ?>
      <?php foreach ($tags as $tag): ?>
        <span class="badge bg-info text-dark me-1"><?= htmlspecialchars($tag['Name']) ?></span>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">Không có từ khóa nào.</p>
    <?php endif; ?>
  </div>

  <a href="blog-admin.php" class="btn btn-secondary mb-3">Quay lại</a>
</div>

<?php include "view/footer-admin.php"; ?>
