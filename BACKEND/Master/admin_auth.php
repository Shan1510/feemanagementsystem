<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    $_SESSION['type'] !== 'admin'
) {
    header("Location:FRONTEND/login.html");
    exit();
}
