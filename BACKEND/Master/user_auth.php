<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ye lines zaroor honi chahiye ✅
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    $_SESSION['type'] !== 'user'
) {
    header("Location: ../../FRONTEND/login.php");
    exit();
}