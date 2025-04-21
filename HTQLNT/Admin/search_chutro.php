<?php
// search_chutro.php
include('../db_connect.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $sql = "SELECT * FROM chu_khu_tro 
            WHERE CKT_HOTEN LIKE ? 
               OR CKT_SODT LIKE ? 
               OR CKT_GIOITINH LIKE ? 
            ORDER BY CKT_SODT DESC";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
} else {
    $sql = "SELECT * FROM chu_khu_tro WHERE is_delete = 0 ORDER BY CKT_SODT DESC";
    $stmt = $conn->query($sql);
}

$chutros = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($chutros as $chutro) {
    ?>
    <tr>
        <td><?php echo $chutro['CKT_HOTEN']; ?></td>
        <td><?php echo $chutro['CKT_SODT']; ?></td>
        <td><?php echo $chutro['CKT_GIOITINH']; ?></td>
        <td>
            <a href="edit_chutro.php?sdt=<?php echo $chutro['CKT_SODT']; ?>" class="btn btn-success btn-sm">Sửa</a>
            <a href="delete_chutro.php?sdt=<?php echo $chutro['CKT_SODT']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa chủ trọ này?');">Xóa</a>
        </td>
    </tr>
    <?php
}
?>