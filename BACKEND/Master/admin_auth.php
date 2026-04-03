<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers HAMESHA send hone chahiye ✅
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    $_SESSION['type'] !== 'admin'
) {
     header("Location: " . FRONTEND_URL . "index.php");
    exit();
}