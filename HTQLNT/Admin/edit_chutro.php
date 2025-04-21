<?php
// Kết nối CSDL
include('../db_connect.php');

if (!isset($_GET['sdt'])) {
    header("Location: chutro.php");
    exit();
}

$sdt = $_GET['sdt'];

// Lấy thông tin chủ trọ cần sửa
$sql = "SELECT * FROM chu_khu_tro WHERE CKT_SODT = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$sdt]);
$chutro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chutro) {
    echo "Không tìm thấy chủ trọ với số điện thoại: " . htmlspecialchars($sdt);
    exit();
}

// Hàm tạo mật khẩu ngẫu nhiên gồm 6 ký tự
function generateRandomPassword($length = 6) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle($chars), 0, $length);
}

// Xử lý cập nhật thông tin chủ trọ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Xử lý cập nhật thông tin chủ trọ (tên, giới tính)
    if (isset($_POST['capnhat_chutro'])) {
        $ten = $_POST['ten'];
        $gioitinh = $_POST['gioitinh'];
    
        if (!empty($ten) && !empty($gioitinh)) {
             $sql = "UPDATE chu_khu_tro SET CKT_HOTEN = ?, CKT_GIOITINH = ? WHERE CKT_SODT = ?";
             $stmt = $conn->prepare($sql);
             $stmt->execute([$ten, $gioitinh, $sdt]);
             header("Location: chutro.php?status=success&message=Sửa+chủ+trọ+thành+công");
             exit();
        } else {
             $error_message = "Vui lòng nhập đầy đủ thông tin.";
        }
    } 
    // Xử lý cấp lại mật khẩu
    else if (isset($_POST['reset_pass'])) {
        $newPassword = generateRandomPassword(6);
        $sql = "UPDATE chu_khu_tro SET CKT_MATKHAU = ? WHERE CKT_SODT = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$newPassword, $sdt]);
        $success_message = "Mật khẩu đã được cấp lại: " . $newPassword;
        // Cập nhật lại thông tin chủ trọ sau khi reset mật khẩu nếu cần
        $sql = "SELECT * FROM chu_khu_tro WHERE CKT_SODT = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$sdt]);
        $chutro = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Chủ Trọ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">Sửa Chủ Trọ</h2>

        <!-- Hiển thị thông báo lỗi -->
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>
        <?php if (isset($success_message)) { ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php } ?>

        <div class="card p-4">
            <form method="POST">
                <div class="mb-3">
                    <label for="ten" class="form-label">Tên chủ trọ</label>
                    <input type="text" name="ten" id="ten" class="form-control" value="<?php echo htmlspecialchars($chutro['CKT_HOTEN']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="gioitinh" class="form-label">Giới tính</label>
                    <select name="gioitinh" id="gioitinh" class="form-control" required>
                        <option value="Nam" <?php echo ($chutro['CKT_GIOITINH'] == 'Nam' ? 'selected' : ''); ?>>Nam</option>
                        <option value="Nữ" <?php echo ($chutro['CKT_GIOITINH'] == 'Nữ' ? 'selected' : ''); ?>>Nữ</option>
                        <option value="Khác" <?php echo ($chutro['CKT_GIOITINH'] == 'Khác' ? 'selected' : ''); ?>>Khác</option>
                    </select>
                </div>
                <div class="text-center mb-3">
                    <button type="submit" name="capnhat_chutro" class="btn btn-primary">Cập nhật</button>
                </div>
                <hr>
                <div class="text-center">
                    <button type="submit" name="reset_pass" class="btn btn-warning">Cấp lại mật khẩu</button>
                </div>
            </form>
        </div>
        <div class="text-center mt-3">
            <a href="chutro.php" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
