<?php
    session_start();

    // Hủy bỏ tất cả các biến session
    session_unset();

    // Hủy bỏ session
    session_destroy();

    // Xóa session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Chuyển hướng người dùng về trang đăng nhập hoặc trang chủ
    header("Location: dangnhap.php");
    exit();
    ?>