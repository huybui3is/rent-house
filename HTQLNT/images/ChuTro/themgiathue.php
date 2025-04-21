<?php 
$page_title = "Quản Lý Giá Thuê & Loại Phòng";
include('/xampp/htdocs/HTQLNT/ChuTro/header.php');
include('/xampp/htdocs/HTQLNT/db_connect.php');

$message = "";
$messageType = "";

// Lấy CKT_SODT của chủ khu trọ từ session
$cktSoDt = $_SESSION["user"]["CKT_SODT"];

// Lấy danh sách khu trọ của chủ trọ (chỉ lấy những khu trọ chưa xóa)
$sql_khuTro = "SELECT KT_MAKT, KT_TENKHUTRO FROM KHU_TRO WHERE CKT_SODT = :cktSoDt AND is_delete = 0";
$stmt_khuTro = $conn->prepare($sql_khuTro);
$stmt_khuTro->bindParam(":cktSoDt", $cktSoDt);
$stmt_khuTro->execute();
$khuTroList = $stmt_khuTro->fetchAll(PDO::FETCH_ASSOC);

/*
  Hàm sinh mã loại phòng: tự động lấy giá trị lớn nhất của LP_MALOAIPHONG và tăng lên.
  Mã phòng có dạng 'L0001'
*/
function generateMaLoaiPhong($conn) {
    $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(LP_MALOAIPHONG, 2, 4) AS UNSIGNED)) AS maxMa FROM loai_phong");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextNumber = $result['maxMa'] ? $result['maxMa'] + 1 : 1;
    return 'L' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}

// Xác định ngày tối thiểu cho trường "Ngày Kết Thúc" (ít nhất là ngày mai)
$minDate = date("Y-m-d", strtotime("+1 day"));

// ----- XỬ LÝ THÊM ----- //
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_gia_thue'])) {
    // Lấy dữ liệu từ form
    $KT_MAKT = trim($_POST['KT_MaKT']);
    $LP_TENLOAIPHONG = trim($_POST['LP_TENLOAIPHONG']);
    $LP_DIENTICH = floatval($_POST['LP_DIENTICH']);
    $LP_SUCCHUA = intval($_POST['LP_SUCCHUA']);
    $LP_VATCHAT = trim($_POST['LP_VATCHAT']);
    $GT_GIA = floatval($_POST['GT_GIA']);
    $GT_NGAYKETTHUC = trim($_POST['GT_NGAYKETTHUC']); // YYYY-MM-DD

    if (empty($KT_MAKT) || empty($LP_TENLOAIPHONG) || $LP_DIENTICH <= 0 || $LP_SUCCHUA <= 0 || empty($LP_VATCHAT) || $GT_GIA <= 0 || empty($GT_NGAYKETTHUC)) {
        $message = "Vui lòng điền đầy đủ thông tin và đảm bảo số lớn hơn 0!";
        $messageType = "danger";
    } elseif ($GT_NGAYKETTHUC <= date("Y-m-d")) {
        $message = "Ngày kết thúc phải là ngày trong tương lai!";
        $messageType = "danger";
    } else {
        try {
            $conn->beginTransaction();
            
            // Sinh mã loại phòng mới
            $LP_MALOAIPHONG = generateMaLoaiPhong($conn);
            
            // Thêm loại phòng vào bảng loai_phong
            $stmt = $conn->prepare("INSERT INTO loai_phong (LP_MALOAIPHONG, LP_TENLOAIPHONG, LP_DIENTICH, LP_SUCCHUA, LP_VATCHAT, is_delete)
                                    VALUES (:LP_MALOAIPHONG, :LP_TENLOAIPHONG, :LP_DIENTICH, :LP_SUCCHUA, :LP_VATCHAT, 0)");
            $stmt->bindParam(":LP_MALOAIPHONG", $LP_MALOAIPHONG);
            $stmt->bindParam(":LP_TENLOAIPHONG", $LP_TENLOAIPHONG);
            $stmt->bindParam(":LP_DIENTICH", $LP_DIENTICH);
            $stmt->bindParam(":LP_SUCCHUA", $LP_SUCCHUA);
            $stmt->bindParam(":LP_VATCHAT", $LP_VATCHAT);
            $stmt->execute();
            
            // Thêm giá thuê vào bảng gia_thue
            $stmt2 = $conn->prepare("INSERT INTO gia_thue (LP_MALOAIPHONG, KT_MAKT, GT_NGAYAPDUNG, GT_GIA, GT_NGAYKETTHUC, is_delete)
                                    VALUES (:LP_MALOAIPHONG, :KT_MAKT, NOW(), :GT_GIA, :GT_NGAYKETTHUC, 0)");
            $stmt2->bindParam(":LP_MALOAIPHONG", $LP_MALOAIPHONG);
            $stmt2->bindParam(":KT_MAKT", $KT_MAKT);
            $stmt2->bindParam(":GT_GIA", $GT_GIA);
            $stmt2->bindParam(":GT_NGAYKETTHUC", $GT_NGAYKETTHUC);
            $stmt2->execute();
            
            $conn->commit();
            $message = "Thêm loại phòng và giá thuê thành công!";
            $messageType = "success";
        } catch (PDOException $e) {
            $conn->rollBack();
            $message = "Lỗi hệ thống: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// ----- XỬ LÝ CẬP NHẬT ----- //
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_gia_thue'])) {
    $LP_MALOAIPHONG = trim($_POST['LP_MALOAIPHONG']);
    $KT_MAKT = trim($_POST['KT_MaKT']);
    $LP_TENLOAIPHONG = trim($_POST['LP_TENLOAIPHONG']);
    $LP_DIENTICH = floatval($_POST['LP_DIENTICH']);
    $LP_SUCCHUA = intval($_POST['LP_SUCCHUA']);
    $LP_VATCHAT = trim($_POST['LP_VATCHAT']);
    $GT_GIA = floatval($_POST['GT_GIA']);
    $GT_NGAYKETTHUC = trim($_POST['GT_NGAYKETTHUC']);

    if (empty($LP_MALOAIPHONG)) {
        $message = "Mã loại phòng không xác định!";
        $messageType = "danger";
    } elseif (empty($KT_MAKT) || empty($LP_TENLOAIPHONG) || $LP_DIENTICH <= 0 || $LP_SUCCHUA <= 0 || empty($LP_VATCHAT) || $GT_GIA <= 0 || empty($GT_NGAYKETTHUC)) {
        $message = "Vui lòng điền đầy đủ thông tin cập nhật và đảm bảo số lớn hơn 0!";
        $messageType = "danger";
    } elseif ($GT_NGAYKETTHUC <= date("Y-m-d")) {
        $message = "Ngày kết thúc phải là ngày trong tương lai!";
        $messageType = "danger";
    } else {
        try {
            $conn->beginTransaction();
            // Cập nhật bảng loai_phong
            $stmt = $conn->prepare("UPDATE loai_phong 
                                    SET LP_TENLOAIPHONG = :LP_TENLOAIPHONG, 
                                        LP_DIENTICH = :LP_DIENTICH, 
                                        LP_SUCCHUA = :LP_SUCCHUA, 
                                        LP_VATCHAT = :LP_VATCHAT 
                                    WHERE LP_MALOAIPHONG = :LP_MALOAIPHONG");
            $stmt->bindParam(":LP_TENLOAIPHONG", $LP_TENLOAIPHONG);
            $stmt->bindParam(":LP_DIENTICH", $LP_DIENTICH);
            $stmt->bindParam(":LP_SUCCHUA", $LP_SUCCHUA);
            $stmt->bindParam(":LP_VATCHAT", $LP_VATCHAT);
            $stmt->bindParam(":LP_MALOAIPHONG", $LP_MALOAIPHONG);
            $stmt->execute();
            
            // Cập nhật bảng gia_thue
            $stmt2 = $conn->prepare("UPDATE gia_thue 
                                     SET KT_MAKT = :KT_MAKT, 
                                         GT_GIA = :GT_GIA, 
                                         GT_NGAYKETTHUC = :GT_NGAYKETTHUC 
                                     WHERE LP_MALOAIPHONG = :LP_MALOAIPHONG AND is_delete = 0");
            $stmt2->bindParam(":KT_MAKT", $KT_MAKT);
            $stmt2->bindParam(":GT_GIA", $GT_GIA);
            $stmt2->bindParam(":GT_NGAYKETTHUC", $GT_NGAYKETTHUC);
            $stmt2->bindParam(":LP_MALOAIPHONG", $LP_MALOAIPHONG);
            $stmt2->execute();
            
            $conn->commit();
            $message = "Cập nhật loại phòng và giá thuê thành công!";
            $messageType = "success";
        } catch (PDOException $e) {
            $conn->rollBack();
            $message = "Lỗi hệ thống: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// ----- XỬ LÝ XÓA MỀM ----- //
if (isset($_GET['delete_lp'])) {
    $LP_MALOAIPHONG = $_GET['delete_lp'];
    try {
        $conn->beginTransaction();

        // Xóa mềm loại phòng
        $stmt = $conn->prepare("UPDATE loai_phong SET is_delete = 1 WHERE LP_MALOAIPHONG = :LP_MALOAIPHONG");
        $stmt->bindParam(":LP_MALOAIPHONG", $LP_MALOAIPHONG);
        $stmt->execute();

        // Xóa mềm bảng giá thuê
        $stmt2 = $conn->prepare("UPDATE gia_thue SET is_delete = 1 WHERE LP_MALOAIPHONG = :LP_MALOAIPHONG");
        $stmt2->bindParam(":LP_MALOAIPHONG", $LP_MALOAIPHONG);
        $stmt2->execute();

        // Lấy danh sách phòng thuộc loại phòng này
        $stmt_phong = $conn->prepare("SELECT PHONG_MaPhong FROM PHONG WHERE LP_maloaiphong = :LP_MALOAIPHONG");
        $stmt_phong->bindParam(":LP_MALOAIPHONG", $LP_MALOAIPHONG);
        $stmt_phong->execute();
        $phongList = $stmt_phong->fetchAll(PDO::FETCH_COLUMN);
        // Loại bỏ các phần tử trùng lặp và re-index lại
        $phongList = array_values(array_unique($phongList));

        if (!empty($phongList)) {
            // Tạo chuỗi placeholders cho danh sách phòng
            $phongPlaceholders = implode(',', array_fill(0, count($phongList), '?'));

            // Xóa mềm các phòng có loại phòng bị xóa
            $stmt3 = $conn->prepare("UPDATE PHONG SET is_delete = 1 WHERE PHONG_MaPhong IN ($phongPlaceholders)");
            $stmt3->execute($phongList);

            // Cập nhật lịch sử thuê: chỉ cập nhật bản ghi mới nhất của mỗi phòng nếu TTP_MA chưa là '02'
            $stmt_ls = $conn->prepare(
                "UPDATE lich_su ls
                 JOIN (
                    SELECT PHONG_MaPhong, MAX(LS_NGAYBATDAUTHUE) AS maxNgay
                    FROM lich_su
                    WHERE PHONG_MaPhong IN ($phongPlaceholders)
                    GROUP BY PHONG_MaPhong
                 ) AS sub 
                 ON ls.PHONG_MaPhong = sub.PHONG_MaPhong AND ls.LS_NGAYBATDAUTHUE = sub.maxNgay
                 SET ls.TTP_MA = '02', ls.LS_NGAYBATDAUTHUE = NOW()
                 WHERE ls.TTP_MA <> '02'"
            );
            // Vì trong câu lệnh chỉ có 1 lần sử dụng placeholders, truyền mảng $phongList một lần
            $stmt_ls->execute($phongList);

            // Cập nhật phiếu thuê của các phòng có loại phòng bị xóa mềm
            $stmt_pt = $conn->prepare(
                "UPDATE phieu_thue 
                 SET PT_TrangThai = '1', PT_NGAYBATDAU = NOW(), PT_NGAYKETTHUC = NULL
                 WHERE PHONG_MaPhong IN ($phongPlaceholders)"
            );
            $stmt_pt->execute($phongList);
        }

        $conn->commit();
        $message = "Xóa mềm loại phòng, bảng giá thuê, phòng, lịch sử và phiếu thuê thành công!";
        $messageType = "success";
    } catch (PDOException $e) {
        $conn->rollBack();
        $message = "Lỗi hệ thống: " . $e->getMessage();
        $messageType = "danger";
    }
}

// Lấy danh sách loại phòng & giá thuê (join các bảng) để hiển thị danh sách
$sql_list = "SELECT gt.*, lp.LP_TENLOAIPHONG, lp.LP_DIENTICH, lp.LP_SUCCHUA, lp.LP_VATCHAT, kt.KT_TENKHUTRO 
             FROM gia_thue gt
             JOIN loai_phong lp ON gt.LP_MALOAIPHONG = lp.LP_MALOAIPHONG
             JOIN khu_tro kt ON gt.KT_MAKT = kt.KT_MAKT
             WHERE kt.CKT_SODT = :cktSoDt AND lp.is_delete = 0 AND gt.is_delete = 0";
$stmt_list = $conn->prepare($sql_list);
$stmt_list->bindParam(":cktSoDt", $cktSoDt);
$stmt_list->execute();
$listRecords = $stmt_list->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Quản Lý Giá Thuê & Loại Phòng</h2>
    <?php if ($message): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Form Thêm / Cập Nhật -->
    <div class="card mb-4">
        <div class="card-header">
            <span id="form-title">Thêm Loại Phòng & Giá Thuê</span>
        </div>
        <div class="card-body">
            <form method="POST" action="" id="formLP">
                <!-- Trường ẩn lưu mã loại phòng khi cập nhật -->
                <input type="hidden" name="LP_MALOAIPHONG" id="LP_MALOAIPHONG" value="">
                <div class="row">
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
                    <!-- Tên Loại Phòng -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tên Loại Phòng</label>
                        <input type="text" name="LP_TENLOAIPHONG" id="LP_TENLOAIPHONG" class="form-control" required>
                    </div>
                    <!-- Diện Tích -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Diện Tích (m²)</label>
                        <input type="number" step="1" min="0" name="LP_DIENTICH" id="LP_DIENTICH" class="form-control" required>
                    </div>
                    <!-- Sức Chứa -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sức Chứa</label>
                        <input type="number" min="0" name="LP_SUCCHUA" id="LP_SUCCHUA" class="form-control" required>
                    </div>
                    <!-- Vật Chất -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Vật Chất</label>
                        <input type="text" name="LP_VATCHAT" id="LP_VATCHAT" class="form-control" required>
                    </div>
                    <!-- Giá Thuê -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Giá Thuê</label>
                        <input type="number" step="100000" min="0" name="GT_GIA" id="GT_GIA" class="form-control" required>
                    </div>
                    <!-- Ngày Kết Thúc -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ngày Kết Thúc (YYYY-MM-DD)</label>
                        <input type="date" name="GT_NGAYKETTHUC" id="GT_NGAYKETTHUC" class="form-control" required min="<?= $minDate ?>">
                    </div>
                </div>
                <button type="submit" id="submitBtn" name="add_gia_thue" class="btn btn-primary">Thêm Loại Phòng & Giá Thuê</button>
                <button type="button" id="cancelBtn" class="btn btn-secondary d-none">Hủy</button>
            </form>
        </div>
    </div>

    <!-- Danh sách Loại Phòng & Giá Thuê -->
    <div class="card">
        <div class="card-header">
            Danh Sách Loại Phòng & Giá Thuê
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="lpTable">
                <thead class="table-light">
                    <tr>
                        <th>LP_MALOAIPHONG</th>
                        <th>Tên Loại Phòng</th>
                        <th>Diện Tích</th>
                        <th>Sức Chứa</th>
                        <th>Vật Chất</th>
                        <th>Khu Trọ</th>
                        <th>Giá Thuê</th>
                        <th>Ngày Áp Dụng</th>
                        <th>Ngày Kết Thúc</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listRecords as $record): ?>
                        <tr class="data-row"
                            data-lp="<?= htmlspecialchars($record['LP_MALOAIPHONG']) ?>"
                            data-ten="<?= htmlspecialchars($record['LP_TENLOAIPHONG']) ?>"
                            data-dientich="<?= htmlspecialchars($record['LP_DIENTICH']) ?>"
                            data-succhua="<?= htmlspecialchars($record['LP_SUCCHUA']) ?>"
                            data-vatchat="<?= htmlspecialchars($record['LP_VATCHAT']) ?>"
                            data-kt="<?= htmlspecialchars($record['KT_MAKT']) ?>"
                            data-gia="<?= htmlspecialchars($record['GT_GIA']) ?>"
                            data-ngayketthuc="<?= htmlspecialchars($record['GT_NGAYKETTHUC']) ?>"
                        >
                            <td><?= htmlspecialchars($record['LP_MALOAIPHONG']) ?></td>
                            <td><?= htmlspecialchars($record['LP_TENLOAIPHONG']) ?></td>
                            <td><?= htmlspecialchars($record['LP_DIENTICH']) ?></td>
                            <td><?= htmlspecialchars($record['LP_SUCCHUA']) ?></td>
                            <td><?= htmlspecialchars($record['LP_VATCHAT']) ?></td>
                            <td><?= htmlspecialchars($record['KT_TENKHUTRO']) ?></td>
                            <td><?= htmlspecialchars($record['GT_GIA']) ?></td>
                            <td><?= htmlspecialchars($record['GT_NGAYAPDUNG']) ?></td>
                            <td><?= htmlspecialchars($record['GT_NGAYKETTHUC']) ?></td>
                            <td>
                                <a href="?delete_lp=<?= htmlspecialchars($record['LP_MALOAIPHONG']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa loại phòng này không?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($listRecords)): ?>
                        <tr>
                            <td colspan="10" class="text-center">Không có bản ghi nào!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- jQuery để load danh sách loại phòng theo khu trọ -->
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

<!-- Khi click vào 1 hàng, điền dữ liệu vào form -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dataRows = document.querySelectorAll(".data-row");
    dataRows.forEach(function(row) {
        row.addEventListener("click", function() {
            const lp = row.getAttribute("data-lp");
            const ten = row.getAttribute("data-ten");
            const dientich = row.getAttribute("data-dientich");
            const succhua = row.getAttribute("data-succhua");
            const vatchat = row.getAttribute("data-vatchat");
            const kt = row.getAttribute("data-kt");
            const gia = row.getAttribute("data-gia");
            const ngayketthuc = row.getAttribute("data-ngayketthuc");
            
            document.getElementById("LP_MALOAIPHONG").value = lp;
            document.getElementById("LP_TENLOAIPHONG").value = ten;
            document.getElementById("LP_DIENTICH").value = dientich;
            document.getElementById("LP_SUCCHUA").value = succhua;
            document.getElementById("LP_VATCHAT").value = vatchat;
            document.getElementById("KT_MaKT").value = kt;
            document.getElementById("GT_GIA").value = gia;
            // Cắt chuỗi ngày chỉ lấy phần "YYYY-MM-DD"
            document.getElementById("GT_NGAYKETTHUC").value = ngayketthuc.substr(0,10);

            document.getElementById("form-title").textContent = "Cập Nhật Loại Phòng & Giá Thuê";
            document.getElementById("submitBtn").textContent = "Cập Nhật Loại Phòng & Giá Thuê";
            document.getElementById("submitBtn").name = "update_gia_thue";
            document.getElementById("cancelBtn").classList.remove("d-none");
        });
    });
    
    document.getElementById("cancelBtn").addEventListener("click", function(){
        document.getElementById("formLP").reset();
        document.getElementById("LP_MALOAIPHONG").value = "";
        document.getElementById("form-title").textContent = "Thêm Loại Phòng & Giá Thuê";
        document.getElementById("submitBtn").textContent = "Thêm Loại Phòng & Giá Thuê";
        document.getElementById("submitBtn").name = "add_gia_thue";
        document.getElementById("cancelBtn").classList.add("d-none");
    });
});
</script>

<?php
include('/xampp/htdocs/HTQLNT/HeaderFooter/footer.php');
?>
