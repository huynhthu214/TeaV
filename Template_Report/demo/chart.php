<?php
// Tạo hình ảnh với kích thước 400x300 px
$image = imagecreatetruecolor(400, 300);

// Thiết lập màu sắc
$backgroundColor = imagecolorallocate($image, 255, 255, 255); // Màu nền trắng
$barColor = imagecolorallocate($image, 0, 102, 204); // Màu cột (xanh dương)
$textColor = imagecolorallocate($image, 0, 0, 0); // Màu chữ (đen)

// Tô nền trắng cho hình ảnh
imagefill($image, 0, 0, $backgroundColor);

// Dữ liệu biểu đồ
$data = [10, 20, 30, 40, 50];

// Vẽ các cột
$barWidth = 50;
$spaceBetweenBars = 20;
for ($i = 0; $i < count($data); $i++) {
    $x1 = 50 + ($i * ($barWidth + $spaceBetweenBars));
    $y1 = 250 - ($data[$i] * 5); // Nhân với 5 để phóng đại dữ liệu
    $x2 = $x1 + $barWidth;
    $y2 = 250;
    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $barColor);
}

// Ghi nhãn
imagestring($image, 5, 150, 270, "Earnings Overview", $textColor);

// Đưa hình ảnh ra trình duyệt
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>
