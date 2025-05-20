<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Kiểm tra dữ liệu gửi đến
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_import'])) {
    $importId = mysqli_real_escape_string($conn, $_POST['ImportId']);
    $note = mysqli_real_escape_string($conn, $_POST['Note'] ?? '');

    // Cập nhật ghi chú trong bảng Import
    $sqlUpdateImport = "UPDATE Import SET Note = '$note' WHERE ImportId = '$importId'";
    $result1 = mysqli_query($conn, $sqlUpdateImport);

    // Duyệt qua từng chi tiết sản phẩm để cập nhật
    $success = true;
    if (isset($_POST['ProductId']) && is_array($_POST['ProductId'])) {
        foreach ($_POST['ProductId'] as $index => $productId) {
            $quantity = (int)$_POST['Quantity'][$index];
            $unitPrice = (float)$_POST['UnitPrice'][$index];

            $sqlUpdateDetail = "UPDATE ImportProduct 
                                SET Quantity = $quantity, UnitPrice = $unitPrice 
                                WHERE ImportId = '$importId' AND ProductId = '$productId'";
            $result2 = mysqli_query($conn, $sqlUpdateDetail);
            if (!$result2) {
                $success = false;
                break;
            }
        }
    }

    if ($result1 && $success) {
        // Chuyển hướng kèm thông báo thành công
        header("Location: import-management.php?update=success");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Cập nhật thất bại: " . mysqli_error($conn) . "</div>";
    }
} else {
    echo "<div class='alert alert-warning'>Dữ liệu không hợp lệ!</div>";
}

mysqli_close($conn);
?>
