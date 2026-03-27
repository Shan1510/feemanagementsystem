<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$student_id = intval($_POST['student_id'] ?? 0);
$month      = intval($_POST['month']      ?? 0);
$year       = intval($_POST['year']       ?? 0);

if (!$student_id || !$month || !$year) {
    echo json_encode(['status' => 'unpaid']);
    exit;
}

// Fee status fetch karo
$stmt = $conn->prepare("SELECT id, status FROM student_fee WHERE student_id = ? AND fee_month = ? AND fee_year = ?");
$stmt->bind_param("iii", $student_id, $month, $year);
$stmt->execute();
$fee = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fee) {
    echo json_encode(['status' => 'unpaid']);
    exit;
}

// Payment details fetch karo
$stmt = $conn->prepare("SELECT * FROM payment_details WHERE student_fee_id = ?");
$stmt->bind_param("i", $fee['id']);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
$stmt->close();

header('Content-Type: application/json');
echo json_encode([
    'status'         => $fee['status'],
    'payment_method' => $payment['payment_method'] ?? 'cash',
    'transaction_id' => $payment['transaction_id'] ?? '',
    'sender_number'  => $payment['sender_number']  ?? '',
    'card_type'      => $payment['card_type']       ?? '',
    'amount_paid'    => $payment['amount_paid']     ?? ''
]);