<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/any_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/admindashboard.php");
    exit;
}

$month    = intval($_POST['month']    ?? 0);
$year     = intval($_POST['year']     ?? 0);
$statuses = $_POST['status']          ?? [];

if (!$month || !$year || empty($statuses)) {
    echo "Invalid data.";
    exit;
}

$allowed = ['paid', 'unpaid'];

foreach ($statuses as $student_id => $status) {
    $student_id = intval($student_id);
    $status     = in_array(strtolower($status), $allowed) ? strtolower($status) : 'unpaid';

    // student_fee check
    $stmt = $conn->prepare("SELECT id FROM student_fee WHERE student_id = ? AND fee_month = ? AND fee_year = ?");
    $stmt->bind_param("iii", $student_id, $month, $year);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $stmt = $conn->prepare("UPDATE student_fee SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $row['id']);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO student_fee (student_id, fee_month, fee_year, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $student_id, $month, $year, $status);
        $stmt->execute();
        $stmt->close();
    }
}

echo "success";
exit;