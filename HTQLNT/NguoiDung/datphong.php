<?php
$page_title = "Đặt phòng";
include('../HeaderFooter/header.php');
include('../db_connect.php');

if (!isset($_GET['maphong'])) {
    echo "<div class='container'><div class='alert alert-danger'>Không có mã phòng được cung cấp.</div></div>";
    include('../HeaderFooter/footer.php');
    exit;
}

$maphong = $_GET['maphong'];

// Lấy thông tin chi tiết phòng và các thuộc tính của loại phòng
$sql = "SELECT 
            e.PHONG_MAPHONG AS MA_PHONG, 
            d.LP_TENLOAIPHONG AS LOAI_PHONG,
            d.LP_DIENTICH AS DIENTICH,
            d.LP_SUCCHUA AS SUC_CHUA,
            d.LP_VATCHAT AS VATCHAT,
            c.GT_GIA AS GIA_1_THANG, 
            h.DUONG_TEN AS DUONG, 
            i.XP_TEN AS XA_PHUONG, 
            j.QH_TEN AS QUAN_HUYEN, 
            k.TTP_TEN AS TINH_THANH_PHO,
            a.CKT_HOTEN AS CHU_KHU_TRO,
            a.CKT_SODT AS SDT_CHU_KHU_TRO,
            b.KT_SONHA AS SO_NHA,
            b.KT_LATITUDE AS KT_LATITUDE,
            b.KT_LONGTITUDE AS KT_LONGTITUDE
        FROM chu_khu_tro a
        JOIN khu_tro b ON a.CKT_SODT = b.CKT_SODT
        JOIN gia_thue c ON b.KT_MAKT = c.KT_MAKT
        JOIN loai_phong d ON c.LP_MALOAIPHONG = d.LP_MALOAIPHONG
        JOIN phong e ON b.KT_MAKT = e.KT_MAKT AND e.LP_MALOAIPHONG = d.LP_MALOAIPHONG
        JOIN lich_su f ON e.PHONG_MAPHONG = f.PHONG_MAPHONG
        JOIN tinh_trang_phong g ON f.TTP_MA = g.TTP_MA
        JOIN duong h ON b.DUONG_MA = h.DUONG_MA
        JOIN xa_phuong i ON h.XP_MA = i.XP_MA
        JOIN quan_huyen j ON i.QH_MA = j.QH_MA
        JOIN tinh_thanh_pho k ON j.TTP_MATINH = k.TTP_MATINH
        WHERE e.PHONG_MAPHONG = :maphong 
          AND g.TTP_MA = '01' 
          AND f.LS_NGAYKETTHUC IS NULL
";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':maphong', $maphong, PDO::PARAM_STR);
$stmt->execute();
$roomDetail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$roomDetail) {
    echo "<div class='container'><div class='alert alert-danger'>Không tìm thấy phòng có mã: " . htmlspecialchars($maphong) . "</div></div>";
    include('../HeaderFooter/footer.php');
    exit;
}

// Thiết lập giá trị mặc định cho ngày bắt đầu thuê (sau 3 ngày) và ngày kết thúc thuê (1 tháng sau ngày bắt đầu)
$defaultStart = date('Y-m-d', strtotime('+3 days'));
$defaultEnd   = date('Y-m-d', strtotime($defaultStart . ' +1 month'));

$errors = [];
$successMsg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hoten       = trim($_POST['hoten']);
    $cccd        = trim($_POST['cccd']);
    $sodt        = trim($_POST['sodt']);
    $ngaybatdau  = trim($_POST['ngaybatdau']);
    $ngayketthuc = trim($_POST['ngayketthuc']);
    
    // Kiểm tra các trường bắt buộc
    if (empty($hoten)) {
        $errors[] = "Họ tên không được để trống.";
    }
    if (empty($cccd)) {
        $errors[] = "Số căn cước không được để trống.";
    }
    if (empty($sodt)) {
        $errors[] = "Số điện thoại không được để trống.";
    }
    if (empty($ngaybatdau)) {
        $errors[] = "Ngày bắt đầu thuê không được để trống.";
    }
    if (empty($ngayketthuc)) {
        $errors[] = "Ngày kết thúc thuê không được để trống.";
    }
    
    // Kiểm tra định dạng số căn cước: chỉ cho phép số, độ dài từ 9 đến 12 ký tự
    if (!empty($cccd) && !preg_match('/^\d{12}$/', $cccd)) {
        $errors[] = "Số căn cước phải là số và có độ dài 12 ký tự.";
    }
    // Kiểm tra định dạng số điện thoại: phải bắt đầu bằng 0 và có 10 hoặc 11 số
    if (!empty($sodt) && !preg_match('/^0\d{9}$/', $sodt)) {
        $errors[] = "Số điện thoại phải bắt đầu bằng 0 và có 10 hoặc 11 số.";
    }
    
    // Kiểm tra ngày kết thúc thuê phải ít nhất là 1 tháng sau ngày bắt đầu thuê
    if (!empty($ngaybatdau) && !empty($ngayketthuc)) {
        $startTimestamp = strtotime($ngaybatdau);
        $minEndTimestamp = strtotime('+1 month', $startTimestamp);
        $endTimestamp = strtotime($ngayketthuc);
        
        if ($endTimestamp < $minEndTimestamp) {
            $errors[] = "Ngày kết thúc thuê phải ít nhất là 1 tháng sau ngày bắt đầu thuê.";
        }
    }
    
    if (empty($errors)) {
        // Kiểm tra và thêm mới khách hàng nếu chưa tồn tại
        $checkSql = "SELECT COUNT(*) FROM khach_hang WHERE KH_CCCD = :cccd";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindValue(':cccd', $cccd, PDO::PARAM_STR);
        $checkStmt->execute();
        if ($checkStmt->fetchColumn() == 0) {
            $insertCustomerSql = "INSERT INTO khach_hang (KH_CCCD, KH_TEN, KH_SDT) VALUES (:cccd, :hoten, :sodt)";
            $insertCustomerStmt = $conn->prepare($insertCustomerSql);
            $insertCustomerStmt->bindValue(':cccd', $cccd, PDO::PARAM_STR);
            $insertCustomerStmt->bindValue(':hoten', $hoten, PDO::PARAM_STR);
            $insertCustomerStmt->bindValue(':sodt', $sodt, PDO::PARAM_STR);
            $insertCustomerStmt->execute();
        }
        
        // Sinh mã phiếu thuê theo định dạng "PTxxx"
        $maxCodeRow = $conn->query("SELECT MAX(PT_MA) AS max_ma FROM PHIEU_THUE")->fetch(PDO::FETCH_ASSOC);
        if ($maxCodeRow && $maxCodeRow['max_ma']) {
            $currentMax = intval(substr($maxCodeRow['max_ma'], 2));
        } else {
            $currentMax = 0;
        }
        $newNumber = $currentMax + 1;
        $pt_ma = "PT" . sprintf("%03d", $newNumber);
        
        $pt_ngaylap = date('Y-m-d'); // Ngày lập phiếu
        $pt_tinhtrang = "Chờ phê duyệt";
        
        // Lưu thông tin yêu cầu đặt phòng vào bảng PHIEU_THUE
        $insertSql = "INSERT INTO PHIEU_THUE (PT_MA, PHONG_MAPHONG, KH_CCCD, PT_NGAYLAP, PT_NGAYBATDAU, PT_NGAYKETTHUC, PT_TINHTRANG) 
                      VALUES (:pt_ma, :maphong, :cccd, :pt_ngaylap, :ngaybatdau, :ngayketthuc, :pt_tinhtrang)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bindValue(':pt_ma', $pt_ma, PDO::PARAM_STR);
        $insertStmt->bindValue(':maphong', $maphong, PDO::PARAM_STR);
        $insertStmt->bindValue(':cccd', $cccd, PDO::PARAM_STR);
        $insertStmt->bindValue(':pt_ngaylap', $pt_ngaylap, PDO::PARAM_STR);
        $insertStmt->bindValue(':ngaybatdau', $ngaybatdau, PDO::PARAM_STR);
        $insertStmt->bindValue(':ngayketthuc', $ngayketthuc, PDO::PARAM_STR);
        $insertStmt->bindValue(':pt_tinhtrang', $pt_tinhtrang, PDO::PARAM_STR);
        
        if ($insertStmt->execute()) {
            echo "<script>
                alert('Yêu cầu đặt phòng đã được gửi, vui lòng chờ chủ trọ phê duyệt.');
                window.location.href = '../NguoiDung/timtro.php';
            </script>";
            exit; // Kết thúc script để tránh chạy tiếp code phía dưới
        } else {
            $errors[] = "Có lỗi xảy ra khi gửi yêu cầu đặt phòng. Vui lòng thử lại sau.";
        }
    }
}
?>
<div class="container my-5">
    <h2 class="text-primary mb-4">Đặt phòng: <?= htmlspecialchars($roomDetail['MA_PHONG']) ?></h2>
    
    <!-- Hiển thị thông tin phòng -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title"><?= htmlspecialchars($roomDetail['LOAI_PHONG']) ?></h4>
            <p class="card-text">
                <strong>Giá thuê:</strong> <?= number_format($roomDetail['GIA_1_THANG'], 0, ',', '.') ?> VNĐ / tháng <br>
                <strong>Diện tích:</strong> <?= htmlspecialchars($roomDetail['DIENTICH']) ?> m² <br>
                <strong>Sức chứa:</strong> <?= htmlspecialchars($roomDetail['SUC_CHUA']) ?> người <br>
                <strong>Vật chất:</strong> <?= htmlspecialchars($roomDetail['VATCHAT']) ?> <br>
                <strong>Địa chỉ:</strong> <?= htmlspecialchars($roomDetail['SO_NHA']) ?>, 
                <?= htmlspecialchars($roomDetail['DUONG']) ?>, 
                <?= htmlspecialchars($roomDetail['XA_PHUONG']) ?>, 
                <?= htmlspecialchars($roomDetail['QUAN_HUYEN']) ?>, 
                <?= htmlspecialchars($roomDetail['TINH_THANH_PHO']) ?> <br>
                <strong>Chủ khu trọ:</strong> <?= htmlspecialchars($roomDetail['CHU_KHU_TRO']) ?> (SĐT: <?= htmlspecialchars($roomDetail['SDT_CHU_KHU_TRO']) ?>)
            </p>
        </div>
    </div>
    
    <!-- Thông báo lỗi hoặc thành công -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($successMsg) ?>
        </div>
    <?php endif; ?>
    
    <!-- Form đặt phòng -->
    <form method="POST" action="datphong.php?maphong=<?= htmlspecialchars($maphong) ?>">
        <div class="mb-3">
            <label for="hoten" class="form-label">Họ tên</label>
            <input type="text" class="form-control" id="hoten" name="hoten" value="<?= isset($_POST['hoten']) ? htmlspecialchars($_POST['hoten']) : '' ?>" required>
        </div>
        <div class="mb-3">
            <label for="cccd" class="form-label">Số căn cước</label>
            <input type="text" class="form-control" id="cccd" name="cccd" value="<?= isset($_POST['cccd']) ? htmlspecialchars($_POST['cccd']) : '' ?>" required>
        </div>
        <div class="mb-3">
            <label for="sodt" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" id="sodt" name="sodt" value="<?= isset($_POST['sodt']) ? htmlspecialchars($_POST['sodt']) : '' ?>" required>
        </div>
        <div class="mb-3">
            <label for="ngaybatdau" class="form-label">Ngày bắt đầu thuê</label>
            <input type="date" class="form-control" id="ngaybatdau" name="ngaybatdau" value="<?= isset($_POST['ngaybatdau']) ? htmlspecialchars($_POST['ngaybatdau']) : $defaultStart ?>" required>
            <small class="form-text text-muted">Ngày bắt đầu thuê phải sau ít nhất 3 ngày kể từ ngày hiện tại.</small>
        </div>
        <div class="mb-3">
            <label for="ngayketthuc" class="form-label">Ngày kết thúc thuê</label>
            <input type="date" class="form-control" id="ngayketthuc" name="ngayketthuc" value="<?= isset($_POST['ngayketthuc']) ? htmlspecialchars($_POST['ngayketthuc']) : $defaultEnd ?>" required>
            <small class="form-text text-muted">Ngày kết thúc thuê phải ít nhất 1 tháng sau ngày bắt đầu thuê.</small>
        </div>
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Quay lại</button>
            <button type="submit" class="btn btn-primary">Gửi yêu cầu</button>
        </div>
    </form>
</div>

<?php include('../HeaderFooter/footer.php'); ?>
