<?php
$page_title = "Tra cứu phòng của bạn";
include('../HeaderFooter/header.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra Cứu Phòng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Thiết lập hình nền */
        .background {
            background: url('../images/bg_room.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            padding: 50px 0;
        }
        /* Lớp overlay */
        .overlay {
            background: rgba(0, 0, 0, 0.5);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 15px;
        }
        /* Hộp nhập liệu */
        .search-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="background d-flex justify-content-center align-items-center">
            <div class="position-relative w-50">
                <div class="overlay"></div>
                <div class="search-box text-center">
                    <h3 class="mb-3 text-primary">Tra Cứu Phòng</h3>
                    <form action="search_phongcuaban.php" method="POST">
                        <div class="mb-3">
                            <label for="search_value" class="form-label">Nhập CCCD hoặc SĐT:</label>
                            <input type="text" class="form-control" id="search_value" name="input" placeholder="Nhập CCCD hoặc số điện thoại" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Tra Cứu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include('../HeaderFooter/footer.php'); ?>
</body>
</html>
