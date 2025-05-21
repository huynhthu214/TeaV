<?php
$conn = new mysqli("localhost", "root", "", "teav_shop1");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

// Lấy và xử lý tham số tìm kiếm nếu có
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchSQL = "";
if ($searchQuery !== '') {
    $q = $conn->real_escape_string($searchQuery);
    $searchSQL = "WHERE a.FullName LIKE '%$q%' OR a.Email LIKE '%$q%' OR a.PhoneNumber LIKE '%$q%'";
}

// Thiết lập header cho file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=danh-sach-khach-hang-' . date('Y-m-d') . '.csv');

// Mở stream ghi
$output = fopen('php://output', 'w');

// Ghi BOM cho UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Ghi tiêu đề cột
fputcsv($output, [
    'Họ tên',
    'Email',
    'Số điện thoại',
    'Ngày đăng ký',
    'Tổng đơn hàng',
    'Tổng chi tiêu (VND)',
    'Trạng thái'
]);

// Truy vấn dữ liệu
$sql = "
SELECT 
    a.Email, a.FullName, a.PhoneNumber, a.IsActive, a.CreatedDate,
    COUNT(o.OrderId) AS TotalOrders,
    COALESCE(SUM(o.TotalAmount), 0) AS TotalSpent
FROM Account a
LEFT JOIN Orders o ON a.Email = o.Email
$searchSQL
GROUP BY a.Email, a.FullName, a.PhoneNumber, a.IsActive, a.CreatedDate
ORDER BY a.CreatedDate DESC
";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['FullName'],
            $row['Email'],
            $row['PhoneNumber'],
            date("d/m/Y", strtotime($row['CreatedDate'])),
            $row['TotalOrders'],
            number_format($row['TotalSpent'], 3, '.', ','),
            $row['IsActive'] === 'Yes' ? 'Hoạt động' : 'Ngừng hoạt động'
        ]);
    }
}

fclose($output);
exit;
