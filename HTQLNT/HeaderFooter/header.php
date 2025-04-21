<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
          <?php
            if (isset($page_title)) {
                echo "HTQLNT | " . $page_title;
            } else {
                echo "HTQLNT | Trang Web của Tôi";
            }
          ?>
    </title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<header class="p-3 bg-dark text-white">
    <div class="container-fluid bg-dark">
      <div class="row align-items-center py-3">
        <!-- Logo và điều hướng bên trái -->
        <div class="col-12 col-md-6 d-flex justify-content-start mb-3 mb-md-0">
          <a href="/HTQLNT/index.php" class="d-flex align-items-center text-white text-decoration-none">
            <h1 class="m-0">HTQLNT</h1>
          </a>
          <ul class="nav ms-4 mb-0">
            <li><a href="/HTQLNT/index.php" class="nav-link px-3 text-warning">Trang chủ</a></li>
            <li><a href="/HTQLNT/NguoiDung/timtro.php" class="nav-link px-3 text-white"><i class="fa-solid fa-magnifying-glass"></i> Tìm trọ</a></li>
            <li><a href="/HTQLNT/NguoiDung/phongcuaban.php" class="nav-link px-3 text-white"><i class="fa-solid fa-house"></i> Phòng trọ của bạn</a></li>
            <li><a href="/HTQLNT/ChuTro/dangnhap.php" class="nav-link px-3 text-white"><i class="fa-solid fa-user"></i> Chủ trọ</a></li>
            <li><a href="/HTQLNT/Admin/login.php" class="nav-link px-3 text-white"><i class="fa-solid fa-user-tie"></i> Quản trị</a></li>
          </ul>
        </div>

        <!-- Tìm kiếm và nút đăng nhập, đăng ký bên phải -->
        <!-- <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
          <form class="d-flex me-3">
            <input type="search" class="form-control form-control-dark" placeholder="Search..." aria-label="Search">
          </form>
          <button type="button" class="btn btn-outline-light me-2">Tìm kiếm</button>
        </div> -->
      </div>
    </div>
  </header>
