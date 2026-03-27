<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$class_name = $_GET['class_name'] ?? '';

$stmt = $conn->prepare("SELECT class_sec FROM class WHERE class_name = ? ORDER BY class_sec");
$stmt->bind_param("s", $class_name);
$stmt->execute();
$result = $stmt->get_result();

$sections = [];
while($row = $result->fetch_assoc()) {
    $sections[] = $row;
}

header('Content-Type: application/json');
echo json_encode($sections);