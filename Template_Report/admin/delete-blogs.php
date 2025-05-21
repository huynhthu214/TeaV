<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blog_ids'])) {
    $blogIds = $_POST['blog_ids'];

    if (!empty($blogIds)) {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=teav_shop1;charset=utf8', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Xóa bình luận liên quan trước (nếu có bảng Reaction liên kết BlogId)
            $inClause = implode(',', array_fill(0, count($blogIds), '?'));
            $stmt = $pdo->prepare("DELETE FROM Reaction WHERE BlogId IN ($inClause)");
            $stmt->execute($blogIds);

            // Xóa bài viết
            $stmt = $pdo->prepare("DELETE FROM Blog WHERE BlogId IN ($inClause)");
            $stmt->execute($blogIds);

            header("Location: blog-admin.php?deleted=success");
            exit();
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    } else {
        echo "Không có bài viết nào được chọn.";
    }
} else {
    header("Location: blog-admin.php");
    exit();
}
?>
