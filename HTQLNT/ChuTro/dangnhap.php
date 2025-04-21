<?php
session_start();
include('/xampp/htdocs/HTQLNT/db_connect.php');
include('../HeaderFooter/header.php');
$error = "";
$user = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $soDT = $_POST["soDT"];
    $matKhau = $_POST["matKhau"];

 // Kiểm tra xem tài khoản có tồn tại không (bất kể mật khẩu)
 $stmtCheck = $conn->prepare("SELECT * FROM CHU_KHU_TRO WHERE CKT_SoDT = :soDT");
 $stmtCheck->execute([":soDT" => $soDT]);
 $account = $stmtCheck->fetch(PDO::FETCH_ASSOC);

 if ($account) {
     // Nếu tài khoản đã bị xóa mềm, báo lỗi
     if ($account['is_delete'] == 1) {
         $error = "Tài khoản không còn tồn tại!";
     } else {
         // Nếu tài khoản tồn tại và chưa bị xóa mềm, kiểm tra mật khẩu
         $stmt = $conn->prepare("SELECT * FROM CHU_KHU_TRO WHERE CKT_SoDT = :soDT AND CKT_MatKhau = :matKhau AND is_delete = 0");
         $stmt->execute([":soDT" => $soDT, ":matKhau" => $matKhau]);
         $user = $stmt->fetch(PDO::FETCH_ASSOC);

         if ($user) {
             $_SESSION["user"] = $user;
             header("Location: themkhutro.php");
             exit();
         } else {
             $error = "Số điện thoại hoặc mật khẩu không đúng!";
         }
     }
 } else {
     $error = "Số điện thoại hoặc mật khẩu không đúng!";
 }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Chủ Khu Trọ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Đăng nhập Chủ Khu Trọ</h4>
                </div>
                <?php if(isset($_GET['status']) && $_GET['status'] === 'success' && isset($_GET['message'])): ?>
    <div class="alert alert-success text-center">
        <?= htmlspecialchars($_GET['message']) ?>
    </div>
<?php endif; ?>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="soDT" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="soDT" name="soDT" placeholder="Nhập số điện thoại" required>
                        </div>
                        <div class="mb-3">
                            <label for="matKhau" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="matKhau" name="matKhau" placeholder="Nhập mật khẩu" required>
                        </div>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                    </form>
                    <div class="mt-3 text-center">
                        <span>Bạn chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a></span>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <small>&copy; <?= date('Y') ?> Hệ thống Quản Lý Khu Trọ</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Optional: Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php
include('/xampp/htdocs/HTQLNT/HeaderFooter/footer.php');
?>
</html>
