<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/admindashboard.php");
    exit;
}

$allowed_statuses = ['paid', 'unpaid'];

foreach ($_POST as $key => $value) {
    if (strpos($key, 'status_') === 0) {
        $student_id = intval(str_replace('status_', '', $key));
        $status     = in_array(strtolower($value), $allowed_statuses) ? strtolower($value) : 'unpaid';

        $stmt = $conn->prepare("SELECT id FROM fees WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if ($exists) {
            $stmt = $conn->prepare("UPDATE fees SET status = ? WHERE student_id = ?");
            $stmt->bind_param("si", $status, $student_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO fees (student_id, status, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("is", $student_id, $status);
        }
        $stmt->execute();
        $stmt->close();
    }
}

echo "Saved successfully!";
exit;
?>























<?php
/*
session_start();
include __DIR__.'Master/conection.php';

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
    */
?>

