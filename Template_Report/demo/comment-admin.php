<?php
session_start();
$namePage = "Quản lý bài đăng";
include "view/header-admin.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$blogid = $_GET['blogid'] ?? '';
if ($blogid === '') {
    die("Thiếu blogid");
}

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

// Lấy bình luận
$sqlComments = "
    SELECT r.Comment, r.IsShow, a.FullName
    FROM Reaction r
    LEFT JOIN Account a ON r.Email = a.Email
    WHERE r.BlogId = ?
    ORDER BY r.ReactionId DESC
";
$stmt = mysqli_prepare($conn, $sqlComments);
mysqli_stmt_bind_param($stmt, "s", $blogid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="content-wrapper">
    <div class="page-title d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 style="color:rgb(10, 119, 52); margin-top:-10px;"><strong>Bình luận</strong></h2>
            <p class="text-muted mb-0">Bài viết: <strong><?= htmlspecialchars($title) ?></strong></p>
        </div>
        <a href="blog-admin.php" class="btn btn-secondary mb-3">Quay lại danh sách bài đăng</a>
    </div>

    <?php if (mysqli_num_rows($result) === 0): ?>
        <p class="text-muted">Chưa có bình luận nào.</p>
    <?php else: ?>
        <ul class="list-group">
        <?php while($comment = mysqli_fetch_assoc($result)): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($comment['FullName'] ?: 'Khách') ?></strong>
                <span class="badge bg-<?= ($comment['IsShow'] === 'Yes') ? 'success' : 'secondary' ?>">
                    <?= ($comment['IsShow'] === 'Yes') ? 'Hiển thị' : 'Ẩn' ?>
                </span>
                <p class="mb-0 mt-2"><?= nl2br(htmlspecialchars($comment['Comment'])) ?></p>
            </li>
        <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</div>

<?php 
include "view/footer-admin.php";
?>
