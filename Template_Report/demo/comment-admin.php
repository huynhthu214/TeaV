<?php
session_start();
$namePage = "Quản lý bình luận";
include "view/header-admin.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Xử lý ẩn/hiện bình luận
if (isset($_POST['toggle_comment_status']) && isset($_POST['commentid'])) {
    $commentid = $_POST['commentid'];
    $current_status = $_POST['current_status'];
    $new_status = ($current_status === 'Yes') ? 'No' : 'Yes';
    
    // Sử dụng mysqli thay vì PDO để đồng nhất với phần còn lại
    $stmt = mysqli_prepare($conn, "UPDATE Reaction SET IsShow = ? WHERE ReactionId = ?");
    mysqli_stmt_bind_param($stmt, "ss", $new_status, $commentid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Chuyển hướng để tránh gửi lại form khi refresh trang
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['blogid']) ? "?blogid=" . $_GET['blogid'] : ""));
    exit();
}

// Xử lý xóa bình luận
if (isset($_POST['delete_comment']) && isset($_POST['commentid'])) {
    $commentid = $_POST['commentid'];
    
    // Sử dụng mysqli thay vì PDO
    $stmt = mysqli_prepare($conn, "DELETE FROM Reaction WHERE ReactionId = ?");
    mysqli_stmt_bind_param($stmt, "s", $commentid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Chuyển hướng để tránh gửi lại form khi refresh trang
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['blogid']) ? "?blogid=" . $_GET['blogid'] : ""));
    exit();
}

$blogid = $_GET['blogid'] ?? '';

// Nếu có blogid, hiển thị bình luận của bài viết cụ thể
if (!empty($blogid)) {
    // Lấy tiêu đề bài viết
    $sqlBlog = "SELECT Title FROM Blog WHERE BlogId = ?";
    $stmt = mysqli_prepare($conn, $sqlBlog);
    mysqli_stmt_bind_param($stmt, "s", $blogid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $title);
    if (!mysqli_stmt_fetch($stmt)) {
        die("Không tìm thấy bài đăng");
    }
    mysqli_stmt_close($stmt);

    // Lấy bình luận của bài viết cụ thể
    $sqlComments = "
        SELECT r.ReactionId, r.Email, r.Comment, r.IsShow, a.FullName, b.Title as BlogTitle
        FROM Reaction r
        LEFT JOIN Account a ON r.Email = a.Email
        LEFT JOIN Blog b ON r.BlogId = b.BlogId
        WHERE r.BlogId = ?
        ORDER BY r.ReactionId DESC
    ";
    $stmt = mysqli_prepare($conn, $sqlComments);
    mysqli_stmt_bind_param($stmt, "s", $blogid);
} else {
    // Hiển thị tất cả bình luận
    $sqlComments = "
        SELECT r.ReactionId, r.Email, r.BlogId, r.Comment, r.IsShow, a.FullName, b.Title as BlogTitle
        FROM Reaction r
        LEFT JOIN Account a ON r.Email = a.Email
        LEFT JOIN Blog b ON r.BlogId = b.BlogId
        ORDER BY r.ReactionId DESC
    ";
    $stmt = mysqli_prepare($conn, $sqlComments);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="content-wrapper p-4">
    <?php if (!empty($blogid)): ?>
        <div class="page-title d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong>Bình luận</strong></h2>
                <p class="text-muted mb-0">Bài viết: <strong><?= htmlspecialchars($title) ?></strong></p>
            </div>
            <a href="blog-admin.php" class="btn btn-secondary mb-3">Quay lại danh sách bài đăng</a>
        </div>
    <?php else: ?>
        <div class="page-title mb-4">
            <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong>Quản lý tất cả bình luận</strong></h2>
            <p class="text-muted">Quản lý bình luận từ tất cả các bài viết</p>
        </div>
        
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#all-comments">Tất cả</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#pending-comments">Chờ duyệt</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#approved-comments">Đã duyệt</a>
            </li>
        </ul>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) === 0): ?>
        <p class="text-muted">Chưa có bình luận nào.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người bình luận</th>
                        <?php if (empty($blogid)): ?>
                            <th>Bài viết</th>
                        <?php endif; ?>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($comment = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($comment['ReactionId']) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($comment['FullName'] ?: $comment['Email']) ?></strong>
                                <?php if (empty($comment['FullName'])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($comment['Email']) ?></small>
                                <?php endif; ?>
                            </td>
                            <?php if (empty($blogid)): ?>
                                <td>
                                    <a href="comment-admin.php?blogid=<?= $comment['BlogId'] ?>">
                                        <?= htmlspecialchars($comment['BlogTitle']) ?>
                                    </a>
                                </td>
                            <?php endif; ?>
                            <td><?= nl2br(htmlspecialchars($comment['Comment'])) ?></td>
                            <td>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="commentid" value="<?= htmlspecialchars($comment['ReactionId']) ?>">
                                    <input type="hidden" name="current_status" value="<?= $comment['IsShow'] ?>">
                                    <button type="submit" name="toggle_comment_status" class="btn btn-sm <?= $comment['IsShow'] === 'Yes' ? 'btn-outline-primary' : 'btn-outline-secondary' ?>" title="<?= $comment['IsShow'] === 'Yes' ? 'Đang hiển thị' : 'Đang ẩn' ?>">
                                        <i class="fa fa-<?= $comment['IsShow'] === 'Yes' ? 'eye' : 'eye-slash' ?>" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?');">
                                    <input type="hidden" name="commentid" value="<?= htmlspecialchars($comment['ReactionId']) ?>">
                                    <button type="submit" name="delete_comment" class="btn btn-sm btn-danger"> <i class="bi bi-trash me-1"></i> </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
// JavaScript để lọc bình luận theo tab
document.addEventListener('DOMContentLoaded', function() {
    // Các tab chỉ hiển thị khi không có blogid cụ thể
    <?php if (empty($blogid)): ?>
    const allRows = document.querySelectorAll('tbody tr');
    
    document.querySelector('a[href="#all-comments"]').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
        this.classList.add('active');
        allRows.forEach(row => row.style.display = '');
    });
    
    document.querySelector('a[href="#pending-comments"]').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
        this.classList.add('active');
        allRows.forEach(row => {
            // Kiểm tra biểu tượng eye-slash (ẩn)
            const eyeIcon = row.querySelector('button[name="toggle_comment_status"] i');
            row.style.display = (eyeIcon && eyeIcon.classList.contains('fa-eye-slash')) ? '' : 'none';
        });
    });

    document.querySelector('a[href="#approved-comments"]').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
        this.classList.add('active');
        allRows.forEach(row => {
            // Kiểm tra biểu tượng eye (hiển thị)
            const eyeIcon = row.querySelector('button[name="toggle_comment_status"] i');
            row.style.display = (eyeIcon && eyeIcon.classList.contains('fa-eye')) ? '' : 'none';
        });
    });
    <?php endif; ?>
});
</script>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
include "view/footer-admin.php";
?>