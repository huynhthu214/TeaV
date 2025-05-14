<?php 
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=bao-cao-thong-ke.csv');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['STT', 'Tên', 'Email', 'Doanh thu', 'Ngày đăng ký']);
    $data = [
    [1, 'Nguyễn Văn A', 'a@example.com', 40000, '2024-01-05'],
    [2, 'Trần Thị B', 'b@example.com', 215000, '2024-03-12'],
    [3, 'Lê Văn C', 'c@example.com', 18000, '2024-04-01']
];

// Ghi từng dòng dữ liệu vào file CSV
foreach ($data as $row) {
    fputcsv($output, $row);
}

// Đóng file
fclose($output);
exit;
?>
