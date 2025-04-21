<?php
include('/xampp/htdocs/HTQLNT/db_connect.php');
if (isset($_GET['xp_ma'])) {
    $xpMa = $_GET['xp_ma'];
    $sql = "SELECT DUONG_Ma, DUONG_Ten FROM DUONG WHERE XP_Ma = :xpMa";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':xpMa', $xpMa);
    $stmt->execute();
    $duongList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($duongList);
}
?>