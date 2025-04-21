<?php
include('/xampp/htdocs/HTQLNT/db_connect.php');

if (isset($_POST["PT_MA"])) {
    $pt_ma = $_POST["PT_MA"];

    // Lấy mã phòng từ phiếu thuê cần kiểm tra
    $sqlPhong = "SELECT PHONG_MAPHONG FROM phieu_thue WHERE PT_MA = ?";
    $stmtPhong = $conn->prepare($sqlPhong);
    $stmtPhong->execute([$pt_ma]);
    $row = $stmtPhong->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["error" => "Không tìm thấy phiếu thuê!"]);
        exit();
    }

    $phong_ma = $row["PHONG_MAPHONG"];

    // Kiểm tra xem phòng này có phiếu thuê nào đã được duyệt chưa
    $sqlCheck = "SELECT COUNT(*) as count FROM phieu_thue WHERE PHONG_MAPHONG = ? AND PT_tinhtrang = 1";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->execute([$phong_ma]);
    $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["phongDaThue" => $result["count"] > 0]);
}
?>
