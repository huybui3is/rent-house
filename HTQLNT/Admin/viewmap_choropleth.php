<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bản đồ phân bố khu trọ theo xã</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        html, body { height: 100%; margin: 0; padding: 0; }
        #map { height: 100vh; width: 100%; }
        .info { padding: 6px 8px; background: white; background: rgba(255,255,255,0.8); box-shadow: 0 0 15px rgba(0,0,0,0.2); border-radius: 5px; }
        .legend { line-height: 18px; color: #555; }
        .legend i { width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.7; }
                /* Nút trang chủ, định vị ở góc trên bên trái */
                #homeButton {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            padding: 8px 12px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }
        #homeButton:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

    <!-- Nút Trang Chủ -->
    <button id="homeButton">Trang Chủ</button>

    <div id="map"></div>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Hàm trả về màu dựa trên số lượng khu trọ (dorm_count)
        function getColor(d) {
            return d > 50 ? '#800026' :
                   d > 30 ? '#BD0026' :
                   d > 20 ? '#E31A1C' :
                   d > 10 ? '#FC4E2A' :
                   d > 5  ? '#FD8D3C' :
                   d > 0  ? '#FEB24C' :
                            '#FFEDA0';
        }

        // Hàm style cho từng feature
        function style(feature) {
            return {
                fillColor: getColor(feature.properties.dorm_count),
                weight: 1,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.7
            };
        }

// Hàm hiển thị popup khi di chuột qua feature
function onEachFeature(feature, layer) {
    // Nếu có thuộc tính "@id" và nó bắt đầu bằng "relation/" thì mới xử lý
    if (feature.properties && feature.properties['@id'] && feature.properties['@id'].indexOf('relation/') === 0) {
        if (feature.properties.name) {
            layer.bindPopup("<strong>" + feature.properties.name + "</strong><br>Số khu trọ: " + feature.properties.dorm_count);
            // Thêm sự kiện mouseover/mouseout để tự mở/đóng popup
            layer.on({
                mouseover: function(e) {
                    this.openPopup();
                },
                mouseout: function(e) {
                    this.closePopup();
                }
            });
        }
    }
}




// Điều chỉnh bounding box cho Thành phố Cần Thơ
// Giá trị này được ước tính, bạn có thể tinh chỉnh thêm cho phù hợp
// var canThoBounds = L.latLngBounds(
//     L.latLng(10.15, 105.69),  // Góc Tây Nam (SW)
//     L.latLng(10.11, 105.30)  // Góc Đông Bắc (NE)
// );

// Khởi tạo bản đồ với center và zoom phù hợp
var map = L.map('map', {
    center: [10.1, 105.70], // Tọa độ trung tâm ước tính của Can Tho
    zoom: 10.5,
    // maxBounds: canThoBounds,
    // maxBoundsViscosity: 0.9
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);



        // Load dữ liệu GeoJSON cập nhật từ endpoint update_geojson.php
        fetch('update_geojson.php')
            .then(response => response.json())
            .then(data => {
                L.geoJSON(data, {
                    style: style,
                    onEachFeature: onEachFeature
                }).addTo(map);
            })
            .catch(error => console.log('Lỗi tải dữ liệu:', error));

        // Thêm legend (chú giải)
        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend'),
                grades = [0, 5, 10, 20, 30, 50],
                labels = [];
            for (var i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
                    grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
            }
            return div;
        };
        legend.addTo(map);

                // Xử lý sự kiện click nút Trang Chủ
                document.getElementById('homeButton').addEventListener('click', function() {
            window.location.href = 'admin.php';
        });
    </script>
</body>
</html>
