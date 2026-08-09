<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/any_auth.php';

$class_name = $_GET['class_name'] ?? '';
$sec        = $_GET['sec']        ?? '';
$month      = $_GET['month']      ?? '';
$year       = $_GET['year']       ?? '';
$students   = $_GET['students']   ?? 0;

// Students fetch karna hai
if ($students && $class_name && $sec && $month && $year) {

    // Class ID lo
    $stmt = $conn->prepare("SELECT id FROM class WHERE class_name = ? AND class_sec = ?");
    $stmt->bind_param("ss", $class_name, $sec);
    $stmt->execute();
    $class_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$class_row) {
        echo json_encode(['students' => [], 'class_id' => 0]);
        exit;
    }

    $class_id = $class_row['id'];

    // Students with fee status
    $stmt = $conn->prepare("
        SELECT s.*,
               COALESCE(sf.status, 'unpaid') AS status
        FROM student s
        LEFT JOIN student_fee sf
            ON s.id = sf.student_id
            AND sf.fee_month = ?
            AND sf.fee_year = ?
        WHERE s.class_id = ?
          AND s.is_deleted = 0
        ORDER BY s.student_name
    ");
    $stmt->bind_param("iii", $month, $year, $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $studentList = [];
    while($row = $result->fetch_assoc()) {
        $studentList[] = $row;
    }
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['students' => $studentList, 'class_id' => $class_id]);
    exit;
}

// Sirf sections fetch karna hai
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