<?php
include('/xampp/htdocs/HTQLNT/db_connect.php');

if(isset($_GET['KT_MAKT']) && !empty($_GET['KT_MAKT'])){
    $KT_MAKT = $_GET['KT_MAKT'];
    
    // Giả sử bảng LOAI_PHONG có cột KT_MAKT để liên kết với khu trọ
    $sql = "SELECT lp.LP_maloaiphong, lp.LP_TenLoaiPhong
            FROM LOAI_PHONG lp
            JOIN gia_thue gt ON gt.LP_MALOAIPHONG = lp.LP_maloaiphong
            JOIN khu_tro kt ON gt.KT_MAKT = kt.KT_MAKT
            WHERE kt.KT_MAKT = :KT_MAKT
            AND gt.is_delete = 0; ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":KT_MAKT", $KT_MAKT);
    $stmt->execute();
    $loaiPhongList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if($loaiPhongList){
        echo '<option value="">Chọn Loại Phòng</option>';
        foreach($loaiPhongList as $loaiPhong){
            echo '<option value="'.htmlspecialchars($loaiPhong['LP_maloaiphong']).'">'.htmlspecialchars($loaiPhong['LP_TenLoaiPhong']).'</option>';
        }
    } else {
        echo '<option value="">Không có loại phòng nào</option>';
    }
} else {
    echo '<option value="">Chọn Loại Phòng</option>';
}
?>
