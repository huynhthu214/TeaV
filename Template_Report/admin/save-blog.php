<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $title = $_POST['title'] ?? '';
    $summary = $_POST['summary'] ?? '';
    $content = $_POST['content'] ?? '';
    $email = $_POST['email'] ?? '';
    $isshow = $_POST['isshow'] ?? 'No';
    $tagsInput = $_POST['tags'] ?? '';

    // Kết nối CSDL
    $pdo = new PDO('mysql:host=localhost;dbname=teav_shop1;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Tạo BlogId
    $blogId = uniqid('BLG');

    // Xử lý ảnh đại diện
    $imgLink = '';
    if (!empty($_FILES['img']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = $blogId . '_' . basename($_FILES['img']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['img']['tmp_name'], $targetPath)) {
            $imgLink = $targetPath;
        }
    }

    // Chèn dữ liệu vào bảng Blog
    $sql = "INSERT INTO Blog (BlogId, Title, Summary, Content, DateUpload, Email, IsShow, ImgLink)
            VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$blogId, $title, $summary, $content, $email, $isshow, $imgLink]);

    // Xử lý từ khóa
    $tags = array_filter(array_map('trim', explode(',', $tagsInput)));

    foreach ($tags as $tagName) {
        // Kiểm tra nếu tag đã tồn tại
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

        // Chèn vào BlogTag
        $stmt = $pdo->prepare("INSERT INTO BlogTag (BlogId, TagId) VALUES (?, ?)");
        $stmt->execute([$blogId, $tagId]);
    }

    echo "<div style='padding:20px; font-family:Arial'>
            <h3 style='color:green;'> Bài viết đã được lưu thành công.</h3>
            <a href='blog-editor.php' class='btn btn-primary'>Viết tiếp bài khác</a>
            <a href='blog-admin.php' class='btn btn-secondary' style='margin-left:10px;'>Về trang quản lý</a>
          </div>";
}
?>
