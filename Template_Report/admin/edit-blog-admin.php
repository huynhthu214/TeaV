<?php 
session_start();
$namePage = "Sửa bài đăng";
include "view/header-admin.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$successMsg = '';
$errorMsg = '';
$blogId = isset($_GET['blogid']) ? trim($_GET['blogid']) : '';

if (empty($blogId)) {
    header("Location: blog-admin.php");
    exit;
}

// Lấy danh sách tác giả (email) từ bảng Account
$authorSql = "SELECT Email, FullName FROM Account WHERE Role = 'Admin' OR Role = 'Author'";
$authorResult = mysqli_query($conn, $authorSql);
$authors = mysqli_fetch_all($authorResult, MYSQLI_ASSOC);

// Lấy thông tin bài viết
$blogSql = "SELECT * FROM Blog WHERE BlogId = ?";
$stmt = mysqli_prepare($conn, $blogSql);
mysqli_stmt_bind_param($stmt, "s", $blogId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header("Location: blog-admin.php");
    exit;
}

$blog = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Xử lý cập nhật bài viết
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_blog'])) {
    // Lấy dữ liệu từ form
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $author = $_POST['author'] ?? '';
    $isShow = $_POST['is_show'] ?? 'No';
    
    // Xử lý upload ảnh mới nếu có
    $imagePath = $blog['Image']; // Giữ nguyên ảnh cũ nếu không upload ảnh mới
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/blog/';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid('blog_') . '.' . $fileExtension;
        $targetFile = $uploadDir . $uniqueName;
        
        // Kiểm tra loại file
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileExtension), $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Xóa ảnh cũ nếu có
                if (!empty($blog['Image']) && file_exists($blog['Image'])) {
                    unlink($blog['Image']);
                }
                $imagePath = $targetFile;
            } else {
                $errorMsg = 'Không thể upload ảnh. Vui lòng thử lại!';
            }
        } else {
            $errorMsg = 'Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF)';
        }
    }
    
    // Kiểm tra dữ liệu
    if (empty($title)) {
        $errorMsg = 'Vui lòng nhập tiêu đề bài viết';
    } elseif (empty($content)) {
        $errorMsg = 'Vui lòng nhập nội dung bài viết';
    } elseif (empty($author)) {
        $errorMsg = 'Vui lòng chọn tác giả';
    } else {
        // Cập nhật bài viết trong database
        $updateSql = "UPDATE Blog SET Title = ?, Content = ?, Email = ?, Image = ?, IsShow = ? WHERE BlogId = ?";
        $stmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($stmt, "ssssss", $title, $content, $author, $imagePath, $isShow, $blogId);
        
        if (mysqli_stmt_execute($stmt)) {
            $successMsg = 'Cập nhật bài viết thành công!';
            // Cập nhật lại thông tin bài viết
            $blog['Title'] = $title;
            $blog['Content'] = $content;
            $blog['Email'] = $author;
            $blog['Image'] = $imagePath;
            $blog['IsShow'] = $isShow;
        } else {
            $errorMsg = 'Đã xảy ra lỗi: ' . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    }
}

?>

<div class="content-wrapper">
    <div class="page-title d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:rgb(10, 119, 52);"><strong>Sửa bài đăng</strong></h2>
        <a href="blog-admin.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
    
    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $successMsg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $errorMsg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề bài viết <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($blog['Title']) ?>">
                </div>
                
                <div class="mb-3">
                    <label for="author" class="form-label">Tác giả <span class="text-danger">*</span></label>
                    <select class="form-select" id="author" name="author" required>
                        <option value="">-- Chọn tác giả --</option>
                        <?php foreach ($authors as $author): ?>
                            <option value="<?= htmlspecialchars($author['Email']) ?>" <?= ($blog['Email'] === $author['Email']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($author['FullName']) ?> (<?= htmlspecialchars($author['Email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh đại diện bài viết</label>
                    <?php if (!empty($blog['Image'])): ?>
                        <div class="mb-2">
                            <img src="<?= htmlspecialchars($blog['Image']) ?>" alt="Ảnh hiện tại" class="img-thumbnail" style="max-height: 150px;">
                            <p class="form-text">Ảnh hiện tại. Upload ảnh mới để thay thế.</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">Chọn ảnh đại diện cho bài viết (JPG, JPEG, PNG, GIF)</div>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Nội dung bài viết <span class="text-danger">*</span></label>
                    <textarea id="editor" name="content"><?= htmlspecialchars($blog['Content']) ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label d-block">Trạng thái hiển thị</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_show" id="isShow_yes" value="Yes" <?= ($blog['IsShow'] === 'Yes') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isShow_yes">Hiển thị</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_show" id="isShow_no" value="No" <?= ($blog['IsShow'] === 'No') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isShow_no">Ẩn</label>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="blog-admin.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Hủy
                    </a>
                    <button type="submit" name="update_blog" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Cập nhật bài viết
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Nhúng CKEditor từ CDN -->
<script src="https://cdn.ckeditor.com/4.16.2/standard-all/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    CKEDITOR.replace('editor', {
        height: 400,
        filebrowserUploadUrl: 'upload.php', // URL xử lý upload file trong CKEditor
        toolbarGroups: [
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
            { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
            { name: 'forms', groups: [ 'forms' ] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
            { name: 'links', groups: [ 'links' ] },
            { name: 'insert', groups: [ 'insert' ] },
            { name: 'styles', groups: [ 'styles' ] },
            { name: 'colors', groups: [ 'colors' ] },
            { name: 'tools', groups: [ 'tools' ] },
            { name: 'others', groups: [ 'others' ] },
            { name: 'about', groups: [ 'about' ] }
        ],
        removeButtons: 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Strike,Subscript,Superscript,CopyFormatting,RemoveFormat,Blockquote,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Language,Anchor,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Maximize,ShowBlocks,About',
        extraPlugins: 'image2,uploadimage,uploadfile,filebrowser',
        language: 'vi'
    });
});
</script>

<?php 
include "view/footer-admin.php";
?>