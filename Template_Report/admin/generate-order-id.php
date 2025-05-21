<?php
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$prefix = "ORD";
$newId = "ORD001"; // mặc định nếu không có đơn hàng nào

// Lấy mã đơn hàng mới nhất theo thứ tự giảm dần
$sql = "SELECT OrderId FROM Orders WHERE OrderId LIKE '{$prefix}%' ORDER BY OrderId DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    $lastId = $row['OrderId'];         // VD: ORD012
    $number = (int)substr($lastId, strlen($prefix)); // lấy phần số: 12
    $number++;                         // tăng lên: 13
    $newId = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT); // ORD013
}

echo $newId;
?>