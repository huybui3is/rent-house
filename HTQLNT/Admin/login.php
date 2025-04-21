<?php 
$page_title = "Trang Chủ";
include('/xampp/htdocs/HTQLNT/HeaderFooter/header.php'); 
session_start();
if (isset($_SESSION["username"])) {
    header("Location: admin.php"); // Nếu đã đăng nhập, chuyển sang admin.php
    exit();
}
include('../db_connect.php'); // Nhúng file kết nối cơ sở dữ liệu

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Dùng prepared statement để tránh SQL Injection
    $sql = "SELECT * FROM admin WHERE TaiKhoan = :username AND MatKhau = :password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $password);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION["username"] = $username;
        header("Location: admin.php");
        exit();
    } else {
        $error_message = "Tên đăng nhập hoặc mật khẩu không đúng.";
    }
}

// Đóng kết nối PDO bằng cách đặt biến về null
$conn = null;
?>

<div class="container-fluid">
        <div class="row p-5" style="background: url('../images/img5.jpeg') center/cover; height: 700px; display: flex; justify-content: center; align-items: center;">
            <div class="col-md-4 m-5 g-0" style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(5px); color: white; padding: 30px; border-radius: 10px;">
                <h2 class="text-center mb-4" style="font-family: Arial, Helvetica, sans-serif;">Quản Lý Hệ Thống</h2>

                <?php if (isset($error_message)) { ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php } ?>

                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Nhập tên đăng nhập">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Đăng nhập</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

<?php include('/xampp/htdocs/HTQLNT/HeaderFooter/footer.php'); ?>

