<?php
session_start();

// Hàm hiển thị trạng thái người dùng (tên và nút đăng xuất/đăng nhập)
function displayUserStatus() {
    if (isset($_SESSION["user"]) && isset($_SESSION["user"]["CKT_HOTEN"])) {
        $hoTen = $_SESSION["user"]["CKT_HOTEN"];
        echo '<div class="text-white me-3">Xin chào, <strong>' . htmlspecialchars($hoTen) . '</strong></div>';
        echo '<a href="/HTQLNT/ChuTro/dangxuat.php" class="btn btn-danger">Đăng xuất</a>'; // Hiển thị nút đăng xuất
    } else {
        echo '<a href="/HTQLNT/ChuTro/dangnhap.php" class="btn btn-success">Đăng nhập</a>';
    }
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION["user"])) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header("Location: /HTQLNT/ChuTro/dangnhap.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Trang chủ chủ trọ'; ?></title>
    <link rel="stylesheet" href="/HTQLNT/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <header class="p-3 bg-dark text-white">
        <div class="container-fluid bg-dark">
            <div class="row align-items-center py-3">
                <div class="col-12 col-md-6 d-flex justify-content-start mb-3 mb-md-0">
                    <a href="/HTQLNT/index.php" class="d-flex align-items-center text-white text-decoration-none">
                        <h1 class="m-0">HTQLNT</h1>
                    </a>
                    <ul class="nav ms-4 mb-0">
                        <!-- <li><a href="indexchutro.php" class="nav-link px-3 text-warning">Trang chủ</a></li> -->
                        <li><a href="themkhutro.php" class="nav-link px-3 text-white">Quản lý khu trọ</a></li>
                        <li><a href="themphongtro.php" class="nav-link px-3 text-white">Quản lý phòng trọ</a></li>
                        <li><a href="danhsachphieuthue.php" class="nav-link px-3 text-white">Duyệt phiếu thuê</a></li>
                        <li><a href="xembando.php" class="nav-link px-3 text-white">Xem bản đồ</a></li>
                        <li><a href="themgiathue.php" class="nav-link px-3 text-white">Quản lý loại phòng</a></li>
                    </ul>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    <?php displayUserStatus(); ?>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid">