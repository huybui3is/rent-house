<?php  
$page_title = "Quản lý Khu Trọ";
include('/xampp/htdocs/HTQLNT/ChuTro/header.php');
include('/xampp/htdocs/HTQLNT/db_connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION["user"])) {
    header("Location: /HTQLNT/ChuTro/dangnhap.php");
    exit();
}

$message = "";
$messageType = "";
$cktSoDt = $_SESSION["user"]["CKT_SODT"];

// Xử lý thêm/cập nhật khu trọ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maKT = isset($_POST['KT_MaKT']) ? trim($_POST['KT_MaKT']) : '';
    $soNha = isset($_POST['KT_SoNha']) ? trim($_POST['KT_SoNha']) : '';
    $tenKhuTro = isset($_POST['KT_TenKhuTro']) ? trim($_POST['KT_TenKhuTro']) : '';
    $longitude = isset($_POST['KT_Longtitude']) ? trim($_POST['KT_Longtitude']) : '';
    $latitude = isset($_POST['KT_Latitude']) ? trim($_POST['KT_Latitude']) : '';

    // Lấy thông tin địa chỉ từ form
    $tinh   = isset($_POST['TTP_MaTinh']) ? trim($_POST['TTP_MaTinh']) : '';
    $quan   = isset($_POST['QH_Ma']) ? trim($_POST['QH_Ma']) : '';
    $xa     = isset($_POST['XP_Ma']) ? trim($_POST['XP_Ma']) : '';
    $duongTen = isset($_POST['DUONG_Ma']) ? trim($_POST['DUONG_Ma']) : '';

    if (empty($soNha) || empty($tenKhuTro) || empty($longitude) || empty($latitude)
        || empty($tinh) || empty($quan) || empty($xa) || empty($duongTen)) {
        $message = "Vui lòng điền đầy đủ thông tin!";
        $messageType = "danger";
    } else {
        try {
            /* --- 1. Xử lý thêm mới địa chỉ (Tỉnh, Quận, Xã, Đường) --- */
            // --- Xử lý Tỉnh/Thành Phố ---
            $stmtTinh = $conn->prepare("SELECT TTP_MaTinh FROM TINH_THANH_PHO WHERE TTP_Ten = :tinh_ten LIMIT 1");
            $stmtTinh->bindParam(":tinh_ten", $tinh);
            $stmtTinh->execute();
            $tinhMa = $stmtTinh->fetchColumn();
            if (!$tinhMa) {
                $queryTinh = "SELECT TTP_MaTinh FROM TINH_THANH_PHO ORDER BY TTP_MaTinh DESC LIMIT 1";
                $stmtTinhLast = $conn->prepare($queryTinh);
                $stmtTinhLast->execute();
                $lastTinhCode = $stmtTinhLast->fetchColumn();
                $newMaTinh = 'TTP01';
                if ($lastTinhCode) {
                    $lastNumber = (int) substr($lastTinhCode, 3);
                    $newMaTinh = 'TTP' . str_pad($lastNumber + 1, 2, "0", STR_PAD_LEFT);
                }
                $stmtInsertTinh = $conn->prepare("INSERT INTO TINH_THANH_PHO (TTP_MaTinh, TTP_Ten) VALUES (:maTinh, :tinh_ten)");
                $stmtInsertTinh->bindParam(":maTinh", $newMaTinh);
                $stmtInsertTinh->bindParam(":tinh_ten", $tinh);
                $stmtInsertTinh->execute();
                $tinhMa = $newMaTinh;
            }
            // --- Xử lý Quận/Huyện ---
            $stmtQuan = $conn->prepare("SELECT QH_Ma FROM QUAN_HUYEN WHERE QH_Ten = :quan_ten AND TTP_MaTinh = :maTinh LIMIT 1");
            $stmtQuan->bindParam(":quan_ten", $quan);
            $stmtQuan->bindParam(":maTinh", $tinhMa);
            $stmtQuan->execute();
            $quanMa = $stmtQuan->fetchColumn();
            if (!$quanMa) {
                $queryQuan = "SELECT QH_Ma FROM QUAN_HUYEN ORDER BY QH_Ma DESC LIMIT 1";
                $stmtQuanLast = $conn->prepare($queryQuan);
                $stmtQuanLast->execute();
                $lastQuanCode = $stmtQuanLast->fetchColumn();
                $newMaQuan = 'QH01';
                if ($lastQuanCode) {
                    $lastNumber = (int) substr($lastQuanCode, 2);
                    $newMaQuan = 'QH' . str_pad($lastNumber + 1, 2, "0", STR_PAD_LEFT);
                }
                $stmtInsertQuan = $conn->prepare("INSERT INTO QUAN_HUYEN (QH_Ma, TTP_MaTinh, QH_Ten) VALUES (:maQuan, :maTinh, :quan_ten)");
                $stmtInsertQuan->bindParam(":maQuan", $newMaQuan);
                $stmtInsertQuan->bindParam(":maTinh", $tinhMa);
                $stmtInsertQuan->bindParam(":quan_ten", $quan);
                $stmtInsertQuan->execute();
                $quanMa = $newMaQuan;
            }
            // --- Xử lý Xã/Phường ---
            $stmtXa = $conn->prepare("SELECT XP_Ma FROM XA_PHUONG WHERE XP_Ten = :xa_ten AND QH_Ma = :maQuan LIMIT 1");
            $stmtXa->bindParam(":xa_ten", $xa);
            $stmtXa->bindParam(":maQuan", $quanMa);
            $stmtXa->execute();
            $xaMa = $stmtXa->fetchColumn();
            if (!$xaMa) {
                $queryXa = "SELECT XP_Ma FROM XA_PHUONG ORDER BY XP_Ma DESC LIMIT 1";
                $stmtXaLast = $conn->prepare($queryXa);
                $stmtXaLast->execute();
                $lastXaCode = $stmtXaLast->fetchColumn();
                $newMaXa = 'XP001';
                if ($lastXaCode) {
                    $lastNumber = (int) substr($lastXaCode, 2);
                    $newMaXa = 'XP' . str_pad($lastNumber + 1, 3, "0", STR_PAD_LEFT);
                }
                $stmtInsertXa = $conn->prepare("INSERT INTO XA_PHUONG (XP_Ma, QH_Ma, XP_Ten) VALUES (:maXa, :maQuan, :xa_ten)");
                $stmtInsertXa->bindParam(":maXa", $newMaXa);
                $stmtInsertXa->bindParam(":maQuan", $quanMa);
                $stmtInsertXa->bindParam(":xa_ten", $xa);
                $stmtInsertXa->execute();
                $xaMa = $newMaXa;
            }
            // --- Xử lý Đường ---
            $stmtDuong = $conn->prepare("SELECT DUONG_Ma FROM DUONG WHERE DUONG_Ten = :duong_ten AND XP_Ma = :maXa LIMIT 1");
            $stmtDuong->bindParam(":duong_ten", $duongTen);
            $stmtDuong->bindParam(":maXa", $xaMa);
            $stmtDuong->execute();
            $duongMa = $stmtDuong->fetchColumn();
            if (!$duongMa) {
                $queryDuong = "SELECT DUONG_Ma FROM DUONG ORDER BY DUONG_Ma DESC LIMIT 1";
                $stmtDuongLast = $conn->prepare($queryDuong);
                $stmtDuongLast->execute();
                $lastDuongCode = $stmtDuongLast->fetchColumn();
                $newMaDuong = 'DU001';
                if ($lastDuongCode) {
                    $lastNumber = (int) substr($lastDuongCode, 2);
                    $newMaDuong = 'DU' . str_pad($lastNumber + 1, 3, "0", STR_PAD_LEFT);
                }
                $stmtInsertDuong = $conn->prepare("INSERT INTO DUONG (DUONG_Ma, XP_Ma, DUONG_Ten) VALUES (:maDuong, :maXa, :duong_ten)");
                $stmtInsertDuong->bindParam(":maDuong", $newMaDuong);
                $stmtInsertDuong->bindParam(":maXa", $xaMa);
                $stmtInsertDuong->bindParam(":duong_ten", $duongTen);
                $stmtInsertDuong->execute();
                $duongMa = $newMaDuong;
            }

            /* --- 2. Thêm hoặc cập nhật khu trọ --- */
            if (empty($maKT)) {
                $query = "SELECT KT_MAKT FROM khu_tro ORDER BY KT_MAKT DESC LIMIT 1";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $lastCode = $stmt->fetchColumn();
                $newMaKT = 'KT001';
                if ($lastCode) {
                    $lastNumber = (int) substr($lastCode, 2);
                    $newMaKT = 'KT' . str_pad($lastNumber + 1, 3, "0", STR_PAD_LEFT);
                }
                $stmt = $conn->prepare("INSERT INTO khu_tro (KT_MAKT, KT_SONHA, DUONG_MA, KT_TENKHUTRO, KT_LONGTITUDE, KT_LATITUDE, CKT_SODT, is_delete) 
                                        VALUES (:maKT, :soNha, :duongMa, :tenKhuTro, :longitude, :latitude, :cktSoDt, 0)");
                $stmt->bindParam(":maKT", $newMaKT);
            } else {
                $stmt = $conn->prepare("UPDATE khu_tro 
                                        SET KT_SONHA = :soNha, DUONG_MA = :duongMa, KT_TENKHUTRO = :tenKhuTro, KT_LONGTITUDE = :longitude, KT_LATITUDE = :latitude 
                                        WHERE KT_MAKT = :maKT AND CKT_SODT = :cktSoDt");
                $stmt->bindParam(":maKT", $maKT);
            }
            $stmt->bindParam(":soNha", $soNha);
            $stmt->bindParam(":duongMa", $duongMa);
            $stmt->bindParam(":tenKhuTro", $tenKhuTro);
            $stmt->bindParam(":longitude", $longitude);
            $stmt->bindParam(":latitude", $latitude);
            $stmt->bindParam(":cktSoDt", $cktSoDt);

            if ($stmt->execute()) {
                $message = empty($maKT) ? "Thêm khu trọ thành công!" : "Cập nhật khu trọ thành công!";
                $messageType = "success";
            } else {
                $message = "Lỗi khi thực hiện thao tác!";
                $messageType = "danger";
            }
        } catch (PDOException $e) {
            $message = "Lỗi hệ thống: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

if (isset($_GET['delete'])) {
    $maKT = $_GET['delete'];
    try {
        $stmt = $conn->prepare("UPDATE khu_tro SET is_delete = 1 WHERE KT_MAKT = :maKT AND CKT_SODT = :cktSoDt");
        $stmt->bindParam(":maKT", $maKT);
        $stmt->bindParam(":cktSoDt", $cktSoDt);
        $stmt->execute();

        $stmtPhong = $conn->prepare("UPDATE phong SET is_delete = 1 WHERE KT_MAKT = :maKT");
        $stmtPhong->bindParam(":maKT", $maKT);
        $stmtPhong->execute();

        $message = "Xóa khu trọ thành công!";
        $messageType = "success";
    } catch (PDOException $e) {
        $message = "Lỗi hệ thống: " . $e->getMessage();
        $messageType = "danger";
    }
}

// Lấy danh sách khu trọ (chỉ các bản ghi chưa xóa)
try {
    $sql_khuTro = "SELECT kt.KT_MAKT, kt.KT_TENKHUTRO, kt.KT_SONHA, du.DUONG_Ten, 
                         kt.KT_LONGTITUDE, kt.KT_LATITUDE, ttp.TTP_Ten, qh.QH_Ten, xp.XP_Ten
                  FROM khu_tro kt
                  LEFT JOIN DUONG du ON kt.DUONG_MA = du.DUONG_MA
                  LEFT JOIN XA_PHUONG xp ON du.XP_MA = xp.XP_MA
                  LEFT JOIN QUAN_HUYEN qh ON xp.QH_MA = qh.QH_MA
                  LEFT JOIN TINH_THANH_PHO ttp ON qh.TTP_MaTinh = ttp.TTP_MaTinh
                  WHERE kt.CKT_SODT = :cktSoDt AND kt.is_delete = 0";
    $stmt_khuTro = $conn->prepare($sql_khuTro);
    $stmt_khuTro->bindParam(":cktSoDt", $cktSoDt);
    $stmt_khuTro->execute();
    $khuTroList = $stmt_khuTro->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Lỗi truy vấn: " . $e->getMessage();
    $messageType = "danger";
    $khuTroList = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page_title) ?></title>
    <!-- Các file CSS: Bootstrap, Leaflet, ... -->
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Quản lý Khu Trọ</h2>
    <?php if ($message): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Form Thêm/Cập Nhật Khu Trọ -->
    <form method="POST" action="">
        <div class="row">
            <!-- Mã Khu Trọ (readonly) -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Mã Khu Trọ</label>
                <input type="text" name="KT_MaKT" class="form-control" readonly>
            </div>
            <!-- Tên Khu Trọ -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Tên Khu Trọ</label>
                <input type="text" name="KT_TenKhuTro" class="form-control" required>
            </div>
            <!-- Số Nhà -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Số Nhà</label>
                <input type="text" name="KT_SoNha" class="form-control" required>
            </div>
            <!-- Tỉnh/Thành Phố -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Tỉnh/Thành Phố</label>
                <input type="text" name="TTP_MaTinh" class="form-control" placeholder="Nhập Tỉnh/Thành Phố" required>
            </div>
            <!-- Quận/Huyện -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Quận/Huyện</label>
                <input type="text" name="QH_Ma" class="form-control" placeholder="Nhập Quận/Huyện" required>
            </div>
            <!-- Xã/Phường -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Xã/Phường</label>
                <input type="text" name="XP_Ma" class="form-control" placeholder="Nhập Xã/Phường" required>
            </div>
            <!-- Đường -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Đường</label>
                <input type="text" name="DUONG_Ma" class="form-control" placeholder="Nhập tên đường" required>
            </div>
            <!-- Kinh Độ & Vĩ Độ -->
            <div class="col-md-6 mb-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kinh Độ (Longitude)</label>
                        <input type="text" name="KT_Longtitude" class="form-control" placeholder="Kinh độ" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vĩ Độ (Latitude)</label>
                        <input type="text" name="KT_Latitude" class="form-control" placeholder="Vĩ độ" required>
                    </div>
                </div>
            </div>
        </div>
        <!-- Nút thao tác -->
        <div class="row mt-3">
        <div class="row mt-3 justify-content-center">
        <div class="row mt-3">
  <!-- Cột bên trái: Thêm & Cập Nhật -->
        <div class="col d-flex align-items-center">
            <button type="submit" class="btn btn-primary btn-sm me-2">Thêm Khu Trọ</button>
            <button type="submit" name="update" class="btn btn-warning btn-sm">Cập Nhật Khu Trọ</button>
            <button type="button" class="btn btn-secondary btn-sm" id="cancelKT" style="display:none;" onclick="window.location.reload();"> Hủy</button>
        </div>
        <!-- Cột bên phải: Chọn điểm & Hủy -->
        <div class="col d-flex justify-content-end align-items-center">
            <button type="button" class="btn btn-secondary btn-sm me-2" id="choose-location">Chọn điểm</button>
           
        </div>
        </div>
    </form>
</div>

<!-- Hiển thị danh sách khu trọ -->
<div class="container mt-4">
    <h3 class="mt-5">Danh Sách Khu Trọ</h3>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="khuTroTable">
            <thead>
                <tr class="table-primary">
                    <th class="text-center">Mã Khu Trọ</th>
                    <th>Tên Khu Trọ</th>
                    <th>Số Nhà</th>
                    <th>Tỉnh/Thành Phố</th>
                    <th>Quận/Huyện</th>
                    <th>Xã/Phường</th>
                    <th>Đường</th>
                    <th class="text-center">Kinh Độ</th>
                    <th class="text-center">Vĩ Độ</th>
                    <th class="text-center">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($khuTroList)): ?>
                    <?php foreach ($khuTroList as $khuTro): ?>
                        <tr class="data-row">
                            <td class="text-center"><?= htmlspecialchars($khuTro['KT_MAKT']) ?></td>
                            <td><?= htmlspecialchars($khuTro['KT_TENKHUTRO']) ?></td>
                            <td><?= htmlspecialchars($khuTro['KT_SONHA']) ?></td>
                            <td><?= htmlspecialchars($khuTro['TTP_Ten']) ?></td>
                            <td><?= htmlspecialchars($khuTro['QH_Ten']) ?></td>
                            <td><?= htmlspecialchars($khuTro['XP_Ten']) ?></td>
                            <td><?= htmlspecialchars($khuTro['DUONG_Ten']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($khuTro['KT_LONGTITUDE']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($khuTro['KT_LATITUDE']) ?></td>
                            <td class="text-center">
                                <a href="?delete=<?= htmlspecialchars($khuTro['KT_MAKT']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Không có dữ liệu khu trọ.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal map để chọn vị trí (sử dụng Leaflet) -->
<div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000;"></div>
<div id="map-container" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); width:800px; height:600px; background:white; z-index:2001; box-shadow:0 4px 6px rgba(0,0,0,0.3); overflow:hidden; border-radius:8px;">
    <button id="close-map" class="btn btn-primary" style="position:absolute; top:10px; right:10px; font-size:20px; cursor:pointer; z-index:2002;">X</button>
    <div id="small-map" style="width:100%; height:100%;"></div>
</div>

<script>
// Khởi tạo bản đồ modal và xử lý reverse geocoding (giữ nguyên)
let modalMap;
function initModalMap() {
    modalMap = L.map('small-map').setView([10.0301, 105.7792], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);
    modalMap.on('click', async function(e) {
        let chosenLatLng = e.latlng;
        L.marker(chosenLatLng).addTo(modalMap)
            .bindPopup("Bạn đã chọn điểm này")
            .openPopup();
        document.querySelector('[name="KT_Latitude"]').value = chosenLatLng.lat;
        document.querySelector('[name="KT_Longtitude"]').value = chosenLatLng.lng;
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${chosenLatLng.lat}&lon=${chosenLatLng.lng}&accept-language=vi`);
            const data = await response.json();
            let address = data.address;
            if (address) {
                let province = address.state || address.region || address.city || '';
                if (province) {
                    document.querySelector('input[name="TTP_MaTinh"]').value = province;
                }
                let district = address.county || address.suburb || '';
                if (district) {
                    document.querySelector('input[name="QH_Ma"]').value = district;
                }
                let ward = address.neighbourhood || address.city_district || address.village || '';
                if (ward) {
                    document.querySelector('input[name="XP_Ma"]').value = ward;
                }
                let street = address.road || '';
                if (street) {
                    document.querySelector('input[name="DUONG_Ma"]').value = street;
                }
            }
        } catch (error) {
            console.error('Reverse geocoding error:', error);
        }
        closeModalMap();
    });
}

document.getElementById('choose-location').addEventListener('click', function(){
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('map-container').style.display = 'block';
    initModalMap();
});
document.getElementById('close-map').addEventListener('click', closeModalMap);
document.getElementById('overlay').addEventListener('click', closeModalMap);

function closeModalMap(){
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('map-container').style.display = 'none';
    if(modalMap){
        modalMap.remove();
    }
}

// Khi click vào một dòng trong bảng khu trọ, load thông tin vào form và khóa nút Thêm Khu Trọ
document.querySelectorAll("#khuTroTable tbody tr").forEach(function(row) {
    row.addEventListener("click", function(){
        const cells = row.querySelectorAll("td");
        if(cells.length >= 9){
            document.querySelector('input[name="KT_MaKT"]').value = cells[0].textContent.trim();
            document.querySelector('input[name="KT_TenKhuTro"]').value = cells[1].textContent.trim();
            document.querySelector('input[name="KT_SoNha"]').value = cells[2].textContent.trim();
            document.querySelector('input[name="TTP_MaTinh"]').value = cells[3].textContent.trim();
            document.querySelector('input[name="QH_Ma"]').value = cells[4].textContent.trim();
            document.querySelector('input[name="XP_Ma"]').value = cells[5].textContent.trim();
            document.querySelector('input[name="DUONG_Ma"]').value = cells[6].textContent.trim();
            document.querySelector('input[name="KT_Longtitude"]').value = cells[7].textContent.trim();
            document.querySelector('input[name="KT_Latitude"]').value = cells[8].textContent.trim();
            
            // Khóa nút "Thêm Khu Trọ"
            document.querySelector('button[type="submit"].btn-primary').disabled = true;
            // Hiện nút "Hủy"
            document.getElementById("cancelKT").style.display = "block";
        }
    });
});
</script>
<?php
include('/xampp/htdocs/HTQLNT/HeaderFooter/footer.php');
?>
</body>
</html>

