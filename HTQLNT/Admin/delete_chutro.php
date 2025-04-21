<?php
// delete_chutro.php
include('../db_connect.php');

if (isset($_GET['sdt'])) {
    $sdt = $_GET['sdt'];
    try {
        // Bắt đầu giao dịch
        $conn->beginTransaction();
        
        // Cập nhật bảng chủ khu trọ
        $sql1 = "UPDATE chu_khu_tro SET is_delete = 1 WHERE CKT_SODT = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([$sdt]);
        
        // // Cập nhật bảng khu trọ (giả sử bảng này có cột CKT_SODT liên kết với chủ trọ)
        // $sql2 = "UPDATE khu_tro SET is_delete = 1 WHERE CKT_SODT = ?";
        // $stmt2 = $conn->prepare($sql2);
        // $stmt2->execute([$sdt]);
        
        // // Cập nhật bảng khoang_cach (dựa vào khóa KT_MAKT từ bảng khu trọ) them cot is_delete
        // $sql3 = "UPDATE khoang_cach SET is_delete = 1 WHERE KT_MAKT IN (SELECT KT_MAKT FROM khu_tro WHERE CKT_SODT = ?)";
        // $stmt3 = $conn->prepare($sql3);
        // $stmt3->execute([$sdt]);

        // // Cập nhật bảng gia_thue (dựa vào khóa KT_MAKT từ bảng khu trọ)
        // $sql4 = "UPDATE gia_thue SET is_delete = 1 WHERE KT_MAKT IN (SELECT KT_MAKT FROM khu_tro WHERE CKT_SODT = ?)";
        // $stmt4 = $conn->prepare($sql4);
        // $stmt4->execute([$sdt]);

        // // Cập nhật bảng phong (dựa vào khóa KT_MAKT từ bảng khu trọ)
        // $sql5 = "UPDATE phong SET is_delete = 1 WHERE KT_MAKT IN (SELECT KT_MAKT FROM khu_tro WHERE CKT_SODT = ?)";
        // $stmt5 = $conn->prepare($sql5);
        // $stmt5->execute([$sdt]);

        // // Cập nhật bảng phieu_thue (dựa vào PHONG_MAPHONG từ bảng phong) thay is_delete thanh PT_TINHTRANG thanh 02
        // $sql6 = "UPDATE phieu_thue SET is_delete = 1 WHERE PHONG_MAPHONG IN (SELECT PHONG_MAPHONG FROM phong WHERE KT_MAKT IN (SELECT KT_MAKT FROM khu_tro WHERE CKT_SODT = ?))";
        // $stmt6 = $conn->prepare($sql6);
        // $stmt6->execute([$sdt]);

        // // Cập nhật bảng lich_su (dựa vào PHONG_MAPHONG từ bảng phong) neu loi thi thay is_delete thành TTP_MA thanh 2
        // $sql7 = "UPDATE lich_su SET is_delete = 1 WHERE PHONG_MAPHONG IN (SELECT PHONG_MAPHONG FROM phong WHERE KT_MAKT IN (SELECT KT_MAKT FROM khu_tro WHERE CKT_SODT = ?))";
        // $stmt7 = $conn->prepare($sql7);
        // $stmt7->execute([$sdt]);

        // Cam kết giao dịch
        $conn->commit();
        header("Location: chutro.php?status=success&message=Xóa+chủ+trọ+thành+công");
        exit();
    } catch (Exception $e) {
        // Nếu có lỗi, rollback giao dịch
        $conn->rollBack();
        header("Location: chutro.php?status=error&message=Đã+có+lỗi+xảy+ra");
        exit();
    }
}
?>
