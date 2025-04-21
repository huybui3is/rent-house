<?php  $page_title = "Trang Chủ"; include('/xampp/htdocs/HTQLNT/HeaderFooter/header.php');?>

<div class="container-fluid">
    <div class="row p-5" style="background: url('images/img5.jpeg') center/cover; height: 700px;">
        <div class="col m-5 g-0" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(5px); color: white;">
            <div class="row d-flex justify-content-center mt-5" style="font-size: 75px; font-family: Arial, Helvetica, sans-serif;">
                Hệ thống quản lý nhà trọ
            </div>
            <div class= "row d-flex justify-content-center m-5" style="font-size: 30px; font-family: Arial, Helvetica, sans-serif;">
                Tìm kiếm và đặt phòng dễ dàng ngay hôm nay!
            </div>
            <div class="row d-flex justify-content-center g-0 pt-1 text-center">
                <!-- Cột 1 -->
                <div class="col d-flex flex-column align-items-center mt-4">
                    <h4 class="mb-3">Bạn cần tìm nhà trọ ?</h4>
                    <a href="/HTQLNT/NguoiDung/timtro.php" class="btn btn-primary text-white px-4 py-2">
                        <h3 class="mt-1 mb-1">Tìm phòng trọ</h3>
                    </a>
                </div>

                <!-- Cột 2 -->
                <div class="col d-flex flex-column align-items-center mt-4">
                    <h4 class="mb-3">Bạn muốn xem nhà trọ của bạn ?</h4>
                    <a href="/HTQLNT/NguoiDung/phongcuaban.php" class="btn btn-primary text-white px-4 py-2">
                        <h3 class="mt-1 mb-1">Phòng trọ của tôi</h3>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('/xampp/htdocs/HTQLNT/HeaderFooter/footer.php'); ?>

