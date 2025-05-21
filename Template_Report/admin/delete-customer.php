<?php
session_start();
$namePage = "Xóa khách hàng";
include "view/header-admin.php";

$conn = new mysqli("localhost", "root", "", "teav_shop1");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra có danh sách khách hàng được chọn để xóa
    if (!empty($_POST['selected_customers']) && is_array($_POST['selected_customers'])) {
        // Lọc sạch email, tránh SQL Injection
        $emails = array_map(function($email) use ($conn) {
            return "'" . $conn->real_escape_string($email) . "'";
        }, $_POST['selected_customers']);

        $emails_list = implode(',', $emails);

        // Xóa khách hàng trong bảng Account (nếu có liên quan bảng khác thì cần xử lý thêm)
        $sql = "DELETE FROM Account WHERE Email IN ($emails_list)";

        if ($conn->query($sql)) {
            $_SESSION['message'] = count($emails) . " khách hàng đã được xóa thành công.";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa khách hàng: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Vui lòng chọn khách hàng cần xóa.";
    }
} else {
    $_SESSION['error'] = "Phương thức yêu cầu không hợp lệ.";
}

// Chuyển hướng về trang quản lý khách hàng
header("Location: customer-admin.php");
exit;
?>
