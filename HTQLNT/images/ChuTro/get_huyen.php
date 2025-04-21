<?php
include('/xampp/htdocs/HTQLNT/db_connect.php');

if (isset($_GET['ttp_ma'])) {
    $ttpMa = $_GET['ttp_ma'];
    try {
        $stmt = $conn->prepare("SELECT QH_Ma, QH_Ten FROM QUAN_HUYEN WHERE TTP_MaTinh = :ttpMa");
        $stmt->bindParam(':ttpMa', $ttpMa);
        $stmt->execute();
        $huyenList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($huyenList);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]); // Trả về lỗi nếu có
    }
} else {
    echo json_encode(['error' => 'Missing ttp_ma parameter']); // Trả về lỗi nếu thiếu tham số
}
?>