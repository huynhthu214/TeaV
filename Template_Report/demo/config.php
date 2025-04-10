<?php
$host = "localhost";
$dbname = "ten_cua_ban";     // Đổi thành tên CSDL thật
$username = "root";          // Đổi nếu server khác
$password = "";              // Mật khẩu MySQL (nếu có)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Kết nối database thất bại: " . $e->getMessage());
}
?>