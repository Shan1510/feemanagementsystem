<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

header('Content-Type: application/json');

$payment_id = intval($_GET['payment_id'] ?? 0);

if (!$payment_id) {
    echo json_encode(['success' => false]);
    exit;
}

// Payment info
$stmt = $conn->prepare("
    SELECT p.*, s.student_name, s.father_name, s.DAS, s.contact_number,
           s.T_Fee, c.class_name, c.class_sec
    FROM payments p
    JOIN student s ON p.student_id = s.id
    LEFT JOIN class c ON s.class_id = c.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$payment) {
    echo json_encode(['success' => false]);
    exit;
}

// Payment months
$stmt = $conn->prepare("SELECT * FROM payment_months WHERE payment_id = ? ORDER BY fee_month");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$months = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode([
    'success'        => true,
    'payment_id'     => $payment_id,
    'receipt_number' => $receipt_number,
    'receipt_url'    => BASE_URL . 'printreceipt.php?payment_id=' . $payment_id
]);