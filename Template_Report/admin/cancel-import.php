<?php
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
$importId = $_GET['id'] ?? '';

if ($importId) {
    $sql = "UPDATE Import SET IsCanceled = 1 WHERE ImportId = '$importId'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Đã hủy phiếu nhập thành công!'); location.href='import-manage.php';</script>";
    } else {
        echo "<script>alert('Hủy thất bại!'); history.back();</script>";
    }
}
?>