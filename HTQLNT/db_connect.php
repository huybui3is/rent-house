<?php
    $host = "localhost"; // Hoặc IP của máy chủ MySQL
    $dbname = "HTQLNT"; // Thay bằng tên cơ sở dữ liệu của bạn
    $username = "root"; // Thay bằng tên người dùng MySQL
    $password = ""; // Thay bằng mật khẩu MySQL

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Lỗi kết nối: " . $e->getMessage());
    }
?>
