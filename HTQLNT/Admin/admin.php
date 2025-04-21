<?php
$page_title = "Trang Quản Trị Hệ Thống Quản Lý Nhà Trọ";
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: /login.php"); // Nếu chưa đăng nhập, chuyển về trang login
    exit();
}
$taikhoan = $_SESSION["username"];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?></title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome cho icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
    body {
      background: #f8f9fa;
    }
    /* Header kiểu admin.php */
    .admin-header {
      background-color: #343a40;
      color: #fff;
      padding: 15px 20px;
    }
    .admin-header h1 {
      margin: 0;
      font-size: 24px;
    }
    .admin-header .welcome {
      font-size: 16px;
    }
    /* Dropdown top right của admin1.php */
    .top-dropdown {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 1050;
    }
    .top-dropdown .dropdown-toggle {
      background-color: #ffffff;
      border: 1px solid #ced4da;
      border-radius: 50px;
      padding: 5px 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .top-dropdown .dropdown-toggle:hover {
      background-color: #e9ecef;
    }
    .top-dropdown .dropdown-menu {
      background-color: #ffffff;
      border: 1px solid #ced4da;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    /* Container nội dung */
    .admin-container {
      margin-top: 30px;
    }
    /* Hiệu ứng cho row khi rê chuột */
    .clickable-row {
      cursor: pointer;
      transition: background-color 0.3s;
    }
    .clickable-row:hover {
      background-color: #e9ecef;
    }
    /* Card */
    .card {
      border-radius: 10px;
      transition: transform 0.3s;
    }
    .card:hover {
      transform: scale(1.02);
    }
  </style>
</head>
<body>
  <!-- Header -->
  <nav class="admin-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <h1><?php echo $page_title; ?></h1>
   
    </div>
  </nav>
  
  <!-- Dropdown ở góc trên bên phải -->
  <div class="top-dropdown dropdown">
    <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($taikhoan); ?>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
      <li><a class="dropdown-item" href="../index.php">Trang Chủ Website</a></li>
      <li><a class="dropdown-item" href="logout.php">Đăng Xuất</a></li>
    </ul>
  </div>
  
  <!-- Nội dung chính -->
  <div class="container admin-container mb-5">
    <p class="mb-3">Vui lòng chọn một chức năng bên dưới:</p>
    
    <!-- Card chức năng -->
    <div class="row pb-3 clickable-row" onclick="window.location='daihoc.php';">
      <div class="card shadow p-3">
        <div class="card-body">
          <h5 class="card-title">🎓 Cập Nhật Trường Đại Học</h5>
          <p class="card-text">Quản lý thông tin trường đại học</p>
        </div>
      </div>
    </div>
    
    <div class="row pb-3 clickable-row" onclick="window.location='chutro.php';">
      <div class="card shadow p-3">
        <div class="card-body">
          <h5 class="card-title">🏠 Quản Lý Chủ Trọ</h5>
          <p class="card-text">Xem và chỉnh sửa thông tin chủ trọ</p>
        </div>
      </div>
    </div>
    
    <div class="row pb-3 clickable-row" onclick="window.location='viewmap.php';">
      <div class="card shadow p-3">
        <div class="card-body">
          <h5 class="card-title">🗺️ Xem Bản Đồ</h5>
          <p class="card-text">Xem các trường, trọ và lộ trình từ trường đến trọ</p>
        </div>
      </div>
    </div>
    
    <div class="row pb-3 clickable-row" onclick="window.location='viewmap_choropleth.php';">
      <div class="card shadow p-3">
        <div class="card-body">
          <h5 class="card-title">📊 Thống Kê</h5>
          <p class="card-text">Xem bản đồ thống kê khu trọ theo xã</p>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Bootstrap JS Bundle (bao gồm Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
