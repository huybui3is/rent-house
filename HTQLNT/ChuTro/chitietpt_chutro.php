<?php
include('/xampp/htdocs/HTQLNT/ChuTro/header.php');
include('/xampp/htdocs/HTQLNT/db_connect.php');

if (!isset($_GET["PT_MA"])) {
    echo "<div class='alert alert-danger text-center'>Không có mã phiếu thuê hợp lệ!</div>";
    exit();
}

$pt_ma = $_GET["PT_MA"];

$sql = "SELECT 
            pt.PT_MA, kh.KH_CCCD, kh.KH_TEN, kh.KH_SDT, pt.PT_NGAYLAP, pt.PT_NGAYBATDAU, pt.PT_NGAYKETTHUC, pt.PT_tinhtrang,
            p.PHONG_MAPHONG, p.PHONG_MOTA, p.PHONG_Stt,
            gt.GT_GIA, gt.GT_NGAYAPDUNG, gt.GT_NGAYKETTHUC,
            kt.KT_TENKHUTRO, kt.KT_SONHA, kt.DUONG_MA, kt.CKT_SODT, kt.KT_LONGTITUDE, kt.KT_LATITUDE, 
            d.DUONG_TEN, xp.xp_TEN,qh.qh_TEN,ttp.ttp_TEN
        FROM phieu_thue pt
        INNER JOIN khach_hang kh ON pt.KH_CCCD = kh.KH_CCCD
        INNER JOIN phong p ON pt.PHONG_MAPHONG = p.PHONG_MAPHONG
        INNER JOIN gia_thue gt ON p.LP_MALOAIPHONG = gt.LP_MALOAIPHONG 
        INNER JOIN khu_tro kt ON p.KT_MAKT = kt.KT_MAKT AND kt.KT_MAKT = gt.KT_MAKT
        INNER JOIN duong d ON kt.DUONG_MA = d.DUONG_MA
        INNER JOIN xa_phuong xp ON d.xp_MA = xp.xp_MA
        INNER JOIN quan_huyen qh ON xp.qh_MA = qh.qh_MA
        INNER JOIN tinh_thanh_pho ttp ON qh.ttp_MAtinh = ttp.ttp_MaTinh
        WHERE pt.PT_MA = :pt_ma";

$stmt = $conn->prepare($sql);
$stmt->bindValue(":pt_ma", $pt_ma, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    echo "<div class='alert alert-warning text-center'>Không tìm thấy phiếu thuê!</div>";
    exit();
}

// Xử lý trạng thái
$isApproved = $result["PT_tinhtrang"] == 1;
$trangThai = $isApproved 
    ? '<span class="badge bg-success">Đã duyệt</span>' 
    : '<span class="badge bg-danger">Chưa duyệt</span>';

$buttonText = $isApproved ? "Hủy duyệt" : "Duyệt";
$buttonClass = $isApproved ? "btn-danger" : "btn-success";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Phiếu Thuê</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Chi Tiết Phiếu Thuê</h2>

    <div class="card shadow-lg p-4">
        <div class="row">
            <div class="col-md-6 border-end">
                <h4 class="text-primary text-center">Thông Tin Khách Hàng</h4>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Họ Tên:</strong> <?= htmlspecialchars($result["KH_TEN"]) ?></li>
                    <li class="list-group-item"><strong>CCCD:</strong> <?= htmlspecialchars($result["KH_CCCD"]) ?></li>
                    <li class="list-group-item"><strong>Số Điện Thoại:</strong> <?= htmlspecialchars($result["KH_SDT"]) ?></li>
                    <li class="list-group-item"><strong>Mã Phiếu Thuê:</strong> <?= htmlspecialchars($result["PT_MA"]) ?></li>
                    <li class="list-group-item"><strong>Ngày Lập:</strong> <?= htmlspecialchars($result["PT_NGAYLAP"]) ?></li>
                    <li class="list-group-item"><strong>Ngày Bắt Đầu Thuê:</strong> <?= htmlspecialchars($result["PT_NGAYBATDAU"]) ?></li>
                    <li class="list-group-item"><strong>Ngày Kết Thúc:</strong> <?= htmlspecialchars($result["PT_NGAYKETTHUC"]) ?></li>
                    <li class="list-group-item"><strong>Trạng Thái Phiếu Thuê:</strong> <span id="trangThai"><?= $trangThai ?></span></li>
                </ul>
            </div>

            <div class="col-md-6">
                <h4 class="text-success text-center">Thông Tin Phòng Trọ</h4>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Mã Phòng:</strong> <?= htmlspecialchars($result["PHONG_MAPHONG"]) ?></li>
                    <li class="list-group-item"><strong>STT Phòng:</strong> <?= htmlspecialchars($result["PHONG_Stt"]) ?></li>
                    <li class="list-group-item"><strong>Mô Tả:</strong> <?= htmlspecialchars($result["PHONG_MOTA"]) ?></li>
                    <li class="list-group-item"><strong>Giá Thuê:</strong> <?= number_format($result["GT_GIA"], 0, ',', '.') ?> VND</li>
                    <li class="list-group-item"><strong>Khu Trọ:</strong> <?= htmlspecialchars($result["KT_TENKHUTRO"]) ?></li>
                    <li class="list-group-item"><strong>Liên Hệ:</strong> <?= htmlspecialchars($result["CKT_SODT"]) ?></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <button id="btnDuyet" class="btn <?= $buttonClass ?>" data-id="<?= $pt_ma ?>"><?= $buttonText ?></button>
        <a href="danhsachphieuthue.php" class="btn btn-secondary">Quay lại</a>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#btnDuyet").click(function() {
        let btn = $(this);
        let pt_ma = btn.data("id");
        let isApproved = btn.text().trim() === "Hủy duyệt"; // Nếu nút đang hiển thị "Hủy duyệt" tức phiếu đã được duyệt

        if (isApproved) {
            // Hỏi xác nhận trước khi hủy duyệt
            if (confirm("Bạn có chắc chắn muốn hủy duyệt phiếu thuê này không?")) {
                $.post("capnhat_trangthai.php", { PT_MA: pt_ma }, function(res) {
                    if (res.success) {
                        $("#trangThai").html('<span class="badge bg-danger">Chưa duyệt</span>');
                        btn.removeClass("btn-danger").addClass("btn-success").text("Duyệt");
                        alert("Hủy duyệt thành công!");
                    } else {
                        alert("Hủy duyệt phiếu thuê thất bại!");
                    }
                }, "json");
            }
        } else {
            // Kiểm tra phòng trước khi duyệt
            $.post("kiemtra_phong.php", { PT_MA: pt_ma }, function(response) {
                if (response.phongDaThue) {
                    alert("Phòng đã được thuê, không thể duyệt phiếu thuê này!");
                } else {
                    // Hỏi xác nhận trước khi duyệt
                    if (confirm("Bạn có chắc chắn muốn duyệt phiếu thuê này không?")) {
                        $.post("capnhat_trangthai.php", { PT_MA: pt_ma }, function(res) {
                            if (res.success) {
                                $("#trangThai").html('<span class="badge bg-success">Đã duyệt</span>');
                                btn.removeClass("btn-success").addClass("btn-danger").text("Hủy duyệt");
                                alert("Duyệt phiếu thuê thành công!");
                            } else {
                                alert("Duyệt phiếu thuê thất bại!");
                            }
                        }, "json");
                    }
                }
            }, "json");
        }
    });
});


    
</script>

</body>
</html>
