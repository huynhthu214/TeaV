<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<pre>';
    print_r($_POST);
    print_r($_FILES);
    echo '</pre>';
    exit; // tạm dừng để xem dữ liệu gửi lên
}
?>
<?php
session_start();
$namePage = "Soạn bài viết";
include "view/header-admin.php";
$email = $_POST['email'] ?? '';
?>

<!-- Nhúng CKEditor 5 Classic từ CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<div class="content-wrapper">
  <h2 class="mb-4" style="color:rgb(10, 119, 52);"><strong>Soạn bài viết mới</strong></h2>

  <form action="save-blog.php" method="POST" enctype="multipart/form-data">
    <div class="card mb-4 p-3">
      <div class="row">
        <div class="col-md-4 text-center">
          <label for="img" class="form-label">Ảnh đại diện</label>
          <input type="file" name="img" id="img" class="form-control" accept="image/*">
        </div>
        <div class="col-md-8">
          <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề bài viết</label>
            <input type="text" name="title" id="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Người đăng (email)</label>
            <input type="email" id="email" class="form-control" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" disabled>
            <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label for="isshow" class="form-label">Hiển thị</label>
            <select name="isshow" id="isshow" class="form-select">
              <option value="Yes">Có</option>
              <option value="No">Không</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4 p-3">
      <label for="summary" class="form-label">Tóm tắt bài viết</label>
      <textarea name="summary" id="summary" class="form-control" rows="3" required></textarea>
    </div>

    <div class="card mb-4 p-3">
      <label for="content" class="form-label">Nội dung chi tiết</label>
      <textarea name="content" id="content" class="form-control" rows="10" required></textarea>
    </div>

    <div class="card mb-4 p-3">
      <label for="tags" class="form-label">Từ khóa (phân cách bằng dấu phẩy)</label>
      <input type="text" name="tags" id="tags" class="form-control" placeholder="trà, truyền thống, sức khỏe">
    </div>

    <button type="submit" class="btn btn-success mb-4">Lưu bài viết</button>

    <a href="blog-admin.php" class="btn btn-secondary mb-4">Hủy</a>
    
  </form>
</div>

<script>
  ClassicEditor
    .create(document.querySelector('#content'), {
      toolbar: [
        'heading', '|',
        'bold', 'italic', 'underline', 'strikethrough', '|',
        'bulletedList', 'numberedList', '|',
        'link', 'blockQuote', 'insertTable', 'undo', 'redo'
      ]
    })
    .then(editor => {
      const form = document.querySelector('form');
      form.addEventListener('submit', () => {
        editor.updateSourceElement(); // Cập nhật lại <textarea> thật sự trước khi submit
      });
    })
    .catch(error => {
      console.error(error);
    });
</script>


<?php include "view/footer-admin.php"; ?>
