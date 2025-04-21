<?php
$page_title = "Duyệt phiếu thuê";
include('/xampp/htdocs/HTQLNT/ChuTro/header.php');
include('/xampp/htdocs/HTQLNT/db_connect.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION["user"])) {
    header("Location: /HTQLNT/ChuTro/dangnhap.php");
    exit();
}

$message = "";
$messageType = "";
$cktSoDt = $_SESSION["user"]["CKT_SODT"];

// Lấy danh sách tất cả phiếu thuê
$limit = 10; // Số phiếu thuê hiển thị mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Truy vấn lấy dữ liệu có phân trang
$sql = "SELECT 
            pt.PT_MA, kh.KH_CCCD, kh.KH_TEN, kh.KH_SDT, 
            pt.PT_NGAYLAP, pt.PT_NGAYBATDAU, pt.PT_NGAYKETTHUC, pt.PT_tinhtrang,
            p.PHONG_MAPHONG, gt.GT_GIA, kt.KT_TENKHUTRO
        FROM phieu_thue pt
        INNER JOIN khach_hang kh ON pt.KH_CCCD = kh.KH_CCCD
        INNER JOIN phong p ON pt.PHONG_MAPHONG = p.PHONG_MAPHONG
        INNER JOIN gia_thue gt ON p.LP_MALOAIPHONG = gt.LP_MALOAIPHONG 
        INNER JOIN khu_tro kt ON p.KT_MAKT = kt.KT_MAKT AND kt.KT_MAKT = gt.KT_MAKT
        WHERE kt.ckt_sodt = $cktSoDt
        and pt.pt_tinhtrang <> 2
        and p.is_delete = 0
        ORDER BY pt.PT_ma asc
        LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$phieuThueList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlCount = "SELECT COUNT(*) AS total 
             FROM phieu_thue pt
             INNER JOIN phong p ON pt.PHONG_MAPHONG = p.PHONG_MAPHONG
             INNER JOIN khu_tro kt ON p.KT_MAKT = kt.KT_MAKT
             WHERE kt.ckt_sodt = :cktSoDt";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bindParam(':cktSoDt', $cktSoDt, PDO::PARAM_INT);
$stmtCount->execute();
$totalRows = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];


$totalPages = ceil($totalRows / $limit); // Tính tổng số trang

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Phiếu Thuê</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid mt-5 mb-3">
    <h2 class="text-center mb-4">Danh Sách Phiếu Thuê</h2>

<!-- Ô tìm kiếm và lọc trên cùng một hàng -->
<div class="row mb-3">
    <div class="col-md-3">
        <input type="text" id="searchBox" class="form-control" placeholder="🔍 Nhập CCCD hoặc SĐT...">
    </div>
    <div class="col-md-3">
        <select id="statusFilter" class="form-select">
            <option value="">📋 Tất cả trạng thái</option>
            <option value="0">⛔ Chưa duyệt</option>
            <option value="1">✅ Đã duyệt</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="text" id="roomFilter" class="form-control" placeholder="🏠 Nhập phòng...">
    </div>
    <div class="col-md-3">
        <input type="text" id="khuTroFilter" class="form-control" placeholder="🏢 Nhập khu trọ...">
    </div>
</div>





    <?php if (!empty($phieuThueList)): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Mã Phiếu</th>
                    <th>Khách Hàng</th>
                    <th>CCCD</th>
                    <th>SĐT</th>
                    <th>Ngày Lập</th>
                    <th>Phòng</th>
                    <th>Giá Thuê</th>
                    <th>Khu Trọ</th>
                    <th>Trạng Thái</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody id="phieuThueTable">
                <?php foreach ($phieuThueList as $phieu): ?>
                    <tr>
                        <td><?= htmlspecialchars($phieu["PT_MA"]) ?></td>
                        <td><?= htmlspecialchars($phieu["KH_TEN"]) ?></td>
                        <td><?= htmlspecialchars($phieu["KH_CCCD"]) ?></td>
                        <td><?= htmlspecialchars($phieu["KH_SDT"]) ?></td>
                        <td><?= htmlspecialchars($phieu["PT_NGAYLAP"]) ?></td>
                        <td><?= htmlspecialchars($phieu["PHONG_MAPHONG"]) ?></td>
                        <td><?= number_format($phieu["GT_GIA"], 0, ',', '.') ?> VND</td>
                        <td><?= htmlspecialchars($phieu["KT_TENKHUTRO"]) ?></td>
                        <td>
                            <?= $phieu["PT_tinhtrang"] == 0 
                                ? '<span class="badge bg-danger">Chưa duyệt</span>' 
                                : '<span class="badge bg-success">Đã duyệt</span>' ?>
                        </td>
                        <td>
    <a href="chitietpt_chutro.php?PT_MA=<?= htmlspecialchars($phieu['PT_MA']) ?>" class="btn btn-primary btn-sm">Xem Chi Tiết</a>
   

</td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-danger text-center">Không có phiếu thuê nào trong hệ thống!</div>
    <?php endif; ?>
    <div class="d-flex justify-content-center mt-3">
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">« Trước</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Sau »</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

    <div class="text-center mt-4">
        <a href="themkhutro.php" class="btn btn-secondary">Quay lại Trang Chủ</a>
    </div>
</div>

<script>
    // Lọc danh sách phiếu thuê theo CCCD hoặc SĐT
    document.getElementById("searchBox").addEventListener("keyup", filterTable);
document.getElementById("statusFilter").addEventListener("change", filterTable);
document.getElementById("roomFilter").addEventListener("keyup", filterTable);
document.getElementById("khuTroFilter").addEventListener("keyup", filterTable);

function filterTable() {
    let searchValue = document.getElementById("searchBox").value.toLowerCase();
    let selectedStatus = document.getElementById("statusFilter").value;
    let roomValue = document.getElementById("roomFilter").value.toLowerCase();
    let khuTroValue = document.getElementById("khuTroFilter").value.toLowerCase();
    
    let rows = document.querySelectorAll("#phieuThueTable tr");

    rows.forEach(row => {
        let cccd = row.cells[2].innerText.toLowerCase();
        let sdt = row.cells[3].innerText.toLowerCase();
        let status = row.cells[8].textContent.includes("Chưa duyệt") ? "0" : "1";
        let room = row.cells[5].innerText.toLowerCase().trim();
        let khuTro = row.cells[7].innerText.toLowerCase().trim();

        let matchSearch = cccd.includes(searchValue) || sdt.includes(searchValue);
        let matchStatus = selectedStatus === "" || selectedStatus === status;
        let matchRoom = room.includes(roomValue);
        let matchKhuTro = khuTro.includes(khuTroValue);

        row.style.display = (matchSearch && matchStatus && matchRoom && matchKhuTro) ? "" : "none";
    });
}






</script>


</body>
</html>
<?php include('../HeaderFooter/footer.php'); ?>
