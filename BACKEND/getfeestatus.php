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

$stmt = $conn->prepare("SELECT status FROM student_fee WHERE student_id = ? AND fee_month = ? AND fee_year = ?");
$stmt->bind_param("iii", $student_id, $month, $year);
$stmt->execute();
$fee = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Latest payment fetch karo from payments table
$payment = null;
if ($fee) {
    $stmt = $conn->prepare("
        SELECT p.payment_method, p.transaction_id, p.sender_number, p.card_type,
               pm.amount_paid
        FROM payments p
        JOIN payment_months pm ON p.id = pm.payment_id
        WHERE pm.student_id = ? AND pm.fee_month = ? AND pm.fee_year = ?
        ORDER BY p.created_at DESC
        LIMIT 1
    ");
    $stmt->bind_param("iii", $student_id, $month, $year);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode([
    'status'         => $fee['status']              ?? 'unpaid',
    'payment_method' => $payment['payment_method']  ?? 'cash',
    'transaction_id' => $payment['transaction_id']  ?? '',
    'sender_number'  => $payment['sender_number']   ?? '',
    'card_type'      => $payment['card_type']        ?? '',
    'amount_paid'    => $payment['amount_paid']      ?? ''
]);