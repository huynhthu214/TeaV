<?php 
session_start();
$namePage = "Thêm bài đăng";
include "view/header-admin.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$successMsg = '';
$errorMsg = '';

// Lấy danh sách tác giả (email) từ bảng Account
$authorSql = "SELECT Email, FullName FROM Account WHERE Role = 'Admin' OR Role = 'Author'";
$authorResult = mysqli_query($conn, $authorSql);
$authors = mysqli_fetch_all($authorResult, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_blog'])) {
    // Lấy dữ liệu từ form
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $author = $_POST['author'] ?? '';
    $isShow = $_POST['is_show'] ?? 'No';
    
    // Xử lý upload ảnh nếu có
    $imagePath = '';
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
        // Tạo blogId mới
        $blogId = uniqid('blog_');
        $currentDate = date('Y-m-d H:i:s');
        
        // Thêm bài viết vào database
        $insertSql = "INSERT INTO Blog (BlogId, Title, Content, Email, DateUpload, Image, IsShow) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertSql);
        mysqli_stmt_bind_param($stmt, "sssssss", $blogId, $title, $content, $author, $currentDate, $imagePath, $isShow);
        
        if (mysqli_stmt_execute($stmt)) {
            $successMsg = 'Thêm bài viết thành công!';
            // Reset form
            $title = '';
            $content = '';
            $author = '';
            $isShow = 'No';
        } else {
            $errorMsg = 'Đã xảy ra lỗi: ' . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    }
}

?>

<div class="content-wrapper">
    <div class="page-title d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:rgb(10, 119, 52);"><strong>Thêm bài đăng mới</strong></h2>
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
                    <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($title ?? '') ?>">
                </div>
                
                <div class="mb-3">
                    <label for="author" class="form-label">Tác giả <span class="text-danger">*</span></label>
                    <select class="form-select" id="author" name="author" required>
                        <option value="">-- Chọn tác giả --</option>
                        <?php foreach ($authors as $author): ?>
                            <option value="<?= htmlspecialchars($author['Email']) ?>">
                                <?= htmlspecialchars($author['FullName']) ?> (<?= htmlspecialchars($author['Email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh đại diện bài viết</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">Chọn ảnh đại diện cho bài viết (JPG, JPEG, PNG, GIF)</div>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Nội dung bài viết <span class="text-danger">*</span></label>
                    <textarea id="editor" name="content"><?= htmlspecialchars($content ?? '') ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label d-block">Trạng thái hiển thị</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_show" id="isShow_yes" value="Yes" <?= (($isShow ?? '') === 'Yes') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isShow_yes">Hiển thị</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_show" id="isShow_no" value="No" <?= (($isShow ?? 'No') === 'No') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isShow_no">Ẩn</label>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise me-1"></i>Làm mới
                    </button>
                    <button type="submit" name="submit_blog" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Lưu bài viết
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
        filebrowserUploadUrl: 'upload.php', // URL xử lý upload file trong CKEditor (cần tạo thêm)
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