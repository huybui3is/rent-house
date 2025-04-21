<?php 
$page_title = "Quản Lý Phòng Trọ";
include('/xampp/htdocs/HTQLNT/ChuTro/header.php');
include('/xampp/htdocs/HTQLNT/db_connect.php');

$message = "";
$messageType = "";

// Lấy CKT_SODT của chủ khu trọ từ session
$cktSoDt = $_SESSION["user"]["CKT_SODT"];

// Lấy danh sách khu trọ của chủ trọ (chỉ lấy các khu trọ chưa xóa)
$sql_khuTro = "SELECT KT_MAKT, KT_TENKHUTRO FROM KHU_TRO WHERE CKT_SODT = :cktSoDt AND is_delete = 0";
$stmt_khuTro = $conn->prepare($sql_khuTro);
$stmt_khuTro->bindParam(":cktSoDt", $cktSoDt);
$stmt_khuTro->execute();
$khuTroList = $stmt_khuTro->fetchAll(PDO::FETCH_ASSOC);

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
  Lấy giá trị lớn nhất của PHONG_stt cho khu trọ có mã $maKT.
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_phong'])) {
    $maPhong = generateMaPhong($conn);
    $moTa = trim($_POST['PHONG_mota']);
    $maLoaiPhong = trim($_POST['LP_maloaiphong']);
    $maKT = trim($_POST['KT_MaKT']);
    $stt = generatePhongStt($conn, $maKT);
    
    // Tạo folder cho phòng mới, ví dụ: "upload/P0001/"
    $uploadBaseDir = "D:/xampp/htdocs/HTQLNT/ChuTro/uploads/";  // Điều chỉnh đường dẫn này theo cấu trúc của bạn
    $roomFolder = $uploadBaseDir . $maPhong . "/";
    if (!is_dir($roomFolder)) {
        mkdir($roomFolder, 0777, true);
    }
    
    // Xử lý upload nhiều ảnh từ input PHONG_anh[]
    $uploadedPaths = array();
    if (isset($_FILES['PHONG_anh'])) {
        foreach ($_FILES['PHONG_anh']['error'] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $fileName = basename($_FILES['PHONG_anh']['name'][$key]);
                $targetFile = $roomFolder . time() . "_" . $key . "_" . $fileName;
                if (move_uploaded_file($_FILES['PHONG_anh']['tmp_name'][$key], $targetFile)) {
                    $uploadedPaths[] = $targetFile;
                }
            }
        }
    }
    // Lưu đường dẫn các ảnh dưới dạng JSON
    //$anh = !empty($uploadedPaths) ? json_encode($uploadedPaths) : "";
    //Lưu đường dẫn thư mục ảnh
    $anh = $roomFolder;
    
    if (empty($moTa) || empty($maLoaiPhong) || empty($maKT)) {
        $message = "Vui lòng điền đầy đủ thông tin!";
        $messageType = "danger";
    } else {
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("INSERT INTO PHONG (Phong_maphong, LP_maloaiphong, PHONG_mota, PHONG_stt, kt_makt, PHONG_anh)
                                    VALUES (:maPhong, :maLoaiPhong, :moTa, :stt, :maKT, :anh)");
            $stmt->bindParam(":maPhong", $maPhong);
            $stmt->bindParam(":maLoaiPhong", $maLoaiPhong);
            $stmt->bindParam(":moTa", $moTa);
            $stmt->bindParam(":stt", $stt);
            $stmt->bindParam(":maKT", $maKT);
            $stmt->bindParam(":anh", $anh);
            $stmt->execute();
            
            // Chèn lịch sử phòng với TTP_MA mặc định là "01"
            $stmt_ls = $conn->prepare("INSERT INTO lich_su (TTP_MA, PHONG_MAPHONG, LS_NGAYBATDAUTHUE) 
                                       VALUES ('01', :maPhong, NOW())");
            $stmt_ls->bindParam(":maPhong", $maPhong);
            $stmt_ls->execute();
            
            $conn->commit();
            $message = "Thêm phòng trọ thành công!";
            $messageType = "success";
        } catch (PDOException $e) {
            $conn->rollBack();
            $message = "Lỗi hệ thống: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_phong'])) {
    $maPhong = trim($_POST['Phong_maphong']);
    $moTa = trim($_POST['PHONG_mota']);
    $maLoaiPhong = trim($_POST['LP_maloaiphong']);
    $maKT = trim($_POST['KT_MaKT']);
    

    // Xử lý upload ảnh (nếu có chọn ảnh mới)
    $uploadBaseDir = "D:/xampp/htdocs/HTQLNT/ChuTro/uploads/";
$roomFolder = $uploadBaseDir . $maPhong . "/";

// Kiểm tra và tạo thư mục nếu chưa có
if (!is_dir($roomFolder)) {
    mkdir($roomFolder, 0777, true);
}

// Nếu có ảnh mới, xóa ảnh cũ trước
if (isset($_FILES['PHONG_anh']) && $_FILES['PHONG_anh']['error'] == UPLOAD_ERR_OK) {
    // Xóa tất cả ảnh cũ trong thư mục
    $files = glob($roomFolder . "*"); // Lấy danh sách tất cả file trong thư mục
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file); // Xóa từng file
        }
    }

    // Upload ảnh mới
    $fileName = time() . "_" . basename($_FILES['PHONG_anh']['name']);
    $targetFile = $roomFolder . $fileName;
    if (move_uploaded_file($_FILES['PHONG_anh']['tmp_name'], $targetFile)) {
        $anh = $roomFolder; // Lưu thư mục thay vì đường dẫn file cụ thể
    }
}

if (empty($maPhong)) {
    $message = "Mã phòng không xác định!";
    $messageType = "danger";
} elseif (empty($moTa) || empty($maLoaiPhong) || empty($maKT)) {
    $message = "Vui lòng điền đầy đủ thông tin cập nhật!";
    $messageType = "danger";
} else {
    try {
        $conn->beginTransaction();
        if (!empty($anh)) {
            $stmt = $conn->prepare("UPDATE PHONG 
                                    SET LP_maloaiphong = :maLoaiPhong, 
                                        PHONG_mota = :moTa, 
                                        kt_makt = :maKT, 
                                        PHONG_anh = :anh 
                                    WHERE Phong_maphong = :maPhong");
            $stmt->bindParam(":anh", $anh);
        } else {
            $stmt = $conn->prepare("UPDATE PHONG 
                                    SET LP_maloaiphong = :maLoaiPhong, 
                                        PHONG_mota = :moTa, 
                                        kt_makt = :maKT 
                                    WHERE Phong_maphong = :maPhong");
        }
        $stmt->bindParam(":maLoaiPhong", $maLoaiPhong);
        $stmt->bindParam(":moTa", $moTa);
        $stmt->bindParam(":maKT", $maKT);
        $stmt->bindParam(":maPhong", $maPhong);
        $stmt->execute();
        $conn->commit();
        $message = "Cập nhật phòng trọ thành công!";
        $messageType = "success";
    } catch (PDOException $e) {
        $conn->rollBack();
        $message = "Lỗi hệ thống: " . $e->getMessage();
        $messageType = "danger";
    }
}
}
if (isset($_GET['delete_phong'])) {
    $maPhongList = $_GET['delete_phong']; // Nhận danh sách phòng từ GET

    if (!is_array($maPhongList)) {
        $maPhongList = [$maPhongList]; // Nếu chỉ có 1 phòng, chuyển thành mảng
    }

    try {
        $conn->beginTransaction();

        // Tạo chuỗi placeholders cho danh sách phòng
        $phongPlaceholders = implode(',', array_fill(0, count($maPhongList), '?'));

        // Xóa mềm các phòng
        $stmt = $conn->prepare("UPDATE PHONG SET is_delete = 1 WHERE PHONG_MaPhong IN ($phongPlaceholders)");
        $stmt->execute($maPhongList);

        // Cập nhật lịch sử thuê: chỉ cập nhật bản ghi mới nhất của mỗi phòng nếu TTP_MA chưa bằng '02'
        $stmt_ls = $conn->prepare(
            "UPDATE lich_su ls
             JOIN (
                SELECT PHONG_MaPhong, MAX(LS_NGAYBATDAUTHUE) AS maxNgay
                FROM lich_su
                WHERE PHONG_MaPhong IN ($phongPlaceholders)
                GROUP BY PHONG_MaPhong
             ) AS sub 
             ON ls.PHONG_MaPhong = sub.PHONG_MaPhong AND ls.LS_NGAYBATDAUTHUE = sub.maxNgay
             SET ls.TTP_MA = '03', ls.LS_NGAYBATDAUTHUE = NOW()
             WHERE ls.TTP_MA <> '03'"
        );
        // Truyền mảng $maPhongList một lần (đủ số lượng placeholders trong subquery)
        $stmt_ls->execute($maPhongList);

        // Cập nhật phiếu thuê của các phòng
        $stmt_pt = $conn->prepare(
            "UPDATE phieu_thue 
             SET PT_Tinhtrang = '2', PT_NGAYBATDAU = NOW(), PT_NGAYKETTHUC = NULL
             WHERE PHONG_MaPhong IN ($phongPlaceholders)"
        );
        $stmt_pt->execute($maPhongList);

        $conn->commit();
        $message = "Xóa thành công!";
        $messageType = "success";
    } catch (PDOException $e) {
        $conn->rollBack();
        $message = "Lỗi hệ thống: " . $e->getMessage();
        $messageType = "danger";
    }
}
// Lấy danh sách phòng (JOIN để lấy tên loại phòng và tên khu trọ)
$sql_phong = "SELECT p.Phong_maphong, p.PHONG_mota, p.PHONG_stt, p.PHONG_anh, 
                     lp.LP_maloaiphong, lp.LP_TenLoaiPhong,
                     kt.KT_MAKT, kt.KT_TenKHUTRO
              FROM PHONG p
              JOIN LOAI_PHONG lp ON p.LP_maloaiphong = lp.LP_maloaiphong
              JOIN KHU_TRO kt ON p.kt_makt = kt.KT_MAKT
              WHERE kt.CKT_SODT = :cktSoDt AND p.is_delete = 0";
$stmt_phong = $conn->prepare($sql_phong);
$stmt_phong->bindParam(":cktSoDt", $cktSoDt);
$stmt_phong->execute();
$phongList = $stmt_phong->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Quản Lý Phòng Trọ</h2>
    <?php if ($message): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Form Thêm/Cập Nhật Phòng Trọ -->
    <div class="card mb-4">
        <div class="card-header">
            Thêm Phòng Trọ
        </div>
        <div class="card-body">
            <form method="POST" action="" id="formLP" enctype="multipart/form-data">
                <div class="row">
                    <!-- Mã Phòng (readonly) -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Mã Phòng</label>
                        <input type="text" name="Phong_maphong" class="form-control" value="<?= generateMaPhong($conn) ?>" readonly>
                    </div>
                    <!-- Chọn Khu Trọ -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Khu Trọ</label>
                        <select name="KT_MaKT" id="KT_MaKT" class="form-control" required>
                            <option value="">Chọn Khu Trọ</option>
                            <?php foreach ($khuTroList as $khuTro): ?>
                                <option value="<?= htmlspecialchars($khuTro['KT_MAKT']) ?>">
                                    <?= htmlspecialchars($khuTro['KT_TENKHUTRO']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Chọn Loại Phòng (load qua AJAX) -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Loại Phòng</label>
                        <select name="LP_maloaiphong" id="LP_maloaiphong" class="form-control" required>
                            <option value="">Chọn Loại Phòng</option>
                        </select>
                    </div>
                    <!-- Mô Tả -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mô Tả</label>
                        <input type="text" name="PHONG_mota" class="form-control">
                    </div>
                    <!-- Upload Ảnh (cho phép upload nhiều ảnh) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ảnh Phòng</label>
                        <input type="file" name="PHONG_anh[]" class="form-control" multiple>
                    </div>
                </div>
                <button type="submit" id="submitBtn" name="add_phong" class="btn btn-primary">Thêm Phòng Trọ</button>
                <button type="submit" name="update_phong" class="btn btn-warning">Cập Nhật Phòng Trọ</button>
                <!-- Nút Hủy: khi nhấn sẽ reload trang -->
                <button type="button" id="cancelBtn" class="btn btn-secondary" onclick="window.location.reload();">Hủy</button>
            </form>
        </div>
    </div>

    <!-- Danh sách phòng -->
    <div class="card">
        <div class="card-header">
            Danh Sách Phòng Trọ
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="phongTable">
                <thead class="table-light">
                    <tr>
                        <th>Mã Phòng</th>
                        <th>Tên Phòng (STT)</th>
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
                        <tr class="data-row" 
                            data-phong="<?= htmlspecialchars($phong['Phong_maphong']) ?>" 
                            data-loai="<?= htmlspecialchars($phong['LP_maloaiphong']) ?>" 
                            data-khutro="<?= htmlspecialchars($phong['KT_MAKT']) ?>" 
                            data-mota="<?= htmlspecialchars($phong['PHONG_mota']) ?>">
                            <td><?= htmlspecialchars($phong['Phong_maphong']) ?></td>
                            <td><?= htmlspecialchars($phong['PHONG_stt']) ?></td>
                            <td><?= htmlspecialchars($phong['LP_TenLoaiPhong']) ?></td>
                            <td><?= htmlspecialchars($phong['KT_TenKHUTRO']) ?></td>
                            <td><?= htmlspecialchars($phong['PHONG_mota']) ?></td>
                            <td><?= htmlspecialchars($phong['PHONG_stt']) ?></td>
                            <td>
                                <?php
                                if (!empty($phong['PHONG_anh'])) {
                                    $images = json_decode($phong['PHONG_anh'], true);
                                    if (!empty($images)) {
                                        echo '<img src="' . htmlspecialchars($images[0]) . '" alt="Ảnh phòng" style="max-width:80px;">';
                                    } else {
                                        echo "Không có ảnh";
                                    }
                                } else {
                                    echo "Không có ảnh";
                                }
                                ?>
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

<!-- jQuery: Load danh sách loại phòng theo khu trọ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('#KT_MaKT').on('change', function(){
        var khuTroID = $(this).val();
        if(khuTroID){
            $.ajax({
                type: 'GET',
                url: 'get_loai_phong.php',
                data: { KT_MAKT: khuTroID },
                success: function(html){
                    $('#LP_maloaiphong').html(html);
                }
            });
        } else {
            $('#LP_maloaiphong').html('<option value="">Chọn Loại Phòng</option>');
        }
    });
});
</script>

<!-- Khi click vào 1 hàng, điền dữ liệu vào form và khóa nút "Thêm Phòng Trọ" -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const rows = document.querySelectorAll('#phongTable tbody .data-row');
    rows.forEach(function(row) {
        row.addEventListener('click', function() {
            const maPhong = row.getAttribute('data-phong');
            const maLoai = row.getAttribute('data-loai');
            const maKhuTro = row.getAttribute('data-khutro');
            const moTa = row.getAttribute('data-mota');
            
            document.querySelector('input[name="Phong_maphong"]').value = maPhong;
            document.querySelector('input[name="PHONG_mota"]').value = moTa;
            document.getElementById('KT_MaKT').value = maKhuTro;
            $('#KT_MaKT').trigger('change');
            setTimeout(function(){
                document.getElementById('LP_maloaiphong').value = maLoai;
            }, 500);
            
            // Khóa nút "Thêm Phòng Trọ" khi đang ở chế độ cập nhật
            document.getElementById("submitBtn").disabled = true;
        });
    });
});
</script>

<?php
include('/xampp/htdocs/HTQLNT/HeaderFooter/footer.php');
?>
