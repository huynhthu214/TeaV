<?php
if (isset($_GET['ids'])) {
    $ids = explode(",", $_GET['ids']);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Hiện lỗi chi tiết

    $conn = mysqli_connect("localhost", "root", "", "teav_shop1");
    if (!$conn) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }

    foreach ($ids as $id) {
        $id = mysqli_real_escape_string($conn, $id);

    mysqli_query($conn, "UPDATE Orders SET StatusOrder = 'Đã hủy' WHERE OrderId = '$id'");

    }

    mysqli_close($conn);
    header("Location: order-admin.php"); // Thay bằng tên đúng của bạn
    exit;
} else {
    header("Location: order-admin.php");
    exit;
}
?>
