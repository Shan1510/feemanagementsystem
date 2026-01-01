<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$password = "";
$database = "feemanagement";

$conn = new mysqli($host, $user, $password, $database);

?>