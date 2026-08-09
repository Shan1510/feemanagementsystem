<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Allow both admin and user roles (cashier workflow pages)
if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    !in_array($_SESSION['type'] ?? '', ['admin', 'user'], true)
) {
    header("Location: ../../FRONTEND/index.php");
    exit();
}