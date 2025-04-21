<?php
// daihoc.php
include('../db_connect.php');


// Xử lý thêm trường
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['them_truong'])) {
      // Kiểm tra bắt buộc các trường cần thiết
      if (empty($_POST['ma_truong']) || empty($_POST['vi_do_truong']) || empty($_POST['kinh_do_truong']) || empty($_POST['ten_truong']) || empty($_POST['ten_duong']) || empty($_POST['ten_xa']) || empty($_POST['ten_quan']) || empty($_POST['ten_tinh']) ) {
        $error_message = "Vui lòng nhập đầy đủ thông tin: Mã trường, Tên trường, Vĩ độ, Kinh độ, Tên đường, Xã/Phường, Quận/Huyện và Tỉnh/Thành phố.";
    } else {
  $ma_truong      = $_POST['ma_truong'];
  $ten_truong     = $_POST['ten_truong'];
  // $so_dia_chi     = $_POST['so_dia_chi'];
  $vi_do_truong  = $_POST['vi_do_truong'];
  $kinh_do_truong = $_POST['kinh_do_truong'];
  $ten_duong = trim($_POST['ten_duong']);
  $ten_xa    = trim($_POST['ten_xa']);
  $ten_quan  = trim($_POST['ten_quan']);
  $ten_tinh  = trim($_POST['ten_tinh']);
  // Tạo mảng chứa các thành phần địa chỉ nếu không rỗng
$addressParts = [];
if (!empty($ten_duong)) { 
    $addressParts[] = $ten_duong;
}
if (!empty($ten_xa)) { 
    $addressParts[] = $ten_xa;
}
if (!empty($ten_quan)) { 
    $addressParts[] = $ten_quan;
}
if (!empty($ten_tinh)) { 
    $addressParts[] = $ten_tinh;
}

// Ghép chuỗi địa chỉ, chỉ chèn dấu phẩy giữa các phần không rỗng
$so_dia_chi = implode(', ', $addressParts);

  // Xử lý upload file icon
  $truong_icon = '';  // Khởi tạo biến icon
  if (isset($_FILES['truong_icon']) && $_FILES['truong_icon']['error'] == UPLOAD_ERR_OK) {
      $uploadDir = 'uploads/icons/';  // Đường dẫn lưu file (có thể điều chỉnh)
      if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0755, true);
      }
      // Đổi tên file để tránh trùng lặp (ví dụ: thêm timestamp)
      $fileName = time() . '_' . basename($_FILES['truong_icon']['name']);
      $targetFile = $uploadDir . $fileName;
      if (move_uploaded_file($_FILES['truong_icon']['tmp_name'], $targetFile)) {
          $truong_icon = $targetFile;  // Lưu đường dẫn file vào biến
      }
  }

  try {
    $conn->beginTransaction();

    function generateCode($conn, $table, $prefix, $column) {
        $stmt = $conn->query("SELECT MAX(CAST(SUBSTRING($column, 3) AS UNSIGNED)) AS max_code FROM $table");
        $row = $stmt->fetch();
        $maxCode = $row['max_code'] ? intval($row['max_code']) + 1 : 1;
        return $prefix . str_pad($maxCode, 3, '0', STR_PAD_LEFT);
    }

    // Kiểm tra và lấy hoặc chèn tỉnh thành phố
    $stmt = $conn->prepare("SELECT TTP_MATINH FROM tinh_thanh_pho WHERE TTP_TEN = ?");
    $stmt->execute([$ten_tinh]);
    $ma_tinh = $stmt->fetchColumn();

    if (!$ma_tinh) {
        $ma_tinh = generateCode($conn, 'tinh_thanh_pho', 'TP', 'TTP_MATINH');
        $stmt = $conn->prepare("INSERT INTO tinh_thanh_pho (TTP_MATINH, TTP_TEN) VALUES (?, ?)");
        $stmt->execute([$ma_tinh, $ten_tinh]);
    }

    // Kiểm tra và lấy hoặc chèn quận huyện
    $stmt = $conn->prepare("SELECT QH_MA FROM quan_huyen WHERE QH_TEN = ? AND TTP_MATINH = ?");
    $stmt->execute([$ten_quan, $ma_tinh]);
    $ma_quan = $stmt->fetchColumn();

    if (!$ma_quan) {
        $ma_quan = generateCode($conn, 'quan_huyen', 'QH', 'QH_MA');
        $stmt = $conn->prepare("INSERT INTO quan_huyen (QH_MA, QH_TEN, TTP_MATINH) VALUES (?, ?, ?)");
        $stmt->execute([$ma_quan, $ten_quan, $ma_tinh]);
    }

    // Kiểm tra và lấy hoặc chèn xã phường
    $stmt = $conn->prepare("SELECT XP_MA FROM xa_phuong WHERE XP_TEN = ? AND QH_MA = ?");
    $stmt->execute([$ten_xa, $ma_quan]);
    $ma_xa = $stmt->fetchColumn();

    if (!$ma_xa) {
        $ma_xa = generateCode($conn, 'xa_phuong', 'XP', 'XP_MA');
        $stmt = $conn->prepare("INSERT INTO xa_phuong (XP_MA, XP_TEN, QH_MA) VALUES (?, ?, ?)");
        $stmt->execute([$ma_xa, $ten_xa, $ma_quan]);
    }

    // Kiểm tra và lấy hoặc chèn đường
    $stmt = $conn->prepare("SELECT DUONG_MA FROM duong WHERE DUONG_TEN = ? AND XP_MA = ?");
    $stmt->execute([$ten_duong, $ma_xa]);
    $ma_duong = $stmt->fetchColumn();

    if (!$ma_duong) {
        $ma_duong = generateCode($conn, 'duong', 'D', 'DUONG_MA');
        $stmt = $conn->prepare("INSERT INTO duong (DUONG_MA, DUONG_TEN, XP_MA) VALUES (?, ?, ?)");
        $stmt->execute([$ma_duong, $ten_duong, $ma_xa]);
    }

    // Chèn trường đại học
    // $ma_truong = generateCode($conn, 'truong', 'tdh', 'TRUONG_MA');
    $sql = "INSERT INTO truong (TRUONG_MA, DUONG_MA, TRUONG_TEN, TRUONG_SODIACHI, TRUONG_LONGTITUDE, TRUONG_LATITUDE, TRUONG_ICON) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$ma_truong, $ma_duong, $ten_truong, $so_dia_chi, $kinh_do_truong, $vi_do_truong, $truong_icon]);

   // Tính và lưu khoảng cách với các khu trọ sử dụng OSRM API cho định tuyến thực tế
   $sqlDorms = "SELECT KT_MAKT, KT_LONGTITUDE, KT_LATITUDE FROM khu_tro";
   $stmtDorms = $conn->query($sqlDorms);
   $dorms = $stmtDorms->fetchAll(PDO::FETCH_ASSOC);
   
   foreach ($dorms as $dorm) {
       $dorm_lon = $dorm['KT_LONGTITUDE'];
       $dorm_lat = $dorm['KT_LATITUDE'];
       
       // Gọi API OSRM: Thay đổi tham số nếu cần để phù hợp với định dạng (lon,lat)
       $osrmUrl = "http://router.project-osrm.org/route/v1/driving/{$kinh_do_truong},{$vi_do_truong};{$dorm_lon},{$dorm_lat}?overview=false";
       
       // Gọi API, sử dụng file_get_contents (đảm bảo allow_url_fopen được bật) hoặc dùng cURL nếu cần
       $json = file_get_contents($osrmUrl);
       if($json === FALSE) {
           // Nếu gọi API lỗi, có thể bỏ qua khu trọ này
           continue;
       }
       
       $data = json_decode($json, true);
       if(isset($data['routes'][0]['distance'])) {
           $distance_m = $data['routes'][0]['distance']; // khoảng cách tính theo mét
           $distance_km = $distance_m / 1000;           // chuyển đổi sang km
           
           // Lưu khoảng cách vào bảng khoang_cach
           $sqlInsert = "INSERT INTO khoang_cach (KT_MAKT, TRUONG_MA, KC_DODAI, KC_DONVIDO) VALUES (?, ?, ?, 'km')";
           $stmtInsert = $conn->prepare($sqlInsert);
           $stmtInsert->execute([$dorm['KT_MAKT'], $ma_truong, $distance_km]);
       }
   }

    $conn->commit();
    header("Location: daihoc.php?status=success&message=Thêm+trường+thành+công");
    exit();
} catch (Exception $e) {
    $conn->rollBack();
    $error_message = "Lỗi: " . $e->getMessage();
}
}
}



// Lấy danh sách trường ban đầu (dùng khi không có tìm kiếm)
$sql = "SELECT t.*, d.DUONG_TEN 
        FROM truong t 
        LEFT JOIN duong d ON t.DUONG_MA = d.DUONG_MA 
        ORDER BY t.TRUONG_MA DESC";
$stmt = $conn->query($sql);
$truongs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Định nghĩa function generateCodeTruong
function generateCodeTruong($conn, $table = 'truong', $prefix = 'TR', $column = 'TRUONG_MA') {
  $stmt = $conn->query("SELECT MAX(CAST(SUBSTRING($column, 3) AS UNSIGNED)) AS max_code FROM $table");
  $row = $stmt->fetch();
  $maxCode = $row['max_code'] ? intval($row['max_code']) + 1 : 1;
  return $prefix . str_pad($maxCode, 3, '0', STR_PAD_LEFT);
}

// Nếu có yêu cầu gọi AJAX để tự sinh mã trường
if (isset($_GET['action']) && $_GET['action'] == 'generate_code') {
  echo generateCodeTruong($conn);
  exit();
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản Lý Trường Đại Học</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    /* CSS cho modal bản đồ */
    #overlay {
      display: none; 
      position: fixed; 
      top: 0; left: 0; 
      width: 100%; 
      height: 100%; 
      background: rgba(0,0,0,0.5); 
      z-index: 2000;
    }
    #map-container {
      display: none; 
      position: fixed; 
      top: 50%; left: 50%; 
      transform: translate(-50%, -50%); 
      width: 800px; 
      height: 600px; 
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
    <h2 class="text-center mb-4">Quản Lý Trường Đại Học</h2>
    
    <!-- Hiển thị thông báo nếu có -->
      <?php if(isset($_GET['status']) && $_GET['status'] == 'success' && isset($_GET['message'])): ?>
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($_GET['message']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

    <!-- Ô tìm kiếm live search -->
    <div class="card p-3 mb-4">
      <input type="text" id="search" class="form-control" placeholder="Tìm kiếm trường theo tên hoặc địa chỉ...">
    </div>
    
    <!-- Hiển thị thông báo lỗi nếu có -->
    <?php if(isset($error_message)) { ?>
      <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>

    <!-- Form Thêm Trường -->
    <div class="card p-4 mb-4">
      <h5 class="card-title">Thêm Trường</h5>
      <form method="POST" enctype="multipart/form-data" id="addForm">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="input-group">
              <input type="text" name="ma_truong" id="ma_truong" class="form-control" placeholder="Mã trường" readonly required>
              <button type="button" class="btn btn-primary" id="btn_generate_code">+</button>
            </div>
          </div>
          <div class="col-md-4">
            <input type="text" name="ten_truong" class="form-control" placeholder="Tên trường" required >
          </div>
          <!-- <div class="col-md-4">
            <input type="text" name="so_dia_chi" class="form-control" placeholder="Số địa chỉ" required>
          </div> -->
          <div class="col-md-4">
          <input type="file" name="truong_icon" class="form-control" accept="image/*">
          </div>

                <!-- Chọn vị trí trên bản đồ -->
      <div class="col-12">
        <label class="h5">Chọn điểm trên bản đồ:</label>
        <button type="button" class="btn btn-secondary" id="choose-location">Chọn điểm</button>
      </div>

      <div class="col-md-6">
        <input type="text" class="form-control" id="latitude" name="vi_do_truong" placeholder="Vĩ độ" readonly required>
      </div>
      <div class="col-md-6">
        <input type="text" class="form-control" id="longitude" name="kinh_do_truong" placeholder="Kinh độ" readonly required>
      </div>

      <!-- Các ô nhập tự động điền thông tin địa chỉ -->
      <div class="col-md-6">
        <input type="text" class="form-control" id="ten_duong" name="ten_duong" placeholder="Tên đường" required >
      </div>
      <div class="col-md-6">
        <input type="text" class="form-control" id="ten_xa" name="ten_xa" placeholder="Xã/Phường" required >
      </div>
      <div class="col-md-6">
        <input type="text" class="form-control" id="ten_quan" name="ten_quan" placeholder="Quận/Huyện" required >
      </div>
      <div class="col-md-6">
        <input type="text" class="form-control" id="ten_tinh" name="ten_tinh" placeholder="Tỉnh/Thành phố" required>
      </div>

      <div class="col-12 text-center">
        <button type="submit" name="them_truong" class="btn btn-primary">Thêm Trường</button>
         <!-- Nút Hủy, ẩn ban đầu -->
         <button type="button" id="resetButton" class="btn btn-danger ms-2" style="display:none;">Hủy</button>
      </div>
    </div>
  </form>
</div>
          
    <!-- Bảng Danh Sách Trường -->
    <div class="card p-3">
      <h5 class="card-title">Danh Sách Trường</h5>
      <table class="table table-bordered mt-3">
        <thead class="table-dark">
          <tr>
            <th>Mã trường</th>
            <th>Tên trường</th>
            <th>Số địa chỉ</th>
            <th>Vĩ độ</th>
            <th>Kinh độ</th>
            <!-- <th>Tên đường</th> -->
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody >
          <?php foreach($truongs as $truong): ?>
          <tr>
            <td><?php echo $truong['TRUONG_MA']; ?></td>
            <td><?php echo $truong['TRUONG_TEN']; ?></td>
            <td><?php echo $truong['TRUONG_SODIACHI']; ?></td>
            <td><?php echo $truong['TRUONG_LATITUDE']; ?></td>
            <td><?php echo $truong['TRUONG_LONGTITUDE']; ?></td>
            <!-- <td><?php echo $truong['DUONG_TEN']; ?></td> -->
            <td>
              <a href="edit_truong.php?ma_truong=<?php echo $truong['TRUONG_MA']; ?>" class="btn btn-primary btn-sm">Sửa</a>
              <a href="delete_truong.php?ma_truong=<?php echo $truong['TRUONG_MA']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa trường này không?');">Xóa</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4 mb-4">
      <a href="admin.php" class="btn btn-primary">Trang Chủ</a>
      <a href="viewmap.php" class="btn btn-success ms-2">Xem Bản Đồ</a>
    </div>
  </div>

  <!-- Các thành phần cho modal bản đồ -->
  <div id="overlay"></div>
  <div id="map-container">
    <button id="close-map" class="btn btn-primary">X</button>
    <div id="small-map"></div>
  </div>
  
  <!-- Script gọi AJAX để tự sinh mã trường -->
  <script>
document.getElementById('btn_generate_code').addEventListener('click', function() {
  fetch('daihoc.php?action=generate_code')
    .then(response => response.text())
    .then(code => {
      document.getElementById('ma_truong').value = code;
    })
    .catch(error => console.error('Lỗi:', error));
});

// Kiểm tra trước khi gửi form
document.getElementById('addForm').addEventListener('submit', function(event) {
  var maTruong = document.getElementById('ma_truong').value.trim();
  var viDoTruong = document.getElementById('latitude').value.trim();
  var kinhDoTruong = document.getElementById('longitude').value.trim();

  if(maTruong === '' || viDoTruong === '' || kinhDoTruong === '') {
    event.preventDefault();
    alert('Vui lòng đảm bảo rằng ô mã trường, vĩ độ và kinh độ đã được điền đầy đủ!');
  }
});
</script>


  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    // Modal bản đồ
let modalMap;
function initModalMap() {
  modalMap = L.map('small-map').setView([10.0301, 105.7792], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);

  // Gán sự kiện click khi bản đồ được tạo
  modalMap.on('click', function(e) {
    let chosenLatLng = e.latlng;

    // Xóa marker cũ nếu có
    modalMap.eachLayer(layer => {
      if (layer instanceof L.Marker) {
        modalMap.removeLayer(layer);
      }
    });

    // Thêm marker mới
    L.marker(chosenLatLng).addTo(modalMap)
      .bindPopup("Bạn đã chọn điểm này")
      .openPopup();

    // Cập nhật tọa độ
    document.getElementById('latitude').value = chosenLatLng.lat;
    document.getElementById('longitude').value = chosenLatLng.lng;

    

    // Lấy địa chỉ từ API OpenStreetMap
    getAddressFromCoordinates(chosenLatLng.lat, chosenLatLng.lng);

    // Đóng modal bản đồ
    closeModalMap();
  });
}

// Hàm lấy địa chỉ từ tọa độ
function getAddressFromCoordinates(lat, lon) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&accept-language=vi`)
        .then(response => response.json())
        .then(data => {
            console.log(data); // Kiểm tra dữ liệu trả về trong console

            // Lấy thông tin đường: ưu tiên key 'road', nếu không có thử 'pedestrian' hoặc 'footway'
            let street = data.address.road || data.address.pedestrian || data.address.footway || '';
            document.getElementById('ten_duong').value = street;
            
            // Lấy thông tin quận/huyện (cho input 'ten_xa'): ưu tiên 'city_district', nếu không có thử 'town' hoặc 'county'
            let district = data.address.city_district || data.address.town || data.address.county || '';
            document.getElementById('ten_xa').value = district;
            
            // Lấy thông tin xã/phường (cho input 'ten_quan'): ưu tiên 'suburb', nếu không có thử 'village' hoặc 'hamlet'
            let ward = data.address.suburb || data.address.village || data.address.hamlet || '';
            // Nếu giá trị của ward trùng với district thì để trống
            if (ward === district) {
                ward = '';
            }
            document.getElementById('ten_quan').value = ward;
            
            // Lấy thông tin tỉnh/thành phố: ưu tiên 'state', nếu không có thử 'city'
            let province = data.address.state || data.address.city || '';
            document.getElementById('ten_tinh').value = province;
        })
        .catch(error => console.error('Lỗi lấy địa chỉ:', error));
}


// Xử lý khi nhấn nút chọn vị trí
document.getElementById('choose-location').addEventListener('click', function() {
  document.getElementById('overlay').style.display = 'block';
  document.getElementById('map-container').style.display = 'block';
  initModalMap();
});

// Đóng bản đồ khi nhấn nút đóng hoặc overlay
document.getElementById('close-map').addEventListener('click', closeModalMap);
document.getElementById('overlay').addEventListener('click', closeModalMap);

// Hàm đóng modal bản đồ
function closeModalMap() {
  document.getElementById('overlay').style.display = 'none';
  document.getElementById('map-container').style.display = 'none';

  // Xóa bản đồ để tránh lỗi khi mở lại
  if (modalMap) {
    modalMap.remove();
    modalMap = null;
  }
}

    // --- Live Search ---
    document.getElementById('search').addEventListener('input', function() {
      var query = this.value;
      fetch('search_daihoc.php?query=' + encodeURIComponent(query))
        .then(response => response.text())
        .then(data => {
          document.querySelector('table tbody').innerHTML = data;
        })
        .catch(error => console.error('Lỗi:', error));
    });

  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
// Hàm debounce (giữ nguyên nếu cần)
function debounce(func, delay) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), delay);
    };
}

// Lấy tham chiếu đến form và nút reset
const addForm = document.getElementById('addForm');
const resetButton = document.getElementById('resetButton');

// Hàm kiểm tra xem có ô nào có dữ liệu không
function checkFormFilled() {
    // Lấy tất cả các input có type text, file, và có id cần thiết
    const inputs = addForm.querySelectorAll('input[type="text"], input[type="file"]');
    let filled = false;
    inputs.forEach(input => {
        if (input.type === 'file') {
            if (input.files && input.files.length > 0) {
                filled = true;
            }
        } else {
            if (input.value.trim() !== '') {
                filled = true;
            }
        }
    });
    // Hiển thị hoặc ẩn nút reset
    resetButton.style.display = filled ? 'inline-block' : 'none';
    return filled;
}

// Thêm event listener cho tất cả các trường: lắng nghe cả "input" và "change"
addForm.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', checkFormFilled);
    input.addEventListener('change', checkFormFilled);
});

// Khi nhấn nút "Hủy", reset form và ẩn nút
resetButton.addEventListener('click', function() {
    addForm.reset();
    // Sau khi reset, gọi hàm kiểm tra để ẩn nút
    checkFormFilled();
});

// Sau khi tự sinh mã hoặc cập nhật tọa độ từ map, gọi thủ công checkFormFilled()
// Ví dụ, khi tự sinh mã:
document.getElementById('btn_generate_code').addEventListener('click', function() {
  fetch('daihoc.php?action=generate_code')
    .then(response => response.text())
    .then(code => {
      const maTruongInput = document.getElementById('ma_truong');
      maTruongInput.value = code;
      // Kích hoạt sự kiện input sau khi cập nhật giá trị
      maTruongInput.dispatchEvent(new Event('input'));
    })
    .catch(error => console.error('Lỗi:', error));
});

// Ví dụ, khi chọn điểm trên bản đồ, trong hàm xử lý sau khi cập nhật tọa độ:
function onMapPointChosen(chosenLatLng) {
    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');
    latInput.value = chosenLatLng.lat;
    lonInput.value = chosenLatLng.lng;
    // Kích hoạt sự kiện input cho cả hai trường
    latInput.dispatchEvent(new Event('input'));
    lonInput.dispatchEvent(new Event('input'));
    // Sau đó, gọi getAddressFromCoordinates nếu cần...
}
</script>


  

</body>
</html>
