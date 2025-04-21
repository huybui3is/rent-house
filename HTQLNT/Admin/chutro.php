<?php
// chutro.php
include('../db_connect.php');

// Xử lý thêm chủ trọ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['them_chutro'])) {
    $ten = trim($_POST['ten']);
    $sdt = trim($_POST['sdt']);
    $gioitinh = trim($_POST['gioitinh']);
    $matkhau = trim($_POST['matkhau']);

    if (!empty($ten) && !empty($sdt) && !empty($gioitinh) && !empty($matkhau)) {
        // Kiểm tra số điện thoại đã tồn tại và đang hoạt động (is_delete = 0)
        $sqlCheckActive = "SELECT COUNT(*) FROM chu_khu_tro WHERE CKT_SODT = ? AND is_delete = 0";
        $stmtActive = $conn->prepare($sqlCheckActive);
        $stmtActive->execute([$sdt]);
        $activeCount = $stmtActive->fetchColumn();

        if ($activeCount > 0) {
            // Nếu có bản ghi đang hoạt động, báo lỗi
            $error_message = "Số điện thoại đã tồn tại, vui lòng nhập số khác.";
        } else {
            // Kiểm tra xem số điện thoại có tồn tại nhưng đã soft delete không (is_delete = 1)
            $sqlCheckDeleted = "SELECT COUNT(*) FROM chu_khu_tro WHERE CKT_SODT = ? AND is_delete = 1";
            $stmtDeleted = $conn->prepare($sqlCheckDeleted);
            $stmtDeleted->execute([$sdt]);
            $deletedCount = $stmtDeleted->fetchColumn();

            if ($deletedCount > 0) {
                // Nếu đã tồn tại bản ghi bị soft delete, cập nhật lại (reactivate) bản ghi đó
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
            header("Location: chutro.php?status=success&message=Thêm+chủ+trọ+thành+công");
            exit();
        }
    } else {
        $error_message = "Vui lòng nhập đầy đủ thông tin.";
    }
}

// Lấy danh sách chủ trọ ban đầu (chỉ lấy các bản ghi chưa xóa mềm)
$sql = "SELECT * FROM chu_khu_tro WHERE is_delete = 0 ORDER BY CKT_SODT DESC";
$stmt = $conn->query($sql);
$chutros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xác định số bản ghi mỗi trang
$limit = 10;
// Lấy số trang từ URL, nếu không có thì mặc định là 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
// Tính offset: vị trí bắt đầu của bản ghi cho trang hiện tại
$offset = ($page - 1) * $limit;

// Đếm tổng số bản ghi
$stmt = $conn->query("SELECT COUNT(*) FROM chu_khu_tro WHERE is_delete = 0");
$total_rows = $stmt->fetchColumn();
// Tính tổng số trang
$total_pages = ceil($total_rows / $limit);

// Lấy dữ liệu của trang hiện tại
$sql = "SELECT * FROM chu_khu_tro WHERE is_delete = 0 ORDER BY CKT_SODT DESC LIMIT $limit OFFSET $offset";
$stmt = $conn->query($sql);
$chutros = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Chủ Trọ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
/* Trạng thái bình thường */
.pagination .page-link {
    background-color: #fff; /* nền trắng */
    border-color: #198754;     /* viền trắng */
    color: #198754;         /* số và chữ màu xanh */
}

/* Hiệu ứng hover: chuyển nền xanh, chữ trắng */
.pagination .page-link:hover {
    background-color: #198754;
    border-color: #198754;
    color: #fff;
}

/* Trạng thái active: nền xanh, chữ trắng */
.pagination .active .page-link {
    background-color: #198754;
    border-color: #198754;
    color: #fff;
}
</style>


</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">Quản Lý Chủ Trọ</h2>
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success' && isset($_GET['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_GET['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
        
        <!-- Form Tìm kiếm: không cần nút submit -->
        <div class="card p-4 mb-4">
            <div class="row">
                <div class="col-md-12">
                    <input type="text" id="searchInput" class="form-control" placeholder="Nhập từ khóa tìm kiếm...">
                </div>
            </div>
        </div>

        <!-- Hiển thị thông báo lỗi -->
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>

        <!-- Form Thêm Chủ Trọ -->
        <!-- <div class="card p-4 mb-4">
            <h5 class="card-title" >Thêm Chủ Trọ</h5>
            <form method="POST" id="addForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="ten" class="form-control" placeholder="Tên chủ trọ" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="sdt" class="form-control" placeholder="Số điện thoại" required>
                    </div>
                    <div class="col-md-3">
                        <select name="gioitinh" class="form-control"  required>
                            <option value="">Giới tính</option>
                            <option value="nam">Nam</option>
                            <option value="nữ">Nữ</option>
                            <option value="khác">Khác</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="matkhau" class="form-control" placeholder="Mật khẩu" required>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" name="them_chutro" class="btn btn-success mt-2">Thêm chủ trọ</button>
                        
                        <button type="button" id="resetButton" class="btn btn-danger mt-2 ms-2" style="display:none;">Hủy</button>
                    </div>
                </div>
            </form>
        </div> -->

        <!-- Bảng Danh Sách Chủ Trọ -->
        <div class="card p-3">
            <h5 class="card-title">Danh Sách Chủ Trọ</h5>
            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>HỌ VÀ TÊN</th>
                        <th>SỐ ĐIỆN THOẠI</th>
                        <th>GIỚI TÍNH</th>
                        <th>THAO TÁC</th>
                    </tr>
                </thead>
                <tbody id="resultTable">
                    <?php foreach ($chutros as $chutro) { ?>
                        <tr>
                            <td><?php echo $chutro['CKT_HOTEN']; ?></td>
                            <td><?php echo $chutro['CKT_SODT']; ?></td>
                            <td><?php echo $chutro['CKT_GIOITINH']; ?></td>
                            <td>
                                <a href="edit_chutro.php?sdt=<?php echo $chutro['CKT_SODT']; ?>" class="btn btn-success btn-sm">Sửa</a>
                                <a href="delete_chutro.php?sdt=<?php echo $chutro['CKT_SODT']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa chủ trọ này?');">Xóa</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <!-- Nút trang trước -->
        <?php if($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="chutro.php?page=<?php echo $page - 1; ?>">Trước</a>
            </li>
        <?php endif; ?>

        <!-- Hiển thị số trang -->
        <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                <a class="page-link"  href="chutro.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Nút trang sau -->
        <?php if($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link"  href="chutro.php?page=<?php echo $page + 1; ?>">Tiếp</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

        <div class="text-center mt-4 mb-4">
            <a href="admin.php" class="btn btn-primary">Trang Chủ</a>
            <a href="viewmap_choropleth.php" class="btn btn-success">Xem Thống Kê</a>
        </div>
    </div>

    <!-- Script để xử lý live search -->
    <script>
        // Hàm debounce để giảm số lần gọi AJAX khi gõ quá nhanh
        function debounce(func, delay) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), delay);
            };
        }

        const searchInput = document.getElementById('searchInput');
        const resultTable = document.getElementById('resultTable');
            // Tham chiếu đến phần tử phân trang
    const paginationNav = document.querySelector('nav[aria-label="Page navigation"]');

        // Hàm thực hiện AJAX call
        function performSearch() {
            const query = searchInput.value;
            fetch('search_chutro.php?search=' + encodeURIComponent(query))
                .then(response => response.text())
                .then(data => {
                    resultTable.innerHTML = data;
                                    // Nếu có từ khóa tìm kiếm, ẩn phân trang, nếu không thì hiện phân trang
                if(query.length > 0) {
                    paginationNav.style.display = 'none';
                } else {
                    paginationNav.style.display = 'block';
                }
                })
                .catch(error => console.error('Error:', error));
        }

        // Sử dụng debounce (ví dụ delay 300ms)
        searchInput.addEventListener('keyup', debounce(performSearch, 300));
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>


// Lấy tham chiếu đến form và nút reset
const addForm = document.getElementById('addForm');
const resetButton = document.getElementById('resetButton');

// Hàm kiểm tra xem có ô nào có dữ liệu không
function checkFormFilled() {
    const inputs = addForm.querySelectorAll('input[type="text"], select');
    let filled = false;
    inputs.forEach(input => {
        if (input.value.trim() !== '') {
            filled = true;
        }
    });
    resetButton.style.display = filled ? 'inline-block' : 'none';
    return filled;
}

// Thêm event listener cho tất cả các trường input và select
addForm.querySelectorAll('input, select').forEach(input => {
    input.addEventListener('input', checkFormFilled);
    input.addEventListener('change', checkFormFilled);
});

// Xử lý sự kiện click cho nút "Hủy"
resetButton.addEventListener('click', function() {
    addForm.reset();
    checkFormFilled(); // Sau khi reset, ẩn nút Hủy
});
</script>
</body>
</html>
