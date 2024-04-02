<?php
// Thông tin máy chủ
$servername = "your_server_address"; // Địa chỉ IP hoặc tên miền của máy chủ MySQL
$serverport = "3306"; // Cổng mặc định của MySQL là 3306
$username = "username"; // Tên người dùng MySQL
$password = "password"; // Mật khẩu MySQL
$database = "database"; // Tên cơ sở dữ liệu

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $database, $serverport);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
} else {
    echo "Kết nối thành công!";
}

// Sau khi sử dụng xong, đóng kết nối
$conn->close();
?>
