<?php
include('/xampp/htdocs/HTQLNT/db_connect.php');
if (isset($_GET['qh_ma'])) {
    $qhMa = $_GET['qh_ma'];
    $sql = "SELECT XP_Ma, XP_Ten FROM XA_PHUONG WHERE QH_Ma = :qhMa";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':qhMa', $qhMa);
    $stmt->execute();
    $xaPhuongList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($xaPhuongList);
}
?>

