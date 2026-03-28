<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$DAS         = trim($_POST['DAS']           ?? '');
$Studentname = trim($_POST['Studentname']   ?? '');
$Fathername  = trim($_POST['Fathername']    ?? '');
$Contact     = trim($_POST['Contactnumber'] ?? '');
$Class       = trim($_POST['Class']         ?? '');
$sec         = trim($_POST['Section']       ?? '');
$T_fee       = trim($_POST['T_fee']         ?? '');

if (!$DAS || !$Studentname || !$Fathername || !$Contact || !$Class || !$sec || !$T_fee) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!is_numeric($T_fee) || $T_fee < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid tuition fee amount.']);
    exit;
}

$T_fee = (int) $T_fee;

$stmt = $conn->prepare("SELECT id FROM class WHERE class_name = ? AND Class_sec = ?");
$stmt->bind_param("ss", $Class, $sec);
$stmt->execute();
$classdata = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$classdata) {
    echo json_encode(['success' => false, 'message' => 'Class/Section not found.']);
    exit;
}

$class_id = $classdata['id'];

$stmt = $conn->prepare("INSERT INTO student (DAS, student_name, father_name, contact_number, class_id, T_fee) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssis", $DAS, $Studentname, $Fathername, $Contact, $class_id, $T_fee);

try {
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Student added successfully!']);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        echo json_encode(['success' => false, 'message' => 'DAS already exists! Please enter a new DAS.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding student. Please try again.']);
    }
}
?>