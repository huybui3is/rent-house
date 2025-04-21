<?php
// delete_truong.php
include('../db_connect.php');

// Kiểm tra xem có truyền mã trường không
if (!isset($_GET['ma_truong'])) {
    header("Location: daihoc.php");
    exit();
}

$ma_truong = $_GET['ma_truong'];

try {
    // Nếu có các bảng liên quan (ví dụ: bảng khoang_cach) bạn nên xóa dữ liệu liên quan trước
    $stmt = $conn->prepare("DELETE FROM khoang_cach WHERE TRUONG_MA = ?");
    $stmt->execute([$ma_truong]);

    // Xóa trường trong bảng truong
    $stmt = $conn->prepare("DELETE FROM truong WHERE TRUONG_MA = ?");
    $stmt->execute([$ma_truong]);

    // Sau khi xóa, chuyển hướng về trang daihoc.php
    header("Location: daihoc.php?status=success&message=Xóa+trường+thành+công");
    exit();
} catch (Exception $e) {
    echo "Lỗi khi xóa trường: " . $e->getMessage();
}
?>
