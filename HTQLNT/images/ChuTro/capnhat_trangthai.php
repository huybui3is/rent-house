<?php
include('/xampp/htdocs/HTQLNT/db_connect.php');
header("Content-Type: application/json");

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["PT_MA"])) {
    $pt_ma = $_POST["PT_MA"];

    // Kiểm tra phiếu thuê có tồn tại không
    $checkStmt = $conn->prepare("SELECT PT_tinhtrang FROM phieu_thue WHERE PT_MA = ?");
    $checkStmt->execute([$pt_ma]);
    $currentStatus = $checkStmt->fetchColumn();

    if ($currentStatus !== false) {
        // Đảo trạng thái
        $newStatus = $currentStatus == 1 ? 0 : 1;
        $updateStmt = $conn->prepare("UPDATE phieu_thue SET PT_tinhtrang = ? WHERE PT_MA = ?");
        $updateStmt->execute([$newStatus, $pt_ma]);

        if ($updateStmt->rowCount() > 0) {
            $response = ["success" => true, "newStatus" => $newStatus];
        }
    }
}

echo json_encode($response);
?>
