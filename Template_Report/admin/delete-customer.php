<?php
session_start();

// Kết nối database
$conn = new mysqli("localhost", "root", "", "teav_shop1");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý xóa một khách hàng đơn lẻ qua tham số ID trong URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    
    // Thực hiện câu lệnh xóa
    $sql = "DELETE FROM Account WHERE AccountID = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: customer-admin.php?deleted=success");
        exit;
    } else {
        header("Location: customer-admin.php?deleted=error&message=" . urlencode($conn->error));
        exit;
    }
}

// Xử lý xóa nhiều khách hàng qua form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_customers']) && isset($_POST['selected_emails'])) {
    $emails = $_POST['selected_emails'];
    
    if (!empty($emails)) {
        try {
            // Sử dụng prepared statement cho an toàn
            $placeholders = implode(',', array_fill(0, count($emails), '?'));
            $stmt = $conn->prepare("DELETE FROM Account WHERE Email IN ($placeholders)");
            
            // Bind các email vào câu lệnh SQL
            $types = str_repeat('s', count($emails)); // 's' cho string, một ký tự cho mỗi tham số
            $stmt->bind_param($types, ...$emails);
            
            // Thực thi câu lệnh
            $stmt->execute();
            
            // Kiểm tra kết quả và chuyển hướng
            if ($stmt->affected_rows > 0) {
                header("Location: customer-admin.php?deleted=success");
                exit;
            } else {
                header("Location: customer-admin.php?deleted=error&message=" . urlencode("Không có dữ liệu nào bị xóa"));
                exit;
            }
        } catch (Exception $e) {
            header("Location: customer-admin.php?deleted=error&message=" . urlencode($e->getMessage()));
            exit;
        }
    } else {
        header("Location: customer-admin.php?deleted=error&message=" . urlencode("Không có khách hàng nào được chọn"));
        exit;
    }
}

// Nếu không có yêu cầu xóa hợp lệ, chuyển hướng về trang quản lý khách hàng
header("Location: customer-admin.php");
exit;

$conn->close();
?>