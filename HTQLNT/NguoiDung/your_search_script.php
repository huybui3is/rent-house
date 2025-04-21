<?php
$page_title = "Tìm trọ";
include('../HeaderFooter/header.php');
include('../db_connect.php'); // file kết nối CSDL

// Số lượng phòng hiển thị mỗi trang
$rooms_per_page = 5; 

// Xác định trang hiện tại từ URL (nếu có) hoặc mặc định là 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rooms_per_page;

// Biến kiểm tra xem form tìm kiếm đã được submit hay chưa
$searchTriggered = isset($_GET['submit']);

// Khởi tạo các biến tìm kiếm, bao gồm biến address (địa chỉ chi tiết được reverse geocoding)
$owner_search    = "";
$khu_tro_search  = "";
$latitude        = "";
$longitude       = "";
$distance_search = "";
$max_people      = "";
$max_price       = "";
$address         = "";
$warning         = "";

if ($searchTriggered) {
    $owner_search    = isset($_GET['owner-search']) ? trim($_GET['owner-search']) : '';
    $khu_tro_search  = isset($_GET['khu-tro-search']) ? trim($_GET['khu-tro-search']) : '';
    $latitude        = isset($_GET['latitude']) ? trim($_GET['latitude']) : '';
    $longitude       = isset($_GET['longitude']) ? trim($_GET['longitude']) : '';
    $distance_search = isset($_GET['distance-search']) ? trim($_GET['distance-search']) : '';
    $max_people      = isset($_GET['max-people']) ? trim($_GET['max-people']) : '';
    $max_price       = isset($_GET['max-price']) ? trim($_GET['max-price']) : '';
    $address         = isset($_GET['address']) ? trim($_GET['address']) : '';
    
    // Nếu submit form mà không nhập thông tin nào thì thông báo
    if (empty($owner_search) && empty($khu_tro_search) && empty($latitude) && empty($longitude) 
        && empty($distance_search) && empty($max_people) && empty($max_price)) {
        $warning = "Vui lòng nhập ít nhất một thông tin tìm kiếm.";
    }
}

// Xây dựng mệnh đề WHERE cơ bản (lấy các phòng có trạng thái hợp lệ)
$where = "WHERE f.TTP_MA = '01' AND f.LS_NGAYKETTHUC IS NULL AND e.is_delete = 0";
$params = [];

// Áp dụng bộ lọc tìm kiếm nếu có nhập thông tin
if ($searchTriggered && !empty($owner_search)) {
    $where .= " AND a.CKT_HOTEN LIKE :owner";
    $params[':owner'] = "%" . $owner_search . "%";
}
if ($searchTriggered && !empty($khu_tro_search)) {
    $where .= " AND b.KT_TENKHUTRO LIKE :khu_tro";
    $params[':khu_tro'] = "%" . $khu_tro_search . "%";
}
// Bỏ qua lọc theo khoảng cách bằng Haversine ở đây
if ($searchTriggered && !empty($max_people)) {
    $where .= " AND d.LP_SUCCHUA = :max_people";
    $params[':max_people'] = $max_people;
}
if ($searchTriggered && !empty($max_price)) {
    $where .= " AND c.GT_GIA = :max_price";
    $params[':max_price'] = $max_price;
}

// Lấy tổng số phòng thoả mãn điều kiện
$total_sql = "SELECT COUNT(*) FROM chu_khu_tro a
    JOIN khu_tro b ON a.CKT_SODT = b.CKT_SODT
    JOIN gia_thue c ON b.KT_MAKT = c.KT_MAKT
    JOIN loai_phong d ON c.LP_MALOAIPHONG = d.LP_MALOAIPHONG
    JOIN phong e ON b.KT_MAKT = e.KT_MAKT AND e.LP_MALOAIPHONG = d.LP_MALOAIPHONG
    JOIN lich_su f ON e.PHONG_MAPHONG = f.PHONG_MAPHONG
    JOIN tinh_trang_phong g ON f.TTP_MA = g.TTP_MA
    JOIN duong h ON b.DUONG_MA = h.DUONG_MA
    JOIN xa_phuong i ON h.XP_MA = i.XP_MA
    JOIN quan_huyen j ON i.QH_MA = j.QH_MA
    JOIN tinh_thanh_pho k ON j.TTP_MATINH = k.TTP_MATINH
    $where";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute($params);
$total_rooms = $total_stmt->fetchColumn();
$total_pages = ceil($total_rooms / $rooms_per_page);

// Truy vấn các phòng theo bộ lọc và phân trang (không lọc khoảng cách tại SQL)
$sql = "
    SELECT 
        e.PHONG_MAPHONG AS MA_PHONG, 
        d.LP_TENLOAIPHONG AS LOAI_PHONG,
        b.KT_TENKHUTRO AS TEN_KHU_TRO, 
        c.GT_GIA AS GIA_1_THANG, 
        h.DUONG_TEN AS DUONG, 
        i.XP_TEN AS XA_PHUONG, 
        j.QH_TEN AS QUAN_HUYEN, 
        k.TTP_TEN AS TINH_THANH_PHO,
        b.KT_LATITUDE AS KT_LATITUDE,
        b.KT_SONHA AS SO_NHA,
        b.KT_LONGTITUDE AS KT_LONGTITUDE,
        a.CKT_HOTEN AS CHU_KHU_TRO,
        d.LP_SUCCHUA AS SUC_CHUA
    FROM 
        chu_khu_tro a
    JOIN 
        khu_tro b ON a.CKT_SODT = b.CKT_SODT
    JOIN 
        gia_thue c ON b.KT_MAKT = c.KT_MAKT
    JOIN 
        loai_phong d ON c.LP_MALOAIPHONG = d.LP_MALOAIPHONG
    JOIN 
        phong e ON b.KT_MAKT = e.KT_MAKT AND e.LP_MALOAIPHONG = d.LP_MALOAIPHONG
    JOIN 
        lich_su f ON e.PHONG_MAPHONG = f.PHONG_MAPHONG
    JOIN 
        tinh_trang_phong g ON f.TTP_MA = g.TTP_MA
    JOIN 
        duong h ON b.DUONG_MA = h.DUONG_MA
    JOIN 
        xa_phuong i ON h.XP_MA = i.XP_MA
    JOIN 
        quan_huyen j ON i.QH_MA = j.QH_MA
    JOIN 
        tinh_thanh_pho k ON j.TTP_MATINH = k.TTP_MATINH
    $where
    LIMIT :rooms_per_page OFFSET :offset
";
$stmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':rooms_per_page', $rooms_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lọc theo khoảng cách lái xe (sử dụng API OSRM) nếu có nhập tọa độ và khoảng cách
if ($searchTriggered && !empty($latitude) && !empty($longitude) && !empty($distance_search)) {
    $filteredRooms = [];
    foreach ($rooms as $room) {
        if (!empty($room['KT_LATITUDE']) && !empty($room['KT_LONGTITUDE'])) {
            // Lấy khoảng cách lái xe từ API OSRM (tham số theo thứ tự: kinh, vĩ)
            $osrm_url = "http://router.project-osrm.org/route/v1/driving/{$longitude},{$latitude};{$room['KT_LONGTITUDE']},{$room['KT_LATITUDE']}?overview=false";
            $response = @file_get_contents($osrm_url);
            if ($response !== false) {
                $data = json_decode($response, true);
                if (isset($data['routes'][0]['distance'])) {
                    // OSRM trả về khoảng cách theo mét, chuyển sang km
                    $drivingDistance = $data['routes'][0]['distance'] / 1000;
                    $room['driving_distance'] = $drivingDistance;
                    if ($drivingDistance <= $distance_search) {
                        $filteredRooms[] = $room;
                    }
                }
            }
        }
    }
    // Cập nhật lại danh sách phòng dựa trên khoảng cách lái xe thực tế
    $rooms = $filteredRooms;
}
?>

<div class="container-fluid">
    <div class="row p-3">
        <div class="col-12 text-center">
            <h2 class="display-4 text-primary">Tìm kiếm nhà trọ và khu trọ</h2>
            <p class="lead text-muted">Tìm kiếm chỗ ở mà bạn mong muốn với giá cả hợp lý, phù hợp với nhu cầu ở mọi nơi.</p>
        </div>
    </div>
    
    <!-- Bộ lọc tìm kiếm -->
    <div class="row">
        <h4 class="mb-4 text-primary">Tìm kiếm vị trí</h4>
        <!-- Form submit vào chính trang hiện tại -->
        <form method="GET" action="<?= $_SERVER['PHP_SELF'] ?>" class="needs-validation" novalidate>
            <div class="row">
                <div class="col">
                    <div class="form-group mb-3 mt-2">
                        <label for="owner-search" class="h5">Tìm theo tên chủ trọ:</label>
                        <input type="text" class="form-control" id="owner-search" name="owner-search" placeholder="Nhập tên chủ trọ (Không bắt buộc)" value="<?= htmlspecialchars($owner_search) ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group mb-3 mt-2">
                        <label for="khu-tro-search" class="h5">Tìm theo tên khu trọ:</label>
                        <input type="text" class="form-control" id="khu-tro-search" name="khu-tro-search" placeholder="Nhập tên khu trọ (Không bắt buộc)" value="<?= htmlspecialchars($khu_tro_search) ?>">
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <div class="row align-items-center">
                    <div class="col">
                        <label class="h5">Chọn điểm trên bản đồ:</label>
                    </div>
                    <div class="col-10 text-start">
                        <button type="button" class="btn btn-secondary" id="choose-location">Chọn điểm</button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-2">
                        <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Vĩ độ" readonly value="<?= htmlspecialchars($latitude) ?>">
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Kinh độ" readonly value="<?= htmlspecialchars($longitude) ?>">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="address" name="address" placeholder="Địa chỉ chi tiết" readonly value="<?= htmlspecialchars($address) ?>">
                    </div>
                </div>
                <!-- Ô hiển thị địa chỉ chi tiết (nếu có)
                <div class="row mt-2">
                    <div class="col">
                        <input type="text" class="form-control" id="address" name="address" placeholder="Địa chỉ chi tiết" readonly value="<?= htmlspecialchars($address) ?>">
                    </div>
                </div> -->
            </div>
            <div class="form-group mb-3">
                <label for="distance-search" class="h5">Khoảng cách tới điểm được chọn (km):</label>
                <input type="number" class="form-control mt-2" id="distance-search" name="distance-search" placeholder="Nhập khoảng cách" min="1" value="<?= htmlspecialchars($distance_search) ?>">
            </div>
            <!-- Các tiêu chí bổ sung -->
            <div class="row">
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="max-people" class="h5">Số người ở tối đa:</label>
                        <input type="number" class="form-control" id="max-people" name="max-people" placeholder="Nhập số người tối đa" value="<?= htmlspecialchars($max_people) ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="max-price" class="h5">Giá thuê tối đa:</label>
                        <input type="number" class="form-control" id="max-price" name="max-price" placeholder="Nhập giá thuê tối đa" min="1" value="<?= htmlspecialchars($max_price) ?>">
                    </div>
                </div>
            </div>
            <button type="submit" name="submit" class="btn btn-primary btn-lg mt-3 w-100">Tìm kiếm</button>
        </form>

        <?php if (!empty($warning)): ?>
            <div class="alert alert-warning mt-3"><?= $warning ?></div>
        <?php endif; ?>

        <!-- Phần hiển thị bản đồ (giữ nguyên chức năng chọn vị trí) -->
        <div id="overlay" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index:2000;"></div>
        <div id="map-container" style="display:none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width:800px; height:600px; background:white; z-index:2001; box-shadow: 0 4px 6px rgba(0,0,0,0.3); overflow:hidden; border-radius:8px;">
            <button id="close-map" class="btn btn-primary" style="position:absolute; top:10px; right:10px; font-size:20px; cursor:pointer; z-index:2002;">X</button>
            <div id="small-map" style="width:100%; height:100%;"></div>
        </div>
        
        <script>
            // Code cho modal map
            let modalMap;
            let chosenLatLng;
            function initModalMap() {
                modalMap = L.map('small-map').setView([10.0301, 105.7792], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);
                modalMap.on('click', function(e) {
                    chosenLatLng = e.latlng;
                    // Xóa marker cũ nếu có
                    modalMap.eachLayer(function(layer) {
                        if(layer instanceof L.Marker) {
                            modalMap.removeLayer(layer);
                        }
                    });
                    L.marker(chosenLatLng).addTo(modalMap)
                        .bindPopup("Bạn đã chọn điểm này")
                        .openPopup();
                    document.getElementById('latitude').value = chosenLatLng.lat;
                    document.getElementById('longitude').value = chosenLatLng.lng;

                    // Gọi API reverse geocoding của Nominatim để lấy địa chỉ chi tiết
                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + chosenLatLng.lat + '&lon=' + chosenLatLng.lng)
                        .then(response => response.json())
                        .then(data => {
                            if(data && data.display_name) {
                                document.getElementById('address').value = data.display_name;
                            } else {
                                document.getElementById('address').value = "Không xác định được địa chỉ";
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            document.getElementById('address').value = "Có lỗi khi lấy địa chỉ";
                        });

                    // Cập nhật marker màu đỏ trên map chính (map1)
                    if (window.map1) {
                        if (window.chosenMarker) {
                            window.map1.removeLayer(window.chosenMarker);
                        }
                        window.chosenMarker = L.marker(chosenLatLng, { icon: window.redIcon }).addTo(window.map1)
                            .bindPopup("Vị trí bạn chọn");
                    }
                    closeModalMap();
                });
            }
            document.getElementById('choose-location').addEventListener('click', function() {
                document.getElementById('overlay').style.display = 'block';
                document.getElementById('map-container').style.display = 'block';
                initModalMap(); // Khởi tạo modal map
            });
            document.getElementById('close-map').addEventListener('click', function() {
                closeModalMap();
            });
            document.getElementById('overlay').addEventListener('click', function() {
                closeModalMap();
            });
            function closeModalMap() {
                document.getElementById('overlay').style.display = 'none';
                document.getElementById('map-container').style.display = 'none';
                if (modalMap) {
                    modalMap.remove();
                }
            }
        </script>
    </div>

    <!-- Danh sách kết quả -->
    <div class="row">
        <div class="col">
            <h3 class="text-center text-primary mt-4 mb-4">Danh sách phòng trọ</h3>
            <div class="list-group" style="min-height: 850px;">
                <?php 
                $stt = $offset + 1;
                foreach ($rooms as $row): ?>
                    <div class="list-group-item list-group-item-action mb-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-1">
                                <span class="badge bg-primary me-2" style="font-size: 1rem;"><?= $stt ?></span>
                                <?= htmlspecialchars($row['TEN_KHU_TRO']) ?>
                            </h5>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($row['LOAI_PHONG']) ?></span>
                        </div>
                        <p class="mb-1">
                            <strong>Chủ khu trọ:</strong> <?= htmlspecialchars($row['CHU_KHU_TRO']) ?>
                        </p>
                        <p class="mb-1">
                            <strong>Mã phòng:</strong> <?= htmlspecialchars($row['MA_PHONG']) ?> <br>
                            <strong>Giá thuê:</strong> <?= number_format($row['GIA_1_THANG'], 0, ',', '.') ?> VNĐ
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-0">
                                <strong>Địa chỉ:</strong>
                                <?= htmlspecialchars($row['SO_NHA']) ?>,
                                <?= htmlspecialchars($row['DUONG']) ?>, 
                                <?= htmlspecialchars($row['XA_PHUONG']) ?>, 
                                <?= htmlspecialchars($row['QUAN_HUYEN']) ?>, 
                                <?= htmlspecialchars($row['TINH_THANH_PHO']) ?>
                            </p>
                            <div>
                                <a href="chitietphong.php?maphong=<?= $row['MA_PHONG'] ?>" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                                <button type="button" class="btn btn-success btn-sm direction-btn"
                                    data-lat="<?= htmlspecialchars($row['KT_LATITUDE']) ?>"
                                    data-lng="<?= htmlspecialchars($row['KT_LONGTITUDE']) ?>"
                                    data-name="<?= htmlspecialchars($row['TEN_KHU_TRO']) ?>"
                                    data-address="<?= htmlspecialchars($row['SO_NHA'] . ', ' . $row['DUONG'] . ', ' . $row['XA_PHUONG'] . ', ' . $row['QUAN_HUYEN'] . ', ' . $row['TINH_THANH_PHO']) ?>">
                                    Chỉ đường
                                </button>
                            </div>
                        </div>
                    </div>
                <?php 
                    $stt++;
                endforeach; ?>
            </div>

            <!-- Phân trang, giữ lại các tham số tìm kiếm (bao gồm address nếu có) -->
            <div class="row mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                            <a class="page-link btn btn-primary" href="?page=1<?= ($searchTriggered ? '&owner-search='.$owner_search.'&khu-tro-search='.$khu_tro_search.'&latitude='.$latitude.'&longitude='.$longitude.'&distance-search='.$distance_search.'&max-people='.$max_people.'&max-price='.$max_price.'&address='.$address.'&submit=1' : '') ?>">First</a>
                        </li>
                        <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                            <a class="page-link btn btn-primary" href="?page=<?= max(1, $page - 1) ?><?= ($searchTriggered ? '&owner-search='.$owner_search.'&khu-tro-search='.$khu_tro_search.'&latitude='.$latitude.'&longitude='.$longitude.'&distance-search='.$distance_search.'&max-people='.$max_people.'&max-price='.$max_price.'&address='.$address.'&submit=1' : '') ?>">«</a>
                        </li>
                        <?php
                        $range = 2;
                        $start = max(1, $page - $range);
                        $end = min($total_pages, $page + $range);
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link btn btn-primary" href="?page=<?= $i ?><?= ($searchTriggered ? '&owner-search='.$owner_search.'&khu-tro-search='.$khu_tro_search.'&latitude='.$latitude.'&longitude='.$longitude.'&distance-search='.$distance_search.'&max-people='.$max_people.'&max-price='.$max_price.'&address='.$address.'&submit=1' : '') ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link btn btn-primary" href="?page=<?= min($total_pages, $page + 1) ?><?= ($searchTriggered ? '&owner-search='.$owner_search.'&khu-tro-search='.$khu_tro_search.'&latitude='.$latitude.'&longitude='.$longitude.'&distance-search='.$distance_search.'&max-people='.$max_people.'&max-price='.$max_price.'&address='.$address.'&submit=1' : '') ?>">»</a>
                        </li>
                        <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link btn btn-primary" href="?page=<?= $total_pages ?><?= ($searchTriggered ? '&owner-search='.$owner_search.'&khu-tro-search='.$khu_tro_search.'&latitude='.$latitude.'&longitude='.$longitude.'&distance-search='.$distance_search.'&max-people='.$max_people.'&max-price='.$max_price.'&address='.$address.'&submit=1' : '') ?>">Last</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Hiển thị bản đồ chính -->
        <div class="col">
            <div class="row p-3">
                <div id="map" style="width: 100%; height: 990px;">
                    <style>
                        #map .leaflet-control-zoom {
                            z-index: 500 !important;
                        }
                    </style>
                    <script>
                        var mapOptions = {
                            center: [10.0279603, 105.7664918],
                            zoom: 10
                        };
                        if (document.getElementById('map')) {
                            var map1 = new L.map('map', mapOptions);
                            var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
                            map1.addLayer(layer);
                                                                    
                            window.map1 = map1;

                            // Định nghĩa icon marker màu cam để hiển thị vị trí được chọn
                            window.redIcon = new L.Icon({
                                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
                                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34],
                                shadowSize: [41, 41]
                            });

                            // Nếu có tọa độ được lưu, hiển thị marker trên bản đồ
                            var latInput = document.getElementById('latitude').value;
                            var lngInput = document.getElementById('longitude').value;
                            if (latInput && lngInput) {
                                var savedLat = parseFloat(latInput);
                                var savedLng = parseFloat(lngInput);
                                window.chosenMarker = L.marker([savedLat, savedLng], { icon: window.redIcon }).addTo(map1)
                                    .bindPopup("Vị trí bạn chọn");
                            }

                            // Hiển thị marker cho các phòng trọ có tọa độ
                            var roomLocations = <?php echo json_encode($rooms); ?>;
                            roomLocations.forEach(function(room) {
                                if (room.KT_LATITUDE && room.KT_LONGTITUDE) {
                                    var marker = L.marker([room.KT_LATITUDE, room.KT_LONGTITUDE]).addTo(map1);
                                    var defaultPopupContent = "<strong>" + room.TEN_KHU_TRO + "</strong><br>" +
                                        room.DUONG + ", " +
                                        room.XA_PHUONG + ", " +
                                        room.QUAN_HUYEN + ", " +
                                        room.TINH_THANH_PHO;
                                    marker.bindPopup(defaultPopupContent);
                                }
                            });

                            // Xử lý sự kiện "Chỉ đường" cho các nút trong danh sách kết quả
                            document.querySelectorAll('.direction-btn').forEach(function(button) {
                                button.addEventListener('click', function() {
                                    var roomLat = parseFloat(this.getAttribute('data-lat'));
                                    var roomLng = parseFloat(this.getAttribute('data-lng'));
                                    var roomName = this.getAttribute('data-name');
                                    var roomAddress = this.getAttribute('data-address');

                                    var latInput = parseFloat(document.getElementById('latitude').value);
                                    var lngInput = parseFloat(document.getElementById('longitude').value);
                                    if (isNaN(latInput) || isNaN(lngInput)) {
                                        alert("Vui lòng chọn điểm khởi hành trên bản đồ.");
                                        return;
                                    }
                                    var origin = lngInput + ',' + latInput;
                                    var destination = roomLng + ',' + roomLat;
                                    var osrmUrl = "http://router.project-osrm.org/route/v1/driving/" 
                                        + origin + ";" + destination 
                                        + "?overview=full&geometries=geojson";

                                    fetch(osrmUrl)
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.routes && data.routes.length > 0) {
                                                var route = data.routes[0];
                                                var distanceKm = (route.distance / 1000).toFixed(2);

                                                if (window.routeLayer) {
                                                    map1.removeLayer(window.routeLayer);
                                                }
                                                window.routeLayer = L.geoJSON(route.geometry, {
                                                    style: { color: 'blue', weight: 4 }
                                                }).addTo(map1);
                                                
                                                L.popup()
                                                    .setLatLng([roomLat, roomLng])
                                                    .setContent("<strong>" + roomName + "</strong><br>" + roomAddress + "<br>Khoảng cách: " + distanceKm + " km")
                                                    .openOn(map1);
                                            } else {
                                                alert("Không tìm thấy tuyến đường.");
                                            }
                                        })
                                        .catch(err => {
                                            console.error(err);
                                            alert("Có lỗi khi lấy thông tin tuyến đường.");
                                        });
                                });
                            });
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../HeaderFooter/footer.php'); ?>
