<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Chuyển hướng về trang soạn bài nếu truy cập trực tiếp
    header('Location: blog-editor.php');
    exit;
}

// Lấy dữ liệu từ form
$title = $_POST['title'] ?? '';
$summary = $_POST['summary'] ?? '';
$content = $_POST['content'] ?? '';
$email = $_POST['email'] ?? '';
$isshow = $_POST['isshow'] ?? 'No';
$tagsInput = $_POST['tags'] ?? '';

// Validate bắt buộc (cơ bản)
if (!$title || !$summary || !$content || !$email) {
    die('Thiếu dữ liệu bắt buộc.');
}

try {
    // Kết nối DB
    $pdo = new PDO('mysql:host=localhost;dbname=teav_shop1;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Tạo BlogId
    $blogId = uniqid('BLG');

    // Xử lý ảnh đại diện
    $imgLink = '';
    if (!empty($_FILES['img']['name'])) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Lấy phần mở rộng file
        $ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
        // Đặt tên file an toàn
        $fileName = $blogId . '.' . $ext;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['img']['tmp_name'], $targetPath)) {
            // Đường dẫn lưu trong DB (relative)
            $imgLink = 'uploads/' . $fileName;
        } else {
            throw new Exception('Upload ảnh thất bại.');
        }
    }

    // Chèn bài viết vào bảng Blog
    $sql = "INSERT INTO Blog (BlogId, Title, Summary, Content, DateUpload, Email, IsShow, ImgLink) 
            VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$blogId, $title, $summary, $content, $email, $isshow, $imgLink]);

    // Xử lý từ khóa
    $tags = array_filter(array_map('trim', explode(',', $tagsInput)));

    foreach ($tags as $tagName) {
        // Kiểm tra tag đã tồn tại chưa
        $stmt = $pdo->prepare("SELECT TagId FROM Tag WHERE Name = ?");
        $stmt->execute([$tagName]);
        $tag = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tag) {
            $tagId = $tag['TagId'];
        } else {
            // Tạo tag mới
            $tagId = uniqid('TAG');
            $stmt = $pdo->prepare("INSERT INTO Tag (TagId, Name) VALUES (?, ?)");
            $stmt->execute([$tagId, $tagName]);
        }

        // Gán tag vào blog
        $stmt = $pdo->prepare("INSERT INTO BlogTag (BlogId, TagId) VALUES (?, ?)");
        $stmt->execute([$blogId, $tagId]);
    }

    // Hiển thị thông báo thành công
    echo "<div style='padding:20px; font-family: Arial;'>
            <h3 style='color:green;'>Bài viết đã được lưu thành công.</h3>
            <a href='blog-editor.php' class='btn btn-primary'>Viết tiếp bài khác</a>
            <a href='blog-admin.php' class='btn btn-secondary' style='margin-left:10px;'>Về trang quản lý</a>
          </div>";

} catch (Exception $e) {
    // Hiển thị lỗi
    echo "<div style='padding:20px; font-family: Arial;'>
            <h3 style='color:red;'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</h3>
            <a href='blog-editor.php' class='btn btn-secondary'>Quay lại</a>
          </div>";
}
