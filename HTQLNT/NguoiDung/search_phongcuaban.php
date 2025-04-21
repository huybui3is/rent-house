<?php
include('../db_connect.php');
include('../HeaderFooter/header.php');

$input = $_POST["input"] ?? $_GET["input"] ?? "";
if (!empty($input)) {
  

    // Lấy danh sách tất cả phiếu thuê của khách hàng
    $sql = "SELECT pt.PT_MA, pt.PT_NGAYLAP, pt.PT_NGAYBATDAU, pt.PT_NGAYKETTHUC, pt.PT_tinhtrang,
                   p.PHONG_MAPHONG, p.PHONG_MOTA, gt.GT_GIA, kt.KT_TENKHUTRO
            FROM phieu_thue pt
            INNER JOIN khach_hang kh ON pt.KH_CCCD = kh.KH_CCCD
            INNER JOIN phong p ON pt.PHONG_MAPHONG = p.PHONG_MAPHONG
            INNER JOIN gia_thue gt ON p.LP_MALOAIPHONG = gt.LP_MALOAIPHONG 
            INNER JOIN khu_tro kt ON p.KT_MAKT = kt.KT_MAKT AND kt.KT_MAKT = gt.KT_MAKT
            and p.is_delete = 0
            WHERE (kh.KH_CCCD = :input OR kh.KH_SDT = :input)";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":input", $input, PDO::PARAM_STR);
    $stmt->execute();
    $phieuThueList = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Phiếu Thuê</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid mt-5 mb-4">
    <h2 class="text-center mb-4">Danh Sách Phiếu Thuê</h2>

    <?php if (!empty($phieuThueList)): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Mã Phiếu</th>
                    <th>Ngày Lập</th>
                    <th>Ngày Bắt Đầu</th>
                    <th>Ngày Kết Thúc</th>
                    <th>Phòng</th>
                    <th>Giá Thuê</th>
                    <th>Khu Trọ</th>
                    <th>Trạng Thái</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($phieuThueList as $phieu): ?>
                    <tr>
                        <td><?= htmlspecialchars($phieu["PT_MA"]) ?></td>
                        <td><?= htmlspecialchars($phieu["PT_NGAYLAP"]) ?></td>
                        <td><?= htmlspecialchars($phieu["PT_NGAYBATDAU"]) ?></td>
                        <td><?= htmlspecialchars($phieu["PT_NGAYKETTHUC"]) ?></td>
                        <td><?= htmlspecialchars($phieu["PHONG_MAPHONG"]) ?></td>
                        <td><?= number_format($phieu["GT_GIA"], 0, ',', '.') ?> VND</td>
                        <td><?= htmlspecialchars($phieu["KT_TENKHUTRO"]) ?></td>
                        <td>
                            <?= $phieu["PT_tinhtrang"] == 0 
                                ? '<span class="badge bg-danger">Chưa duyệt</span>' 
                                : '<span class="badge bg-success">Đã duyệt</span>' ?>
                        </td>
                        <td>
                        <a href="chitietphieuthue.php?PT_MA=<?= htmlspecialchars($phieu['PT_MA']) ?>&input=<?= urlencode($input) ?>" class="btn btn-primary">
    Xem Chi Tiết
</a>


                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-danger text-center">Không tìm thấy phiếu thuê nào!</div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="phongcuaban.php" class="btn btn-secondary">Quay lại</a>
    </div>
</div>
</body>
</html>
<?php include('../HeaderFooter/footer.php'); ?>
