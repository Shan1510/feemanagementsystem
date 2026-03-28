<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . FRONTEND_URL . "addclass.html");
    exit;
}

$classname = trim($_POST['class_name'] ?? '');
$classsec  = trim($_POST['class_sec']  ?? '');

if (!$classname || !$classsec) {
    echo "All fields are required.";
    exit;
}

// Prepared statement ✅
$stmt = $conn->prepare("INSERT INTO class (class_name, class_sec) VALUES (?, ?)");
$stmt->bind_param("ss", $classname, $classsec);

try {
    $stmt->execute();
    $stmt->close();
    header("Location: " . BASE_URL . "admin/admindashboard.php");
    exit;
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        echo "Class already exists!";
    } else {
        echo "Error adding class. Please try again.";
    }
}
?>