<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$DAS = trim($_POST['DAS'] ?? '');

if (!$DAS) {
    echo json_encode(['error' => 'Please enter DAS number']);
    exit;
}

// ✅ JOIN add kiya
$stmt = $conn->prepare("
    SELECT s.*, c.class_name, c.class_sec 
    FROM student s
    LEFT JOIN class c ON s.class_id = c.id
    WHERE s.DAS = ? AND s.is_deleted = 0
");
$stmt->bind_param("s", $DAS);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    echo json_encode(['error' => 'No student found for DAS: ' . htmlspecialchars($DAS)]);
    exit;
}

header('Content-Type: application/json');
echo json_encode($data);

?>







<?php
/*
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$DAS = trim($_POST['DAS'] ?? '');

if (!$DAS) {
    echo json_encode(['error' => 'Please enter DAS number']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM student WHERE DAS = ? AND is_deleted = 0");
$stmt->bind_param("s", $DAS);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    echo json_encode(['error' => 'No student found for DAS: ' . htmlspecialchars($DAS)]);
    exit;
}

header('Content-Type: application/json');
echo json_encode($data);
*/
?>