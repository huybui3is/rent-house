<?php
include('../db_connect.php');
include('../HeaderFooter/header.php');

if (!isset($_GET["PT_MA"])) {
    echo "<div class='alert alert-danger text-center'>Không có mã phiếu thuê hợp lệ!</div>";
    exit();
}

$pt_ma = $_GET["PT_MA"];
$input = $_GET['input'] ?? '';


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
$trangThai = ($result["PT_tinhtrang"] == 0) 
    ? '<span class="badge bg-danger">Chưa duyệt</span>' 
    : '<span class="badge bg-success">Đã duyệt</span>';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Phiếu Thuê</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid mt-5 mb-4">
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
                    <li class="list-group-item"><strong>Trạng Thái Phiếu Thuê:</strong> <?= $trangThai ?></li>
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
                    <li class="list-group-item"><strong>Địa Chỉ:</strong> 
                        <?= htmlspecialchars($result["KT_SONHA"]) ?>, <?= htmlspecialchars($result["DUONG_TEN"]) ?>,
                        <?= htmlspecialchars($result["xp_TEN"]) ?>, <?= htmlspecialchars($result["qh_TEN"]) ?>, <?= htmlspecialchars($result["ttp_TEN"]) ?>
                    </li>
                    <li class="list-group-item"><strong>Liên Hệ:</strong> <?= htmlspecialchars($result["CKT_SODT"]) ?></li>
                    <li class="list-group-item">
                            <strong>Vị Trí:</strong> 
                            <button type="button" class="btn btn-success" id="choose-location" 
                                data-lat="<?= htmlspecialchars($result["KT_LATITUDE"]) ?>" 
                                data-lng="<?= htmlspecialchars($result["KT_LONGTITUDE"]) ?>"
                                data-address="<?= htmlspecialchars($result["KT_SONHA"] . ', ' . $result["DUONG_TEN"] . ', ' . $result["xp_TEN"] . ', ' . $result["qh_TEN"] . ', ' . $result["ttp_TEN"]) ?>">
                                Xem vị trí nhà trọ
                            </button>

</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
    <a href="search_phongcuaban.php?input=<?= urlencode($input) ?>" class="btn btn-secondary">Quay lại</a>

    </div>

     <!-- Overlay nền tối -->
<div id="overlay" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index:2000;"></div>

<!-- Container hiển thị bản đồ -->
<div id="map-container" style="display:none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width:800px; height:600px; background:white; z-index:2001; box-shadow: 0 4px 6px rgba(0,0,0,0.3); overflow:hidden; border-radius:8px;">
    <button id="close-map" class="btn btn-primary" style="position:absolute; top:10px; right:10px; font-size:20px; cursor:pointer; z-index:2002;">X</button>
    <div id="small-map" style="width:100%; height:100%;"></div>
</div>
<script>
    let modalMap;
    let marker;

    function initModalMap(lat, lng) {

        function initModalMap(lat, lng) {
    // Nếu bản đồ đã tồn tại, chỉ cập nhật tọa độ thay vì xóa bản đồ
    if (!modalMap) {
        modalMap = L.map('small-map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);
    } else {
        modalMap.setView([lat, lng], 15); // Cập nhật vị trí mới
        if (marker) {
            marker.setLatLng([lat, lng]); // Cập nhật marker
        } else {
            marker = L.marker([lat, lng]).addTo(modalMap).bindPopup("Vị trí nhà trọ").openPopup();
        }
    }
}

        // Kiểm tra nếu tọa độ hợp lệ
        if (!lat || !lng) {
            alert("Không có tọa độ nhà trọ!");
            return;
        }

        // Tạo bản đồ tại vị trí chỉ định
        modalMap = L.map('small-map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);

        // Thêm marker tại vị trí nhà trọ
        marker = L.marker([lat, lng]).addTo(modalMap)
    .bindPopup(`<b>Vị trí nhà trọ</b><br>
                <?= htmlspecialchars($result["KT_SONHA"]) ?>, 
                <?= htmlspecialchars($result["DUONG_TEN"]) ?>, 
                <?= htmlspecialchars($result["xp_TEN"]) ?>, 
                <?= htmlspecialchars($result["qh_TEN"]) ?>, 
                <?= htmlspecialchars($result["ttp_TEN"]) ?>`)
    .openPopup();

    }

    document.getElementById('choose-location').addEventListener('click', function() {
    let lat = parseFloat(this.getAttribute('data-lat'));
    let lng = parseFloat(this.getAttribute('data-lng'));

    if (!lat || !lng) {
        alert("Không có tọa độ nhà trọ!");
        return;
    }

    document.getElementById('overlay').style.display = 'block';
    document.getElementById('map-container').style.display = 'block';

    initModalMap(lat, lng); // Hiển thị bản đồ với tọa độ
    });

    document.getElementById('close-map').addEventListener('click', closeModalMap);
    document.getElementById('overlay').addEventListener('click', closeModalMap);

    function closeModalMap() {
        document.getElementById('overlay').style.display = 'none';
        document.getElementById('map-container').style.display = 'none';
        if (modalMap) {
            modalMap.remove();
        }
    }
</script>
</div>

</body>
</html>

<?php
include('../HeaderFooter/footer.php');
?>
