<?php
// live_search.php
include('../db_connect.php');

$search = isset($_GET['query']) ? $_GET['query'] : '';

// Tạo câu truy vấn với điều kiện tìm kiếm nếu có
$sql = "SELECT t.*, d.DUONG_TEN 
        FROM truong t 
        LEFT JOIN duong d ON t.DUONG_MA = d.DUONG_MA";
if ($search != '') {
    $sql .= " WHERE t.TRUONG_MA LIKE :search OR t.TRUONG_TEN LIKE :search OR t.TRUONG_SODIACHI LIKE :search";
}
$sql .= " ORDER BY t.TRUONG_MA DESC";

$stmt = $conn->prepare($sql);
if ($search != '') {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}
$truongs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Trả về các dòng <tr> tương ứng
foreach ($truongs as $truong) {
    ?>
    <tr>
        <td><?php echo $truong['TRUONG_MA']; ?></td>
        <td><?php echo $truong['TRUONG_TEN']; ?></td>
        <td><?php echo $truong['TRUONG_SODIACHI']; ?></td>
        <td><?php echo $truong['TRUONG_LATITUDE']; ?></td>
        <td><?php echo $truong['TRUONG_LONGTITUDE']; ?></td>
        <td>
          <a href="edit_truong.php?ma_truong=<?php echo $truong['TRUONG_MA']; ?>" class="btn btn-primary btn-sm">Sửa</a>
          <a href="delete_truong.php?ma_truong=<?php echo $truong['TRUONG_MA']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa trường này không?');">Xóa</a>
        </td>
    </tr>
    <?php
}
?>
