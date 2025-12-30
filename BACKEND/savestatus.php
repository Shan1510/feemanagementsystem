<?php
include 'Master/conection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($_POST as $key => $value) {

        if (strpos($key, 'status_') === 0) {

            $student_id = (int) str_replace('status_', '', $key);
            $status = mysqli_real_escape_string($conn, $value);

            $check = mysqli_query(
                $conn,
                "SELECT id FROM fees WHERE student_id = $student_id"
            );

            if (!$check) {
                die(mysqli_error($conn));
            }

            if (mysqli_num_rows($check) > 0) {
                $sql = "UPDATE fees 
                        SET status = '$status' 
                        WHERE student_id = $student_id";
            } else {
                $sql = "INSERT INTO fees (student_id, status, created_at)
                        VALUES ($student_id, '$status', NOW())";
            }

            if (!mysqli_query($conn, $sql)) {
                die(mysqli_error($conn));
            }
        }
    }

    echo "SAVED SUCCESSFULLY";
    exit;
}
