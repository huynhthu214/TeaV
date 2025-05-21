<?php
// blog-edit.php

// Kết nối CSDL
$pdo = new PDO('mysql:host=localhost;dbname=teav_shop1;charset=utf8', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

if (!isset($_GET['blogid'])) {
    echo "Không tìm thấy bài viết.";
    exit;
}

$blogId = $_GET['blogid'];

// Lấy thông tin bài viết
$stmt = $pdo->prepare("SELECT * FROM Blog WHERE BlogId = ?");
$stmt->execute([$blogId]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    echo "Bài viết không tồn tại.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $summary = $_POST['summary'] ?? '';
    $content = $_POST['content'] ?? '';
    $email = $_POST['email'] ?? '';
    $isshow = $_POST['isshow'] ?? 'No';

    $sql = "UPDATE Blog SET Title = ?, Summary = ?, Content = ?, Email = ?, IsShow = ? WHERE BlogId = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $summary, $content, $email, $isshow, $blogId]);

    echo '
    <div class="alert alert-success mt-4" role="alert">
    <h4 class="alert-heading">Thành công!</h4>
    <p>Bài viết đã được cập nhật thành công.</p>
    <hr>
    <a href="blog-admin.php" class="btn btn-primary">Quay về danh sách</a>
    </div>
    ';
    exit;
}
?>

<?php include "view/header-admin.php"; ?>

<div class="content-wrapper">
  <h2 class="mb-4" style="color:rgb(10, 119, 52);"><strong>Chỉnh sửa bài viết</strong></h2>

  <form action="" method="POST">
    <div class="mb-3">
      <label for="title" class="form-label">Tiêu đề bài viết</label>
      <input type="text" name="title" id="title" class="form-control" required value="<?= htmlspecialchars($blog['Title']) ?>">
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Người đăng (email)</label>
      <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($blog['Email']) ?>">
    </div>

    <div class="mb-3">
      <label for="isshow" class="form-label">Hiển thị</label>
      <select name="isshow" id="isshow" class="form-select">
        <option value="Yes" <?= $blog['IsShow'] === 'Yes' ? 'selected' : '' ?>>Có</option>
        <option value="No" <?= $blog['IsShow'] === 'No' ? 'selected' : '' ?>>Không</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="summary" class="form-label">Tóm tắt bài viết</label>
      <textarea name="summary" id="summary" class="form-control" rows="3" required><?= htmlspecialchars($blog['Summary']) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="content" class="form-label">Nội dung chi tiết</label>
      <textarea name="content" id="content" class="form-control" rows="10" required><?= htmlspecialchars($blog['Content']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">Lưu thay đổi</button>
    <a href="blog-admin.php" class="btn btn-secondary">Hủy</a>
  </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
  .create( document.querySelector( '#content' ) )
  .catch( error => {
      console.error( error );
  } );
</script>

<?php include "view/footer-admin.php"; ?>
