<?php
// edit_truong.php
include('../db_connect.php');

if (!isset($_GET['ma_truong'])) {
    header("Location: daihoc.php");
    exit();
}

$ma_truong = $_GET['ma_truong'];

// Truy vấn thông tin trường hiện tại kèm theo các thông tin địa chỉ liên quan
$sql = "SELECT t.*, d.DUONG_TEN, xp.XP_TEN, qh.QH_TEN, ttp.TTP_TEN
        FROM truong t
        LEFT JOIN duong d ON t.DUONG_MA = d.DUONG_MA
        LEFT JOIN xa_phuong xp ON d.XP_MA = xp.XP_MA
        LEFT JOIN quan_huyen qh ON xp.QH_MA = qh.QH_MA
        LEFT JOIN tinh_thanh_pho ttp ON qh.TTP_MATINH = ttp.TTP_MATINH
        WHERE t.TRUONG_MA = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$ma_truong]);
$truong = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$truong) {
    echo "Không tìm thấy trường với mã: " . htmlspecialchars($ma_truong);
    exit();
}

// Gán giá trị cho các trường form
$ten_truong    = $truong['TRUONG_TEN'];
$vi_do_truong = $truong['TRUONG_LATITUDE'];     // Vĩ độ
$kinh_do_truong= $truong['TRUONG_LONGTITUDE'];     // Kinh độ
$ten_duong     = $truong['DUONG_TEN'];
$ten_xa        = $truong['XP_TEN'];
$ten_quan      = $truong['QH_TEN'];
$ten_tinh      = $truong['TTP_TEN'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sua_truong'])) {
    // Lấy dữ liệu từ form (sử dụng trim để loại bỏ khoảng trắng thừa)
    $ten_truong    = trim($_POST['ten_truong']);
    $vi_do_truong = trim($_POST['vi_do_truong']);
    $kinh_do_truong= trim($_POST['kinh_do_truong']);
    $ten_duong     = trim($_POST['ten_duong']);
    $ten_xa        = trim($_POST['ten_xa']);
    $ten_quan      = trim($_POST['ten_quan']);
    $ten_tinh      = trim($_POST['ten_tinh']);
    
    // Tạo chuỗi địa chỉ từ các phần nếu không rỗng
    $addressParts = [];
    if (!empty($ten_duong)) { $addressParts[] = $ten_duong; }
    if (!empty($ten_xa)) { $addressParts[] = $ten_xa; }
    if (!empty($ten_quan)) { $addressParts[] = $ten_quan; }
    if (!empty($ten_tinh)) { $addressParts[] = $ten_tinh; }
    $so_dia_chi = implode(', ', $addressParts);

        // Xử lý upload file ảnh nếu có chọn
        $truong_icon = '';  // Khởi tạo biến lưu đường dẫn file ảnh
        if (isset($_FILES['truong_icon']) && $_FILES['truong_icon']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/icons/';  // Thư mục lưu ảnh
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            // Đổi tên file để tránh trùng lặp (ví dụ: thêm timestamp)
            $fileName = time() . '_' . basename($_FILES['truong_icon']['name']);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['truong_icon']['tmp_name'], $targetFile)) {
                $truong_icon = $targetFile;  // Lưu đường dẫn file ảnh vào biến
            }
        }
    
    try {
        $conn->beginTransaction();

        // Hàm hỗ trợ sinh mã tự động (giống như trong phần thêm)
        function generateCode($conn, $table, $prefix, $column) {
            $stmt = $conn->query("SELECT MAX(CAST(SUBSTRING($column, 3) AS UNSIGNED)) AS max_code FROM $table");
            $row = $stmt->fetch();
            $maxCode = $row['max_code'] ? intval($row['max_code']) + 1 : 1;
            return $prefix . str_pad($maxCode, 3, '0', STR_PAD_LEFT);
        }

        
        // Kiểm tra và lấy/chèn Tỉnh/Thành phố
        $stmt = $conn->prepare("SELECT TTP_MATINH FROM tinh_thanh_pho WHERE TTP_TEN = ?");
        $stmt->execute([$ten_tinh]);
        $ma_tinh = $stmt->fetchColumn();
        if (!$ma_tinh) {
            $ma_tinh = generateCode($conn, 'tinh_thanh_pho', 'TP', 'TTP_MATINH');
            $stmt = $conn->prepare("INSERT INTO tinh_thanh_pho (TTP_MATINH, TTP_TEN) VALUES (?, ?)");
            $stmt->execute([$ma_tinh, $ten_tinh]);
        }
        
        // Kiểm tra và lấy/chèn Quận/Huyện
        $stmt = $conn->prepare("SELECT QH_MA FROM quan_huyen WHERE QH_TEN = ? AND TTP_MATINH = ?");
        $stmt->execute([$ten_quan, $ma_tinh]);
        $ma_quan = $stmt->fetchColumn();
        if (!$ma_quan) {
            $ma_quan = generateCode($conn, 'quan_huyen', 'QH', 'QH_MA');
            $stmt = $conn->prepare("INSERT INTO quan_huyen (QH_MA, QH_TEN, TTP_MATINH) VALUES (?, ?, ?)");
            $stmt->execute([$ma_quan, $ten_quan, $ma_tinh]);
        }
        
        // Kiểm tra và lấy/chèn Xã/Phường
        $stmt = $conn->prepare("SELECT XP_MA FROM xa_phuong WHERE XP_TEN = ? AND QH_MA = ?");
        $stmt->execute([$ten_xa, $ma_quan]);
        $ma_xa = $stmt->fetchColumn();
        if (!$ma_xa) {
            $ma_xa = generateCode($conn, 'xa_phuong', 'XP', 'XP_MA');
            $stmt = $conn->prepare("INSERT INTO xa_phuong (XP_MA, XP_TEN, QH_MA) VALUES (?, ?, ?)");
            $stmt->execute([$ma_xa, $ten_xa, $ma_quan]);
        }
        
        // Kiểm tra và lấy/chèn Đường
        $stmt = $conn->prepare("SELECT DUONG_MA FROM duong WHERE DUONG_TEN = ? AND XP_MA = ?");
        $stmt->execute([$ten_duong, $ma_xa]);
        $ma_duong = $stmt->fetchColumn();
        if (!$ma_duong) {
            $ma_duong = generateCode($conn, 'duong', 'D', 'DUONG_MA');
            $stmt = $conn->prepare("INSERT INTO duong (DUONG_MA, DUONG_TEN, XP_MA) VALUES (?, ?, ?)");
            $stmt->execute([$ma_duong, $ten_duong, $ma_xa]);
        }
        
        // Cập nhật bảng truong với thông tin mới (mã trường không thay đổi)
        // $sqlUpdate = "UPDATE truong 
        //               SET DUONG_MA = ?, TRUONG_TEN = ?, TRUONG_SODIACHI = ?, TRUONG_LONGTITUDE = ?, TRUONG_LATITUDE = ?
        //               WHERE TRUONG_MA = ?";
        // $stmt = $conn->prepare($sqlUpdate);
        // $stmt->execute([$ma_duong, $ten_truong, $so_dia_chi, $kinh_do_truong, $vi_do_truong, $ma_truong]);

        if (!empty($truong_icon)) {
          $sqlUpdate = "UPDATE truong 
                        SET DUONG_MA = ?, TRUONG_TEN = ?, TRUONG_SODIACHI = ?, TRUONG_LONGTITUDE = ?, TRUONG_LATITUDE = ?, TRUONG_ICON = ?
                        WHERE TRUONG_MA = ?";
          $stmt = $conn->prepare($sqlUpdate);
          $stmt->execute([$ma_duong, $ten_truong, $so_dia_chi, $kinh_do_truong, $vi_do_truong, $truong_icon, $ma_truong]);
      } else {
          $sqlUpdate = "UPDATE truong 
                        SET DUONG_MA = ?, TRUONG_TEN = ?, TRUONG_SODIACHI = ?, TRUONG_LONGTITUDE = ?, TRUONG_LATITUDE = ?
                        WHERE TRUONG_MA = ?";
          $stmt = $conn->prepare($sqlUpdate);
          $stmt->execute([$ma_duong, $ten_truong, $so_dia_chi, $kinh_do_truong, $vi_do_truong, $ma_truong]);
      }
        
        // Nếu tọa độ thay đổi, cập nhật lại bảng khoang_cach:
// Xóa các dòng khoảng cách cũ của trường
$stmt = $conn->prepare("DELETE FROM khoang_cach WHERE TRUONG_MA = ?");
$stmt->execute([$ma_truong]);

// Lấy danh sách các khu trọ từ bảng khu_tro
$sqlDorms = "SELECT KT_MAKT, KT_LONGTITUDE, KT_LATITUDE FROM khu_tro";
$stmtDorms = $conn->query($sqlDorms);
$dorms = $stmtDorms->fetchAll(PDO::FETCH_ASSOC);

// Tính và chèn khoảng cách mới đối với từng khu trọ sử dụng API OSRM
foreach ($dorms as $dorm) {
    $dorm_lon = $dorm['KT_LONGTITUDE'];
    $dorm_lat = $dorm['KT_LATITUDE'];
    
    // Xây dựng URL API OSRM (thứ tự tham số: kinh độ, vĩ độ)
    $osrmUrl = "http://router.project-osrm.org/route/v1/driving/{$kinh_do_truong},{$vi_do_truong};{$dorm_lon},{$dorm_lat}?overview=false";
    
    // Gọi API OSRM (sử dụng file_get_contents; lưu ý: allow_url_fopen cần được bật)
    $json = @file_get_contents($osrmUrl);
    if ($json === FALSE) {
        // Nếu gọi API lỗi, bỏ qua khu trọ này
        continue;
    }
    
    $data = json_decode($json, true);
    if (isset($data['routes'][0]['distance'])) {
        $distance_m = $data['routes'][0]['distance']; // khoảng cách theo mét
        $distance_km = $distance_m / 1000;              // chuyển đổi sang km
        
        // Lưu khoảng cách vào bảng khoang_cach
        $sqlInsert = "INSERT INTO khoang_cach (KT_MAKT, TRUONG_MA, KC_DODAI, KC_DONVIDO) VALUES (?, ?, ?, 'km')";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->execute([$dorm['KT_MAKT'], $ma_truong, $distance_km]);
    }
}

        $conn->commit();
        header("Location: daihoc.php?status=success&message=Sửa+trường+thành+công");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $error_message = "Lỗi: " . $e->getMessage();
    }

}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sửa Trường Đại Học</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Nếu cần dùng Leaflet cho chọn vị trí -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    /* CSS cho modal bản đồ (như trong daihoc.php) */
    #overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 2000;
    }
    #map-container {
      display: none;
      position: fixed;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 800px; height: 600px;
      background: white;
      z-index: 2001;
      box-shadow: 0 4px 6px rgba(0,0,0,0.3);
      overflow: hidden;
      border-radius: 8px;
    }
    #small-map { width: 100%; height: 100%; }
    #close-map { position: absolute; top: 10px; right: 10px; font-size: 20px; cursor: pointer; z-index: 2002; }
  </style>
</head>
<body>
  <div class="container mt-4">
    <h2 class="text-center mb-4">Sửa Trường Đại Học</h2>
    
    <?php if(isset($error_message)) { ?>
      <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>
    
    <div class="card p-4 mb-4">
      <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">
          <!-- Mã trường (read-only) -->
          <div class="col-md-4">
            <label class="form-label">Mã trường</label>
            <input type="text" name="ma_truong" class="form-control" value="<?php echo htmlspecialchars($ma_truong); ?>" readonly>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tên trường</label>
            <input type="text" name="ten_truong" class="form-control" value="<?php echo htmlspecialchars($ten_truong); ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Ảnh trường</label>
            <input type="file" name="truong_icon" class="form-control" accept="image/*">
          </div>
          <!-- Nút chọn vị trí trên bản đồ -->
          <div class="col-12">
            <label class="h5">Chọn điểm trên bản đồ:</label>
            <button type="button" class="btn btn-secondary" id="choose-location">Chọn điểm</button>
          </div>
          <div class="col-md-6">
            <label class="form-label">Vĩ độ</label>
            <input type="text" class="form-control" id="latitude" name="vi_do_truong" value="<?php echo htmlspecialchars($vi_do_truong); ?>" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label">Kinh độ</label>
            <input type="text" class="form-control" id="longitude" name="kinh_do_truong" value="<?php echo htmlspecialchars($kinh_do_truong); ?>" readonly>
          </div>
          <!-- Các trường địa chỉ -->
          <div class="col-md-6">
            <label class="form-label">Tên đường</label>
            <input type="text" class="form-control" id="ten_duong" name="ten_duong" value="<?php echo htmlspecialchars($ten_duong); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Xã/Phường</label>
            <input type="text" class="form-control" id="ten_xa" name="ten_xa" value="<?php echo htmlspecialchars($ten_xa); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Quận/Huyện</label>
            <input type="text" class="form-control" id="ten_quan" name="ten_quan" value="<?php echo htmlspecialchars($ten_quan); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Tỉnh/Thành phố</label>
            <input type="text" class="form-control" id="ten_tinh" name="ten_tinh" value="<?php echo htmlspecialchars($ten_tinh); ?>">
          </div>
          <div class="col-12 text-center">
            <button type="submit" name="sua_truong" class="btn btn-primary">Cập nhật</button>
            <a href="daihoc.php" class="btn btn-secondary">Quay lại</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Các thành phần cho modal bản đồ -->
  <div id="overlay"></div>
  <div id="map-container">
    <button id="close-map" class="btn btn-primary">X</button>
    <div id="small-map"></div>
  </div>
  
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    let modalMap;
    function initModalMap() {
      modalMap = L.map('small-map').setView([10.0301, 105.7792], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);
  
      modalMap.on('click', function(e) {
        let chosenLatLng = e.latlng;
  
        modalMap.eachLayer(layer => {
          if (layer instanceof L.Marker) {
            modalMap.removeLayer(layer);
          }
        });
  
        L.marker(chosenLatLng).addTo(modalMap)
          .bindPopup("Bạn đã chọn điểm này")
          .openPopup();
  
        document.getElementById('latitude').value = chosenLatLng.lat;
        document.getElementById('longitude').value = chosenLatLng.lng;
  
        getAddressFromCoordinates(chosenLatLng.lat, chosenLatLng.lng);
  
        closeModalMap();
      });
    }
  
    function getAddressFromCoordinates(lat, lon) {
      fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&accept-language=vi`)
        .then(response => response.json())
        .then(data => {
          let street = data.address.road || data.address.pedestrian || data.address.footway || '';
          document.getElementById('ten_duong').value = street;
  
          let district = data.address.city_district || data.address.town || data.address.county || '';
          document.getElementById('ten_xa').value = district;
  
          let ward = data.address.suburb || data.address.village || data.address.hamlet || '';
          if (ward === district) { ward = ''; }
          document.getElementById('ten_quan').value = ward;
  
          let province = data.address.state || data.address.city || '';
          document.getElementById('ten_tinh').value = province;
        })
        .catch(error => console.error('Lỗi lấy địa chỉ:', error));
    }
  
    document.getElementById('choose-location').addEventListener('click', function() {
      document.getElementById('overlay').style.display = 'block';
      document.getElementById('map-container').style.display = 'block';
      initModalMap();
    });
  
    document.getElementById('close-map').addEventListener('click', closeModalMap);
    document.getElementById('overlay').addEventListener('click', closeModalMap);
  
    function closeModalMap() {
      document.getElementById('overlay').style.display = 'none';
      document.getElementById('map-container').style.display = 'none';
      if (modalMap) {
        modalMap.remove();
        modalMap = null;
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
