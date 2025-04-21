<?php
session_start();
include('/xampp/htdocs/HTQLNT/db_connect.php');
include('../HeaderFooter/header.php');

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = trim($_POST['ten']);
    $sdt = trim($_POST['sdt']);
    $gioitinh = trim($_POST['gioitinh']);
    $matkhau = trim($_POST['matkhau']);

    // Kiểm tra nhập đầy đủ thông tin
    if (empty($ten) || empty($sdt) || empty($gioitinh) || empty($matkhau)) {
        $error_message = "Vui lòng nhập đầy đủ thông tin.";
    }
    // Ràng buộc tên chỉ cho phép chữ cái và khoảng trắng (bao gồm cả ký tự có dấu)
    else if (!preg_match("/^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯăâếệîôớèý\s]+$/u", $ten)) {
        $error_message = "Tên chỉ được chứa chữ cái và khoảng trắng.";
    }
    // Ràng buộc số điện thoại phải là số và đúng 10 số
    else if (!preg_match("/^[0-9]{10}$/", $sdt)) {
        $error_message = "Số điện thoại phải là số và đúng 10 số.";
    }
    else {
        // Kiểm tra số điện thoại đã tồn tại và đang hoạt động (is_delete = 0)
        $sqlCheckActive = "SELECT COUNT(*) FROM chu_khu_tro WHERE CKT_SODT = ? AND is_delete = 0";
        $stmtActive = $conn->prepare($sqlCheckActive);
        $stmtActive->execute([$sdt]);
        $activeCount = $stmtActive->fetchColumn();

        if ($activeCount > 0) {
            $error_message = "Số điện thoại đã tồn tại, vui lòng nhập số khác.";
        } else {
            // Kiểm tra xem số điện thoại có tồn tại nhưng đã soft delete (is_delete = 1)
            $sqlCheckDeleted = "SELECT COUNT(*) FROM chu_khu_tro WHERE CKT_SODT = ? AND is_delete = 1";
            $stmtDeleted = $conn->prepare($sqlCheckDeleted);
            $stmtDeleted->execute([$sdt]);
            $deletedCount = $stmtDeleted->fetchColumn();

            if ($deletedCount > 0) {
                // Nếu tồn tại bản ghi bị soft delete, cập nhật lại (reactivate)
                $sqlUpdate = "UPDATE chu_khu_tro 
                              SET CKT_HOTEN = ?, CKT_GIOITINH = ?, CKT_MATKHAU = ?, is_delete = 0 
                              WHERE CKT_SODT = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->execute([$ten, $gioitinh, $matkhau, $sdt]);
            } else {
                // Nếu số điện thoại chưa tồn tại, thêm mới
                $sqlInsert = "INSERT INTO chu_khu_tro (CKT_SODT, CKT_HOTEN, CKT_GIOITINH, CKT_MATKHAU, is_delete)
                              VALUES (?, ?, ?, ?, 0)";
                $stmtInsert = $conn->prepare($sqlInsert);
                $stmtInsert->execute([$sdt, $ten, $gioitinh, $matkhau]);
            }
            // Chuyển hướng về trang đăng nhập với thông báo thành công
            header("Location: dangnhap.php?status=success&message=Đăng ký thành công, vui lòng đăng nhập.");
            exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký Chủ Khu Trọ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Đăng ký Chủ Khu Trọ</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="ten" class="form-label">Họ và Tên</label>
                            <input type="text" class="form-control" id="ten" name="ten" placeholder="Nhập họ và tên" required
                                   pattern="[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯăâếệîôớèý\s]+"
                                   title="Tên chỉ được chứa chữ cái và khoảng trắng">
                        </div>
                        <div class="mb-3">
                            <label for="sdt" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="sdt" name="sdt" placeholder="Nhập số điện thoại" required
                                   pattern="\d{10}"
                                   title="Số điện thoại phải gồm 10 chữ số">
                        </div>
                        <div class="mb-3">
                            <label for="gioitinh" class="form-label">Giới tính</label>
                            <select class="form-control" id="gioitinh" name="gioitinh" required>
                                <option value="">Chọn giới tính</option>
                                <option value="nam">Nam</option>
                                <option value="nữ">Nữ</option>
                                <option value="khác">Khác</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="matkhau" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="matkhau" name="matkhau" placeholder="Nhập mật khẩu" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                        <div class="mt-3 text-center">
                            <a href="dangnhap.php">Quay lại Đăng nhập</a>
                        </div>
                    </form>
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
<?php include('/xampp/htdocs/HTQLNT/HeaderFooter/footer.php'); ?>
</body>
</html>
