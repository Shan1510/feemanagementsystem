<?php
require_once __DIR__ . '/../config_example.php';

session_start();
session_unset();
session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
header("Location: " . FRONTEND_URL . "index.php");
exit();
?>