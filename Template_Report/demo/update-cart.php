<?php
// update-cart.php - Cải thiện phiên bản file xử lý AJAX cập nhật giỏ hàng
session_start();

header('Content-Type: application/json'); // Đảm bảo đúng Content-Type

// Kiểm tra nếu cart tồn tại
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Giỏ hàng trống']);
    exit;
}

// Lấy các tham số
$action = isset($_GET['action']) ? $_GET['action'] : '';
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (empty($action) || empty($productId)) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu tham số']);
    exit;
}

$found = false;
$itemQuantity = 0;

// Cập nhật giỏ hàng dựa trên hành động
switch ($action) {
    case 'increase':
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] += 1;
                $itemQuantity = $item['quantity'];
                $found = true;
                break;
            }
        }
        break;
        
    case 'decrease':
        foreach ($_SESSION['cart'] as $key => &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] -= 1;
                $found = true;
                
                // Nếu số lượng = 0, xóa sản phẩm
                if ($item['quantity'] <= 0) {
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['cart'] = array_values($_SESSION['cart']); // Đánh lại chỉ số mảng
                    echo json_encode([
                        'status' => 'deleted', 
                        'message' => 'Sản phẩm đã bị xóa',
                        'productId' => $productId
                    ]);
                    exit;
                }
                
                $itemQuantity = $item['quantity'];
                break;
            }
        }
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ']);
        exit;
}

if (!$found) {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy sản phẩm']);
    exit;
}

// Tính toán tổng giá trị giỏ hàng mới
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $price = floatval(str_replace(',', '', $item['price']));
    $total += $price * $item['quantity'];
}

// Trả về kết quả
echo json_encode([
    'status' => 'success',
    'message' => 'Cập nhật thành công',
    'quantity' => $itemQuantity,
    'productId' => $productId,
    'total' => number_format($total, 2)
]);
?>