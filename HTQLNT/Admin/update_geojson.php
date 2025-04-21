<?php
// update_geojson.php
// Kết nối CSDL (sử dụng PDO)
include('../db_connect.php');

// 1. Truy vấn số lượng khu trọ theo tên xã (XP_TEN)
$sql = "
    SELECT 
        xp.XP_TEN,
        COUNT(kt.KT_MAKT) AS dorm_count
    FROM xa_phuong xp
    LEFT JOIN duong d ON xp.XP_MA = d.XP_MA
    LEFT JOIN khu_tro kt ON d.DUONG_MA = kt.DUONG_MA AND kt.is_delete = 0
    GROUP BY xp.XP_TEN
";
$stmt = $conn->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Tạo mảng ánh xạ: XP_TEN => dorm_count
$countMap = [];
foreach ($results as $row) {
    // Chuẩn hóa tên: chuyển sang chữ thường và loại bỏ khoảng trắng thừa
    $xpTen = trim(strtolower($row['XP_TEN']));
    $countMap[$xpTen] = (int)$row['dorm_count'];
}

// 3. Đọc file GeoJSON (dữ liệu ranh giới hành chính từ OSM)
$geojsonFile = 'data/nhatro.geojson'; // Đường dẫn đến file GeoJSON đã tải từ Overpass Turbo
$geojsonStr = file_get_contents($geojsonFile);
$geojsonData = json_decode($geojsonStr, true);

// 4. Ghép dữ liệu số lượng khu trọ vào từng feature dựa trên thuộc tính "name"
// Chỉ xử lý những feature là relation (bỏ qua node dùng cho nhãn)
// if (isset($geojsonData['features'])) {
//     foreach ($geojsonData['features'] as &$feature) {
//         if (!isset($feature['properties']['@id']) || strpos($feature['properties']['@id'], 'relation/') !== 0) {
//             continue;
//         }
//         if (isset($feature['properties']['name'])) {
//             $osmName = trim(strtolower($feature['properties']['name']));
//             // Nếu cần, có thể loại bỏ tiền tố như "phường", "xã" để đối chiếu tốt hơn
//             // $osmName = preg_replace('/^(phường|xã)\s+/i', '', $osmName);
//             if (isset($countMap[$osmName])) {
//                 $feature['properties']['dorm_count'] = $countMap[$osmName];
//             } else {
//                 $feature['properties']['dorm_count'] = 0;
//             }
//         }
//     }
// }

// 4. Lọc và cập nhật feature
$filteredFeatures = [];
if (isset($geojsonData['features'])) {
    foreach ($geojsonData['features'] as $feature) {
        if (isset($feature['properties']['@id']) && strpos($feature['properties']['@id'], 'relation/') === 0) {
            if (isset($feature['properties']['name'])) {
                $osmName = trim(strtolower($feature['properties']['name']));
                // Nếu cần, loại bỏ tiền tố "phường" hoặc "xã" để so sánh tốt hơn
                // $osmName = preg_replace('/^(phường|xã)\s+/i', '', $osmName);
                
                if (isset($countMap[$osmName])) {
                    $feature['properties']['dorm_count'] = $countMap[$osmName];
                } else {
                    $feature['properties']['dorm_count'] = 0;
                }
            }
            $filteredFeatures[] = $feature;
        }
    }
}
$geojsonData['features'] = $filteredFeatures;

// 5. Xuất dữ liệu GeoJSON đã được cập nhật
header('Content-Type: application/json; charset=utf-8');
echo json_encode($geojsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
