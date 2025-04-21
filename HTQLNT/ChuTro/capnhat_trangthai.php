<?php
include('/xampp/htdocs/HTQLNT/db_connect.php');

if (!isset($_POST["PT_MA"])) {
    echo json_encode(["success" => false, "message" => "Mã phiếu thuê không hợp lệ"]);
    exit();
}

$pt_ma = $_POST["PT_MA"];
$date_now = date("Y-m-d H:i:s");

// Lấy thông tin phiếu thuê
$sql = "SELECT PHONG_MAPHONG, PT_tinhtrang FROM phieu_thue WHERE PT_MA = :pt_ma";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":pt_ma", $pt_ma, PDO::PARAM_STR);
$stmt->execute();
$phieu_thue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$phieu_thue) {
    echo json_encode(["success" => false, "message" => "Không tìm thấy phiếu thuê"]);
    exit();
}

$phong_ma = $phieu_thue["PHONG_MAPHONG"];
$isApproved = $phieu_thue["PT_tinhtrang"] == 1;

if ($isApproved) {
    // HỦY DUYỆT
    // Cập nhật LS_NGAYKETTHUC cho bản ghi TTP_MA = '02'
    $update_ls_02 = "UPDATE lich_su 
                     SET LS_NGAYKETTHUC = :ngayketthuc 
                     WHERE PHONG_MAPHONG = :phong_ma AND TTP_MA = '02'";
    $stmt = $conn->prepare($update_ls_02);
    $stmt->bindValue(":ngayketthuc", $date_now, PDO::PARAM_STR);
    $stmt->bindValue(":phong_ma", $phong_ma, PDO::PARAM_STR);
    $stmt->execute();

    // Cập nhật LS_NGAYKETTHUC = NULL cho bản ghi TTP_MA = '01'
    $update_ls_01 = "UPDATE lich_su 
                     SET LS_NGAYKETTHUC = NULL 
                     WHERE PHONG_MAPHONG = :phong_ma AND TTP_MA = '01'";
    $stmt = $conn->prepare($update_ls_01);
    $stmt->bindValue(":phong_ma", $phong_ma, PDO::PARAM_STR);
    $stmt->execute();

    // Kiểm tra và thêm bản ghi nếu chưa có (TTP_MA = '01')
    $check_sql = "SELECT * FROM lich_su WHERE PHONG_MAPHONG = :phong_ma AND TTP_MA = '01'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bindValue(":phong_ma", $phong_ma, PDO::PARAM_STR);
    $stmt->execute();
    $existing_record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing_record) {
        $insert_sql = "INSERT INTO lich_su (PHONG_MAPHONG, TTP_MA, LS_NGAYBATDAUTHUE, LS_NGAYKETTHUC) 
                       VALUES (:phong_ma, '01', :ngaybatdau, NULL)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bindValue(":phong_ma", $phong_ma, PDO::PARAM_STR);
        $stmt->bindValue(":ngaybatdau", $date_now, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Cập nhật trạng thái phiếu thuê
    $update_pt = "UPDATE phieu_thue SET PT_tinhtrang = 0 WHERE PT_MA = :pt_ma";
    $stmt = $conn->prepare($update_pt);
    $stmt->bindValue(":pt_ma", $pt_ma, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Hủy duyệt thành công"]);
} else {
    // DUYỆT
    // Cập nhật LS_NGAYKETTHUC = NULL cho bản ghi TTP_MA = '02'
    $update_ls_02 = "UPDATE lich_su 
                     SET LS_NGAYKETTHUC = NULL 
                     WHERE PHONG_MAPHONG = :phong_ma AND TTP_MA = '02'";
    $stmt = $conn->prepare($update_ls_02);
    $stmt->bindValue(":phong_ma", $phong_ma, PDO::PARAM_STR);
    $stmt->execute();

    // Cập nhật LS_NGAYKETTHUC = ngày hiện tại cho bản ghi TTP_MA = '01'
    $update_ls_01 = "UPDATE lich_su 
                     SET LS_NGAYKETTHUC = :ngayketthuc 
                     WHERE PHONG_MAPHONG = :phong_ma AND TTP_MA = '01'";
    $stmt = $conn->prepare($update_ls_01);
    $stmt->bindValue(":ngayketthuc", $date_now, PDO::PARAM_STR);
    $stmt->bindValue(":phong_ma", $phong_ma, PDO::PARAM_STR);
    $stmt->execute();

    // Kiểm tra và thêm bản ghi nếu chưa có (TTP_MA = '02')
    $check_sql = "SELECT * FROM lich_su WHERE PHONG_MAPHONG = :phong_ma AND TTP_MA = '02'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bindValue(":phong_ma", $phong_ma, PDO::PARAM_STR);
    $stmt->execute();
    $existing_record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing_record) {
        $insert_sql = "INSERT INTO lich_su (PHONG_MAPHONG, TTP_MA, LS_NGAYBATDAUTHUE, LS_NGAYKETTHUC) 
                       VALUES (:phong_ma, '02', :ngaybatdau, NULL)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bindValue(":phong_ma", $phong_ma, PDO::PARAM_STR);
        $stmt->bindValue(":ngaybatdau", $date_now, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Cập nhật trạng thái phiếu thuê
    $update_pt = "UPDATE phieu_thue SET PT_tinhtrang = 1 WHERE PT_MA = :pt_ma";
    $stmt = $conn->prepare($update_pt);
    $stmt->bindValue(":pt_ma", $pt_ma, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Duyệt phiếu thuê thành công"]);
}
?>
