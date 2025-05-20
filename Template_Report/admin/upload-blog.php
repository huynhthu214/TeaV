<?php
// upload.php - Xử lý upload hình ảnh từ CKEditor

// Kết nối database nếu cần
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Kiểm tra nếu có file được upload
if (isset($_FILES['upload']) && $_FILES['upload']['error'] === 0) {
    // Thư mục lưu trữ file
    $uploadDir = 'uploads/ckeditor/';
    
    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Tạo tên file mới để tránh trùng lặp
    $fileExtension = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
    $uniqueFileName = uniqid('upload_') . '.' . $fileExtension;
    $targetFile = $uploadDir . $uniqueFileName;
    
    // Kiểm tra loại file
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'txt'];
    
    if (in_array(strtolower($fileExtension), $allowedTypes)) {
        // Upload file
        if (move_uploaded_file($_FILES['upload']['tmp_name'], $targetFile)) {
            // File URL để trả về cho CKEditor
            $fileUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . 
                       dirname($_SERVER['REQUEST_URI']) . '/' . $targetFile;
            
            // Ghi log upload nếu cần
            $logSql = "INSERT INTO UploadLog (FileName, FilePath, UploadDate, UserEmail) 
                      VALUES (?, ?, NOW(), ?)";
            $userEmail = $_SESSION['email'] ?? 'guest';
            $stmt = mysqli_prepare($conn, $logSql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $_FILES['upload']['name'], $targetFile, $userEmail);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            
            // Trả về thông tin file cho CKEditor
            $response = [
                'uploaded' => 1,
                'fileName' => $uniqueFileName,
                'url' => $fileUrl
            ];
            
            echo json_encode($response);
        } else {
            // Lỗi khi upload
            $response = [
                'uploaded' => 0,
                'error' => [
                    'message' => 'Không thể upload file. Vui lòng thử lại!'
                ]
            ];
            
            echo json_encode($response);
        }
    } else {
        // Loại file không được phép
        $response = [
            'uploaded' => 0,
            'error' => [
                'message' => 'Loại file không được phép. Chỉ chấp nhận: ' . implode(', ', $allowedTypes)
            ]
        ];
        
        echo json_encode($response);
    }
} else {
    // Không có file được upload
    $response = [
        'uploaded' => 0,
        'error' => [
            'message' => 'Không nhận được file nào'
        ]
    ];
    
    echo json_encode($response);
}

// Đóng kết nối
mysqli_close($conn);