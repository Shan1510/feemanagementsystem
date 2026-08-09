<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$classname = trim($_POST['class_name'] ?? '');
$classsec  = trim($_POST['class_sec']  ?? '') ?: null; // NULL if empty

if (!$classname) {
    echo json_encode(['success' => false, 'message' => 'Class name is required.']);
    exit;
}

// if section is null, use "is null" check to avoid duplicate
$checkSql = $classsec
    ? "SELECT id FROM class WHERE class_name = ? AND class_sec = ?"
    : "SELECT id FROM class WHERE class_name = ? AND class_sec IS NULL";

if ($classsec) {
    $check = $conn->prepare($checkSql);
    $check->bind_param("ss", $classname, $classsec);
} else {
    $check = $conn->prepare($checkSql);
    $check->bind_param("s", $classname);
}
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Class already exists!']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO class (class_name, class_sec) VALUES (?, ?)");
$stmt->bind_param("ss", $classname, $classsec);

try {
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Class added successfully!']);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error adding class. Please try again.']);
}
?>


















<?php
/*
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
*/
?>