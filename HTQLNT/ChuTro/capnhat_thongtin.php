<?php
// capnhatthongtin.php
session_start();
include('/xampp/htdocs/HTQLNT/db_connect.php');

if (!isset($_SESSION["user"])) {
    header("Location: dangnhap.php");
    exit();
}

$user = $_SESSION["user"];
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoten = trim($_POST['hoten']);
    $gioitinh = trim($_POST['gioitinh']);
    $matkhau = trim($_POST['matkhau']);

    if (empty($hoten) || empty($gioitinh) || empty($matkhau)) {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        // Cập nhật thông tin vào bảng chu_khu_tro (khóa chính là CKT_SoDT)
        $stmt = $conn->prepare("UPDATE CHU_KHU_TRO 
                                SET CKT_HOTEN = :hoten, CKT_GIOITINH = :gioitinh, CKT_MATKHAU = :matkhau 
                                WHERE CKT_SoDT = :sodt");
        $stmt->execute([
            ":hoten"    => $hoten,
            ":gioitinh" => $gioitinh,
            ":matkhau"  => $matkhau,
            ":sodt"     => $user['CKT_SODT']
        ]);
        if ($stmt->rowCount() > 0) {
            $success = "Cập nhật thông tin thành công!";
            // Cập nhật lại session nếu cần
            $_SESSION["user"]["CKT_HOTEN"] = $hoten;
            $_SESSION["user"]["CKT_GIOITINH"] = $gioitinh;
            $_SESSION["user"]["CKT_MATKHAU"] = $matkhau;
        } else {
            $error = "Không có thay đổi nào hoặc cập nhật thất bại!";
        }
    }
}
?>
<?php include('/xampp/htdocs/HTQLNT/ChuTro/header.php'); ?>
<div class="container mt-5 mb-5">
    <h2>Cập nhật thông tin cá nhân</h2>
    <?php if ($error): ?>
         <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
         <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="POST">
         <div class="mb-3">
             <label for="hoten" class="form-label">Họ và tên</label>
             <input type="text" name="hoten" id="hoten" class="form-control" value="<?= htmlspecialchars($user['CKT_HOTEN']) ?>" required>
         </div>
         <div class="mb-3">
             <label for="gioitinh" class="form-label">Giới tính</label>
             <select name="gioitinh" id="gioitinh" class="form-select" required>
                 <option value="nam" <?= strtolower($user['CKT_GIOITINH']) == 'nam' ? 'selected' : '' ?>>Nam</option>
                 <option value="nữ" <?= strtolower($user['CKT_GIOITINH']) == 'nữ' ? 'selected' : '' ?>>Nữ</option>
                 <option value="khác" <?= strtolower($user['CKT_GIOITINH']) == 'khác' ? 'selected' : '' ?>>Khác</option>
             </select>
         </div>
         <div class="mb-3">
             <label for="matkhau" class="form-label">Mật khẩu</label>
             <input type="password" name="matkhau" id="matkhau" class="form-control" value="<?= htmlspecialchars($user['CKT_MATKHAU']) ?>" required>
         </div>
         <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
    </form>
</div>
<?php include('/xampp/htdocs/HTQLNT/HeaderFooter/footer.php'); ?>
</body>
</html>
