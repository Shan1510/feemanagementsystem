<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$class_name = $_GET['class_name'] ?? '';

// ── STUDENTS MODE ──────────────────────────────────────────
if (isset($_GET['students'])) {
    $sec   = $_GET['sec']   ?? '';
    $month = $_GET['month'] ?? '';
    $year  = $_GET['year']  ?? '';

    // Get class id
    $stmt = $conn->prepare("SELECT id FROM class WHERE class_name = ? AND class_sec = ?");
    $stmt->bind_param("ss", $class_name, $sec);
    $stmt->execute();
    $classRow = $stmt->get_result()->fetch_assoc();
    $class_id = $classRow ? $classRow['id'] : null;

    // Get students with fee status from student_fee table
    $stmt = $conn->prepare("
        SELECT s.id, s.DAS, s.student_name, s.father_name, s.contact_number, s.T_Fee,
               COALESCE(sf.status, 'unpaid') AS status
        FROM student s
        LEFT JOIN student_fee sf
            ON sf.student_id = s.id 
            AND sf.fee_month = ? 
            AND sf.fee_year  = ?
        WHERE s.class_id = ? AND s.is_deleted = 0
        ORDER BY s.student_name
    ");
    $stmt->bind_param("iii", $month, $year, $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode(['class_id' => $class_id, 'students' => $students]);
    exit;
}

// ── SECTIONS MODE (default) ────────────────────────────────
$stmt = $conn->prepare("SELECT class_sec FROM class WHERE class_name = ? ORDER BY Class_sec");
$stmt->bind_param("s", $class_name);
$stmt->execute();
$result = $stmt->get_result();

$sections = [];
while($row = $result->fetch_assoc()) {
    $sections[] = $row;
}

header('Content-Type: application/json');
echo json_encode($sections);