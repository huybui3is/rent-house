<?php
$page_title = "Chi tiết phòng";
include('../HeaderFooter/header.php');
include('../db_connect.php');

if (!isset($_GET['maphong'])) {
    echo "<div class='container'><div class='alert alert-danger'>Không có mã phòng được cung cấp.</div></div>";
    include('../HeaderFooter/footer.php');
    exit;
}

$maphong = $_GET['maphong'];

// Truy vấn thông tin chi tiết phòng và các thuộc tính của loại phòng
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
            b.KT_LONGTITUDE AS KT_LONGTITUDE,
            e.PHONG_ANH AS HINH_ANH 
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

// Xử lý thư mục chứa ảnh của phòng (trường PHONG_ANH)
// Đường dẫn hệ thống lưu trong CSDL (ví dụ: "C:\xampp\htdocs\HTQLNT\images\phong006")
$room_images_path = $roomDetail['HINH_ANH'];

// Định nghĩa base URL của website (điều chỉnh nếu cần)
$base_url = "http://localhost/HTQLNT";

// Chuyển đổi đường dẫn Windows thành URL:
// 1. Thay thế backslashes thành forward slashes
$room_images_path_url = str_replace("\\", "/", $room_images_path);
// 2. Loại bỏ phần đường dẫn máy chủ ("C:/xampp/htdocs/HTQLNT") để lấy đường dẫn tương đối
$relative_path = str_replace("D:/xampp/htdocs/HTQLNT", "", $room_images_path_url);
// 3. Tạo URL đầy đủ
$url_path = $base_url . $relative_path;

$images = [];
if (is_dir($room_images_path)) {
    // Lấy danh sách file trong thư mục
    $files = scandir($room_images_path);
    foreach ($files as $file) {
        // Loại bỏ các file hệ thống và chỉ lấy các file có đuôi ảnh
        if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            $images[] = $url_path . '/' . $file;
        }
    }
}
?>

<div class="container my-5">
    <h2 class="text-primary mb-4">Chi tiết phòng: <?= htmlspecialchars($roomDetail['MA_PHONG']) ?></h2>
    
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
                <?= htmlspecialchars($roomDetail['TINH_THANH_PHO']) ?>
                <br>
                <strong>Chủ khu trọ:</strong> <?= htmlspecialchars($roomDetail['CHU_KHU_TRO']) ?> (SĐT: <?= htmlspecialchars($roomDetail['SDT_CHU_KHU_TRO']) ?>)
            </p>
        </div>
    </div>
    
    <!-- Hiển thị slider ảnh của phòng trọ nếu có -->
    <?php if (!empty($images)): ?>
    <div id="roomCarousel" class="carousel slide mb-4" data-ride="carousel" style="background-color: gray;">
        <ol class="carousel-indicators">
            <?php foreach ($images as $index => $img): ?>
            <li data-target="#roomCarousel" data-slide-to="<?= $index ?>" <?= $index === 0 ? 'class="active"' : '' ?>></li>
            <?php endforeach; ?>
        </ol>
        <div class="carousel-inner">
            <?php foreach ($images as $index => $img): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <img src="<?= htmlspecialchars($img) ?>" class="d-block carousel-img" alt="Ảnh phòng">
            </div>
            <?php endforeach; ?>
        </div>
        <?php if(count($images) > 1): ?>
        <a class="carousel-control-prev" href="#roomCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon arrow-icon" aria-hidden="true"></span>
            <span class="sr-only">Trước</span>
        </a>
        <a class="carousel-control-next" href="#roomCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon arrow-icon" aria-hidden="true"></span>
            <span class="sr-only">Tiếp</span>
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-info">Không có hình ảnh của phòng trọ.</div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between">
        <a href="javascript:history.back()" class="btn btn-secondary">Quay lại</a>
        <a href="datphong.php?maphong=<?= $roomDetail['MA_PHONG'] ?>" class="btn btn-primary">Đặt phòng</a>
    </div>
</div>

<!-- CSS điều chỉnh kích thước ảnh và nút điều hướng -->
<style>
.carousel-img {
    max-height: 400px; /* Chiều cao tối đa của ảnh trong carousel */
    object-fit: contain;
    width: 100%;
}

/* Tăng kích thước và độ nổi bật của nút mũi tên */
.arrow-icon {
    width: 50px;
    height: 50px;
    background-size: 50px 50px;
}

/* Tùy chỉnh màu nền nút mũi tên */
.carousel-control-prev-icon, 
.carousel-control-next-icon {
    filter: drop-shadow(2px 2px 2px rgba(0,0,0,0.5));
}
</style>

<?php include('../HeaderFooter/footer.php'); ?>
