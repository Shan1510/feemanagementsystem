<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'class') {
    $currentClass = intval($_POST['current_class_id'] ?? 0);
    $nextClass    = intval($_POST['next_class_id'] ?? 0);

    if (!$currentClass || !$nextClass) {
        echo json_encode(['success' => false, 'message' => 'Please select both classes!']);
        exit;
    }
    if ($currentClass === $nextClass) {
        echo json_encode(['success' => false, 'message' => 'Current and next class cannot be the same!']);
        exit;
    }

    // Verify both classes exist
    $stmt = $conn->prepare("SELECT id FROM class WHERE id = ?");
    $stmt->bind_param("i", $currentClass);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Current class not found!']);
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT id FROM class WHERE id = ?");
    $stmt->bind_param("i", $nextClass);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Next class not found!']);
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE student SET class_id = ? WHERE class_id = ? AND is_deleted = 0");
    $stmt->bind_param("ii", $nextClass, $currentClass);
    $stmt->execute();

    $count = $conn->affected_rows;
    $stmt->close();

    if ($count == 0) {
        echo json_encode(['success' => false, 'message' => 'No students found in the current class.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => "$count students promoted successfully!"]);
    exit;
}

if ($action === 'individual') {
    $student_id = intval($_POST['student_id'] ?? 0);
    $nextClass  = intval($_POST['next_class_id'] ?? 0);

    if (!$student_id || !$nextClass) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid DAS number and select next class!']);
        exit;
    }

    // Verify next class exists
    $stmt = $conn->prepare("SELECT id FROM class WHERE id = ?");
    $stmt->bind_param("i", $nextClass);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Next class not found!']);
        exit;
    }
    $stmt->close();

    // Verify student exists
    $stmt = $conn->prepare("SELECT id FROM student WHERE id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'Student not found!']);
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE student SET class_id = ? WHERE id = ? AND is_deleted = 0");
    $stmt->bind_param("ii", $nextClass, $student_id);
    $stmt->execute();

    $count = $conn->affected_rows;
    $stmt->close();

    if ($count == 0) {
        echo json_encode(['success' => false, 'message' => 'Could not promote student.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Student promoted successfully!']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
exit;