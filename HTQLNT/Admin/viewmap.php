<?php
// view_map.php
include('../db_connect.php');

// Lấy dữ liệu trường từ bảng truong
$sqlSchools = "SELECT TRUONG_MA, TRUONG_TEN, TRUONG_LONGTITUDE, TRUONG_LATITUDE, TRUONG_ICON FROM truong";
$stmtSchools = $conn->query($sqlSchools);
$schools = $stmtSchools->fetchAll(PDO::FETCH_ASSOC);

// Lấy dữ liệu trọ từ bảng khotro
$sqlDorms = "SELECT KT_MAKT, KT_TENKHUTRO, KT_LONGTITUDE, KT_LATITUDE FROM khu_tro WHERE is_delete = 0";
$stmtDorms = $conn->query($sqlDorms);
$dorms = $stmtDorms->fetchAll(PDO::FETCH_ASSOC);

// Lấy dữ liệu khoảng cách từ bảng khoangcach (nếu cần hiển thị thông tin)
$sqlDistances = "SELECT KT_MAKT, TRUONG_MA, KC_DODAI, KC_DONVIDO FROM khoang_cach";
$stmtDistances = $conn->query($sqlDistances);
$distances = $stmtDistances->fetchAll(PDO::FETCH_ASSOC);

// Chuyển dữ liệu thành JSON để dùng trong JavaScript
$schoolsJSON = json_encode($schools);
$dormsJSON   = json_encode($dorms);
$distancesJSON = json_encode($distances);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xem Bản Đồ Và Chỉ Đường</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-search/4.0.0/leaflet-search.min.css" />
    <!-- Leaflet Routing Machine CSS (đảm bảo đường dẫn đúng) -->
    <link rel="stylesheet" href="css/leaflet-routing-machine.css" />
    <style>
        
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            height: 100vh; /* chiếm toàn bộ chiều cao viewport */
            width: 100%;
        }

        .info-box {
            position: absolute;
            bottom: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        /* Container tùy chỉnh chứa ô search */
#custom-search-container {
  position: absolute;
  top: 20px;              /* Điều chỉnh khoảng cách từ đỉnh trang */
  left: 50%;
  transform: translateX(-50%);
  z-index: 9999;          /* Đảm bảo nó nằm trên cùng */
  width: 400px;           /* Chiều rộng của ô search, có thể điều chỉnh */
}

/* Nếu cần, tùy chỉnh lại giao diện ô search bên trong */
#custom-search-container .leaflet-control-search {
  width: 233px !important;
  padding: 8px;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 4px;
}
#custom-search-container .leaflet-control-search input[type="search"] {
  width: 100%;
  padding: 8px 10px;
  border: none;
  outline: none;
  font-size: 14px;
}

.leaflet-control-search .search-button,
.leaflet-control-search .search-button:hover {
  background-image: url('uploads/icons/search-icon.png') !important; 
  background-size: 24px 24px;  /* Điều chỉnh kích thước phù hợp */
  background-repeat: no-repeat;
  background-position: center;
  
}

.leaflet-control-search .search-cancel {
  display: none !important;
}

/* .leaflet-control-search .search-button:hover {
    background: none;
    background-image: none;
    background-repeat: no-repeat;
    background-size: 24px 24px !important;
    transform: none !important;
    transition: none !important;
} */



        
    </style>
</head>

<body>

     <!-- Container search tùy chỉnh -->
    <div id="custom-search-container"></div>
    <div id="map"></div>
    <div class="info-box" id="infoBox">
        <h4>Thông tin nhanh</h4>
        <div id="routeInfo">Vui lòng chọn một trường và một trọ.</div>
        <div class="d-flex justify-content-end">
        <button id="clearSelection" class="btn btn-sm btn-secondary me-2">Xóa lựa chọn</button>
        <button id="homeButton" class="btn btn-sm btn-secondary" style="color: red;" onclick="window.location.href='admin.php'">Trang Chủ</button>
    </div>
    </div>


    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-search/4.0.0/leaflet-search.min.js"></script>
    <!-- Leaflet Routing Machine JS (đảm bảo đường dẫn đúng) -->
    <script src="js/leaflet-routing-machine.js"></script>
    <script>
        // Dữ liệu được truyền từ PHP
        const schools = <?php echo $schoolsJSON; ?>;
        const dorms = <?php echo $dormsJSON; ?>;
        const distances = <?php echo $distancesJSON; ?>;

        // Khởi tạo bản đồ
        const map = L.map('map').setView([10.0164, 105.761], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Tạo các layer để quản lý marker
        const schoolLayer = L.layerGroup().addTo(map);
        const dormLayer = L.layerGroup().addTo(map);

        // Định nghĩa icon cho trường và trọ
        const schoolIcon = L.icon({
            iconUrl: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });
        const dormIcon = L.icon({
            iconUrl: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        // Biến lưu lựa chọn và điều khiển định tuyến
        // let selectedSchool = null;
        // let selectedDorm = null;
        let selectedPoints = [];
        let routingControl = null;

        // Thêm marker cho trường
        schools.forEach(school => {
    if (school.TRUONG_LATITUDE && school.TRUONG_LONGTITUDE) {
        // Tạo cấu hình icon tùy chỉnh
        let iconOptions = {
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        };
        if (school.TRUONG_ICON && school.TRUONG_ICON !== "") {
            // Nếu trường có icon, sử dụng icon đó (đảm bảo đường dẫn đúng)
            iconOptions.iconUrl = school.TRUONG_ICON;
        } else {
            // Nếu không, dùng icon mặc định
            iconOptions.iconUrl = 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png';
        }
        const marker = L.marker([school.TRUONG_LATITUDE, school.TRUONG_LONGTITUDE], {
                icon: L.icon(iconOptions),
                title: school.TRUONG_TEN ? school.TRUONG_TEN : school.TRUONG_MA,
                schoolCode: String(school.TRUONG_MA)
            })
            .bindPopup(`<strong>Trường:</strong> ${school.TRUONG_TEN}<br>Mã: ${school.TRUONG_MA}`)
            .on('click', function() {
                onSchoolClick(school);
            });
        marker.addTo(schoolLayer);
    }
});

        // Thêm marker cho trọ
        dorms.forEach(dorm => {
            if (dorm.KT_LATITUDE && dorm.KT_LONGTITUDE) {
                const marker = L.marker([dorm.KT_LATITUDE, dorm.KT_LONGTITUDE], {
                        icon: dormIcon,
                        title: dorm.KT_TENKHUTRO ? dorm.KT_TENKHUTRO : String(dorm.KT_MAKT),
                        dormCode: String(dorm.KT_MAKT) 
                    })
                    .bindPopup(`<strong>Trọ:</strong> ${dorm.KT_TENKHUTRO ? dorm.KT_TENKHUTRO : dorm.KT_MAKT}<br>Mã: ${dorm.KT_MAKT}`)
                    .on('click', function() {
                        onDormClick(dorm);
                    });
                marker.addTo(dormLayer);
            }
        });

        // Tạo layer kết hợp và đưa các marker từ 2 layer vào đó
const combinedLayer = L.layerGroup().addTo(map);
schoolLayer.eachLayer(function(marker) {
    combinedLayer.addLayer(marker);
});
dormLayer.eachLayer(function(marker) {
    combinedLayer.addLayer(marker);
});

// Tạo search control sử dụng layer chung
var searchControl = new L.Control.Search({
      layer: combinedLayer,
      propertyName: 'title',
        marker: false,
        initial: false,
      zoom: 15,
      textPlaceholder: 'Tìm trường hoặc trọ...',
      moveToLocation: function(latlng, title, map) {
        map.setView(latlng, 15);
      },
      position: 'topleft',
      collapsed: false
      
    });
    searchControl.on('search:locationfound', function(e) {
      if (e.layer._popup)
        e.layer.openPopup();
    });
    map.addControl(searchControl);

    // Lấy container của search control
var searchContainer = searchControl.getContainer();

// Di chuyển nó sang container tùy chỉnh
document.getElementById('custom-search-container').appendChild(searchContainer);

// (Tùy chọn) Xóa container mặc định nếu cần, nhưng thông thường getContainer() đã trả về DOM element cần thiết.


        // Hàm cập nhật định tuyến dựa trên lựa chọn của trường và trọ
       // Hàm cập nhật định tuyến theo thứ tự lựa chọn
        function updateRoute() {
            if (selectedPoints.length === 2) {
                // Nếu đã có định tuyến, xóa đi
                if (routingControl) {
                map.removeControl(routingControl);
                }
                routingControl = L.Routing.control({
                router: new L.Routing.OSRMv1({
                    serviceUrl: 'http://router.project-osrm.org/route/v1',
                    language: 'vi'
                }),
                waypoints: [
                    // Dùng thứ tự trong mảng: điểm đầu tiên là điểm bắt đầu, thứ hai là điểm kết thúc
                    L.latLng(selectedPoints[0].data.lat, selectedPoints[0].data.lng),
                    L.latLng(selectedPoints[1].data.lat, selectedPoints[1].data.lng)
                ],
                routeWhileDragging: true,
                lineOptions: {
                    styles: [{
                    color: 'blue',
                    weight: 6,
                    opacity: 1
                    }]
                }
            }).addTo(map);

        // Cập nhật nội dung hiển thị dựa theo thứ tự lựa chọn
        let directionText = '';
        if (selectedPoints[0].type === 'school' && selectedPoints[1].type === 'dorm') {
            directionText = '<p>Từ trường đến trọ:</p>';
        } else if (selectedPoints[0].type === 'dorm' && selectedPoints[1].type === 'school') {
            directionText = '<p>Từ trọ đến trường:</p>';
        } else {
            directionText = `<p>Từ ${selectedPoints[0].data.name} đến ${selectedPoints[1].data.name}.</p>`;
        }
        
        document.getElementById('routeInfo').innerHTML = `
            <p><strong>Từ:</strong> ${selectedPoints[0].data.name} (Mã: ${selectedPoints[0].data.code})</p>
            <p><strong>Đến:</strong> ${selectedPoints[1].data.name} (Mã: ${selectedPoints[1].data.code})</p>
            ${directionText}`;
                    // Lắng nghe sự kiện 'routesfound' để lấy thông tin khoảng cách và thời gian
        routingControl.on('routesfound', function(e) {
            var routes = e.routes;
            if (routes.length > 0) {
                var summary = routes[0].summary;
                var distance = (summary.totalDistance / 1000).toFixed(2) + " km";
                var time = Math.ceil(summary.totalTime / 60) + " phút";
                document.getElementById('routeInfo').innerHTML += `<p>Khoảng cách: ${distance}</p>
                                                                     <p>Thời gian: ${time}</p>`;
            }
        });
    }
}

        // Hàm xử lý khi marker của trường được nhấn
function onSchoolClick(school) {
  // Tạo đối tượng chứa thông tin của trường
  const schoolObj = {
    type: 'school',
    data: {
      lat: parseFloat(school.TRUONG_LATITUDE),
      lng: parseFloat(school.TRUONG_LONGTITUDE),
      name: school.TRUONG_TEN,
      code: school.TRUONG_MA
    }
  };
  // Nếu đã có đối tượng của trường, cập nhật; nếu chưa có, thêm mới
  const index = selectedPoints.findIndex(item => item.type === 'school');
  if (index !== -1) {
    selectedPoints[index] = schoolObj;
  } else {
    selectedPoints.push(schoolObj);
  }
  updateRoute();
}

// Hàm xử lý khi marker của trọ được nhấn
function onDormClick(dorm) {
  const dormObj = {
    type: 'dorm',
    data: {
      lat: parseFloat(dorm.KT_LATITUDE),
      lng: parseFloat(dorm.KT_LONGTITUDE),
      name: dorm.KT_TENKHUTRO ? dorm.KT_TENKHUTRO : dorm.KT_MAKT,
      code: dorm.KT_MAKT
    }
  };
  const index = selectedPoints.findIndex(item => item.type === 'dorm');
  if (index !== -1) {
    selectedPoints[index] = dormObj;
  } else {
    selectedPoints.push(dormObj);
  }
  updateRoute();
}

        // Xử lý nút xóa lựa chọn
        document.getElementById('clearSelection').addEventListener('click', function() {
            selectedPoints = [];
            if (routingControl) {
                map.removeControl(routingControl);
                routingControl = null;
            }
            document.getElementById('routeInfo').innerHTML = 'Chọn một trường và một trọ để chỉ đường.';
        });

    </script>
    
</body>

</html>