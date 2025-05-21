<?php
$conn = mysqli_connect("localhost", "root", "", "teav_shop1");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$type = isset($_GET['type']) ? $_GET['type'] : '';

header('Content-Type: text/csv; charset=utf-8');

$output = fopen('php://output', 'w');
fwrite($output, "\xEF\xBB\xBF");

if ($type === 'order') {
    header('Content-Disposition: attachment; filename=Danh-sach-don-hang.csv');
    fputcsv($output, ['Mã đơn', 'Khách hàng', 'Email', 'Sản phẩm', 'Ngày đặt', 'Tổng tiền', 'Thanh toán', 'Trạng thái']);

    $sql = "
        SELECT 
            o.OrderId,
            o.OrderDate,
            o.TotalAmount,
            o.PaymentId,
            o.StatusOrder,
            a.FullName AS CustomerName,
            a.Email AS CustomerEmail,
            GROUP_CONCAT(CONCAT(p.Name, ' (x', op.Quantity, ')') SEPARATOR '; ') AS Products
        FROM Orders o
        LEFT JOIN OrderProduct op ON o.OrderId = op.OrderId
        LEFT JOIN Product p ON op.ProductId = p.ProductId
        LEFT JOIN Account a ON a.Email = o.Email
        GROUP BY o.OrderId
        ORDER BY o.OrderDate DESC
    ";

    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $formattedDate = date('d-m-Y H:i:s', strtotime($row['OrderDate']));
            fputcsv($output, [
                $row['OrderId'],
                $row['CustomerName'],
                $row['CustomerEmail'],
                $row['Products'],
                $formattedDate,
                number_format($row['TotalAmount'], 0) . ' VND',
                $row['PaymentId'] ?: 'Chưa có',
                $row['StatusOrder']
            ]);
        }
    }

} elseif ($type === 'import') {
    header('Content-Disposition: attachment; filename=Danh-sach-phieu-nhap.csv');
    fputcsv($output, ['Mã phiếu nhập', 'Ngày nhập', 'Tên sản phẩm', 'Số lượng', 'Giá nhập', 'Thành tiền']);

    $sql = "
        SELECT 
            i.ImportId,
            i.ImportDate,
            p.Name AS ProductName,
            ip.Quantity,
            ip.UnitPrice,
            (ip.Quantity * ip.UnitPrice) AS SubTotal
        FROM Import i
        JOIN ImportProduct ip ON i.ImportId = ip.ImportId
        JOIN Product p ON p.ProductId = ip.ProductId
        ORDER BY i.ImportDate DESC
    ";

    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $formattedDate = date('d-m-Y H:i:s', strtotime($row['ImportDate']));
            fputcsv($output, [
                $row['ImportId'],
                $formattedDate,
                $row['ProductName'],
                $row['Quantity'],
                number_format($row['UnitPrice'], 0) . ' VND',
                number_format($row['SubTotal'], 0) . ' VND'
            ]);
        }
    }

} else {
    // Nếu không truyền hoặc truyền sai type
    fclose($output);
    echo "Thiếu hoặc sai tham số ?type=order hoặc ?type=import";
    exit;
}

fclose($output);
exit;
?>
