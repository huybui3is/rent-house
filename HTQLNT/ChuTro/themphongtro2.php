<?php 
$page_title = "Quản Lý Phòng Trọ";
include('/xampp/htdocs/HTQLNT/ChuTro/header.php');
include('/xampp/htdocs/HTQLNT/db_connect.php');

$message = "";
$messageType = "";

// Lấy CKT_SODT của chủ khu trọ từ session
$cktSoDt = $_SESSION["user"]["CKT_SODT"];

// Lấy danh sách KT_MaKT của chủ trọ (chỉ lấy các khu trọ có is_delete = 0)
$sql_khuTro_ids = "SELECT KT_MaKT FROM KHU_TRO WHERE CKT_SODT = :cktSoDt AND is_delete = 0";
$stmt_khuTro_ids = $conn->prepare($sql_khuTro_ids);
$stmt_khuTro_ids->bindParam(":cktSoDt", $cktSoDt);
$stmt_khuTro_ids->execute();
$khuTroIds = $stmt_khuTro_ids->fetchAll(PDO::FETCH_COLUMN);

/*
  Hàm sinh mã phòng: tự động lấy giá trị lớn nhất của Phong_maphong và tăng lên.
  Mã phòng có dạng 'P0001'
*/
function generateMaPhong($conn) {
    $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(Phong_maphong, 2, 4) AS UNSIGNED)) AS maxMa FROM PHONG");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextNumber = $result['maxMa'] ? $result['maxMa'] + 1 : 1;
    return 'P' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}

/*
  Hàm sinh số thứ tự (tên phòng) cho khu trọ:
  Lấy giá trị lớn nhất của PHONG_stt (dạng chuỗi 2 số) cho khu trọ có mã $maKT.
  Nếu chưa có phòng nào, trả về "01"; nếu có, tăng và định dạng thành 2 số.
*/
function generatePhongStt($conn, $maKT) {
    $stmt = $conn->prepare("SELECT MAX(CAST(PHONG_stt AS UNSIGNED)) AS maxStt FROM PHONG WHERE kt_makt = :maKT");
    $stmt->bindParam(":maKT", $maKT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextStt = $result['maxStt'] ? $result['maxStt'] + 1 : 1;
    return str_pad($nextStt, 2, '0', STR_PAD_LEFT);
}

// Lấy danh sách loại phòng từ bảng LOAI_PHONG
$sql_loaiPhong = "SELECT LP_maloaiphong, LP_TenLoaiPhong FROM LOAI_PHONG";
$stmt_loaiPhong = $conn->prepare($sql_loaiPhong);
$stmt_loaiPhong->execute();
$loaiPhongList = $stmt_loaiPhong->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách khu trọ của chủ trọ (chỉ khu trọ có is_delete = 0)
$sql_khuTro = "SELECT KT_MaKT, KT_TenKhuTro FROM KHU_TRO WHERE CKT_SODT = :cktSoDt AND is_delete = 0";
$stmt_khuTro = $conn->prepare($sql_khuTro);
$stmt_khuTro->bindParam(":cktSoDt", $cktSoDt);
$stmt_khuTro->execute();
$khuTroList = $stmt_khuTro->fetchAll(PDO::FETCH_ASSOC);

// Xử lý thêm phòng
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_phong'])) {
    $maPhong = generateMaPhong($conn);
    // Lấy mô tả phòng (có thể bao gồm thông tin khác nếu cần)
    $moTa = trim($_POST['PHONG_mota']);
    $maLoaiPhong = trim($_POST['LP_maloaiphong']);
    $maKT = trim($_POST['KT_MaKT']);
    
    // Sinh số thứ tự phòng (tên phòng) theo khu trọ
    $stt = generatePhongStt($conn, $maKT);
    
    // Xử lý upload ảnh
    $anh = "";
    if (isset($_FILES['PHONG_anh']) && $_FILES['PHONG_anh']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = "D:/xampp/htdocs/HTQLNT/ChuTro/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = basename($_FILES['PHONG_anh']['name']);
        $targetFile = $uploadDir . time() . "_" . $fileName;
        if (move_uploaded_file($_FILES['PHONG_anh']['tmp_name'], $targetFile)) {
            $anh = $targetFile;
        }
    }

    // Kiểm tra các trường bắt buộc
    if (empty($moTa) || empty($maLoaiPhong) || empty($maKT)) {
        $message = "Vui lòng điền đầy đủ thông tin!";
        $messageType = "danger";
    } else {
        try {
            // Chèn dữ liệu vào PHONG, lưu số thứ tự (PHONG_stt) làm tên phòng tự tăng
            $stmt = $conn->prepare("INSERT INTO PHONG (Phong_maphong, LP_maloaiphong, PHONG_mota, PHONG_stt, kt_makt, PHONG_anh)
                                    VALUES (:maPhong, :maLoaiPhong, :moTa, :stt, :maKT, :anh)");
            $stmt->bindParam(":maPhong", $maPhong);
            $stmt->bindParam(":maLoaiPhong", $maLoaiPhong);
            $stmt->bindParam(":moTa", $moTa);
            $stmt->bindParam(":stt", $stt);
            $stmt->bindParam(":maKT", $maKT);
            $stmt->bindParam(":anh", $anh);

            if ($stmt->execute()) {
                $message = "Thêm phòng trọ thành công!";
                $messageType = "success";
            } else {
                $message = "Lỗi khi thêm phòng trọ!";
                $messageType = "danger";
            }
        } catch (PDOException $e) {
            $message = "Lỗi hệ thống: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Xử lý xóa phòng (nếu muốn chuyển is_delete thành 1 thay vì xóa, cập nhật câu lệnh UPDATE ở đây)
if (isset($_GET['delete_phong'])) {
    $maPhong = $_GET['delete_phong'];
    try {
        $stmt = $conn->prepare("UPDATE PHONG SET is_delete = 1 WHERE Phong_maphong = :maPhong");
        $stmt->bindParam(":maPhong", $maPhong);
        if ($stmt->execute()) {
            $message = "Xóa phòng trọ thành công!";
            $messageType = "success";
        } else {
            $message = "Lỗi khi xóa phòng trọ!";
            $messageType = "danger";
        }
    } catch (PDOException $e) {
        $message = "Lỗi hệ thống: " . $e->getMessage();
        $messageType = "danger";
    }
}

// Lấy danh sách phòng của chủ trọ (chỉ các phòng có is_delete = 0)
$sql_phong = "SELECT Phong_maphong, PHONG_mota, PHONG_stt, LP_maloaiphong, kt_makt, PHONG_anh 
              FROM PHONG 
              WHERE kt_makt IN (
                  SELECT KT_MaKT FROM KHU_TRO WHERE CKT_SODT = :cktSoDt AND is_delete = 0
              ) AND is_delete = 0";
$stmt_phong = $conn->prepare($sql_phong);
$stmt_phong->bindParam(":cktSoDt", $cktSoDt);
$stmt_phong->execute();
$phongList = $stmt_phong->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Giao diện hiển thị (sử dụng Bootstrap cho đẹp) -->
<div class="container mt-5">
    <h2 class="text-center mb-4">Quản Lý Phòng Trọ</h2>
    <?php if ($message): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            Thêm Phòng Trọ
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <!-- Mã Phòng (readonly) -->            
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Mã Phòng</label>
                        <input type="text" name="Phong_maphong" class="form-control" value="<?= generateMaPhong($conn) ?>" readonly>
                    </div>
                    <!-- Loại Phòng -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Loại Phòng</label>
                        <select name="LP_maloaiphong" class="form-control" required>
                            <option value="">Chọn Loại Phòng</option>
                            <?php foreach ($loaiPhongList as $loaiPhong): ?>
                                <option value="<?= htmlspecialchars($loaiPhong['LP_maloaiphong']) ?>">
                                    <?= htmlspecialchars($loaiPhong['LP_TenLoaiPhong']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Khu Trọ (chỉ hiện khu trọ của chủ và is_delete = 0) -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Khu Trọ</label>
                        <select name="KT_MaKT" class="form-control" required>
                            <option value="">Chọn Khu Trọ</option>
                            <?php foreach ($khuTroList as $khuTro): ?>
                                <option value="<?= htmlspecialchars($khuTro['KT_MaKT']) ?>">
                                    <?= htmlspecialchars($khuTro['KT_TenKhuTro']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Mô Tả -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mô Tả</label>
                        <input type="text" name="PHONG_mota" class="form-control">
                    </div>
                    <!-- Upload Ảnh -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ảnh Phòng</label>
                        <input type="file" name="PHONG_anh" class="form-control">
                    </div>
                </div>
                <button type="submit" name="add_phong" class="btn btn-primary">Thêm Phòng Trọ</button>
                <button type="submit" name="update_phong" class="btn btn-warning">Cập Nhật Phòng Trọ</button>
            </form>
        </div>
    </div>

    <!-- Danh sách phòng -->
    <div class="card">
        <div class="card-header">
            Danh Sách Phòng Trọ
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Mã Phòng</th>
                        <th>Tên Phòng</th>
                        <th>Loại Phòng</th>
                        <th>Khu Trọ</th>
                        <th>Mô Tả</th>
                        <th>STT</th>
                        <th>Ảnh</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($phongList as $phong): ?>
                        <tr>
                            <td><?= htmlspecialchars($phong['Phong_maphong']) ?></td>
                            <!-- Tên Phòng được tự động là số thứ tự (PHONG_stt) -->
                            <td><?= htmlspecialchars($phong['PHONG_stt']) ?></td>
                            <td><?= htmlspecialchars($phong['LP_maloaiphong']) ?></td>
                            <td><?= htmlspecialchars($phong['kt_makt']) ?></td>
                            <td><?= htmlspecialchars($phong['PHONG_mota']) ?></td>
                            <td><?= htmlspecialchars($phong['PHONG_stt']) ?></td>
                            <td>
                                <?php if (!empty($phong['PHONG_anh'])): ?>
                                    <img src="<?= htmlspecialchars($phong['PHONG_anh']) ?>" alt="Ảnh phòng" style="max-width:80px;">
                                <?php else: ?>
                                    Không có ảnh
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?delete_phong=<?= htmlspecialchars($phong['Phong_maphong']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa phòng này?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($phongList)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có phòng trọ nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Nếu bạn muốn hỗ trợ điền dữ liệu từ danh sách vào form để cập nhật, có thể thêm sự kiện click cho các dòng bảng.
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach(function(row) {
        row.addEventListener('click', function() {
            const cells = row.querySelectorAll("td");
            const maPhong = cells[0].textContent;
            const tenPhong = cells[1].textContent; // Tên phòng ở đây là số thứ tự (PHONG_stt)
            const loaiPhong = cells[2].textContent;
            const khuTro = cells[3].textContent;
            const moTa = cells[4].textContent;
            
            // Điền vào form (nếu cần cập nhật)
            document.querySelector('input[name="Phong_maphong"]').value = maPhong;
            document.querySelector('input[name="PHONG_mota"]').value = moTa;
            document.querySelector('select[name="LP_maloaiphong"]').value = loaiPhong;
            document.querySelector('select[name="KT_MaKT"]').value = khuTro;
        });
    });
});
</script>

<?php
include('/xampp/htdocs/HTQLNT/HeaderFooter/footer.php');
?>
