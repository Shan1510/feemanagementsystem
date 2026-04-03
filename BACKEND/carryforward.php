<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';


header('Content-Type: application/json');
// File ke top pe add karo — action check
$action = $_POST['action'] ?? 'forward';

if ($action === 'undo') {
    // Carry forward undo karo
    $stmt = $conn->prepare("
        UPDATE payment_months 
        SET carry_forward = 0 
        WHERE student_id = ? AND fee_month = ? AND fee_year = ?
    ");
    $stmt->bind_param("iii", $student_id, $month, $year);
    $stmt->execute();
    $stmt->close();

    // Next month se carried record delete karo
    $nm = $month == 12 ? 1  : $month + 1;
    $ny = $month == 12 ? $year + 1 : $year;

    $stmt = $conn->prepare("
        DELETE FROM payment_months 
        WHERE student_id = ? AND fee_month = ? AND fee_year = ? AND payment_id = 0
    ");
    $stmt->bind_param("iii", $student_id, $nm, $ny);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Undo done!']);
    exit;
}

$student_id = intval($_POST['student_id'] ?? 0);
$month      = intval($_POST['month']      ?? 0);
$year       = intval($_POST['year']       ?? 0);
$next_month = intval($_POST['next_month'] ?? 0);
$next_year  = intval($_POST['next_year']  ?? 0);

if (!$student_id || !$month || !$year || !$next_month || !$next_year) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Student monthly fee fetch karo
$stmt = $conn->prepare("SELECT T_Fee FROM student WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student     = $stmt->get_result()->fetch_assoc();
$stmt->close();
$monthly_fee = floatval($student['T_Fee']);

// Get remaining amount from current month
$stmt = $conn->prepare("
    SELECT COALESCE(SUM(remaining), 0) as total_remaining
    FROM payment_months 
    WHERE student_id = ? AND fee_month = ? AND fee_year = ?
");
$stmt->bind_param("iii", $student_id, $month, $year);
$stmt->execute();
$data      = $stmt->get_result()->fetch_assoc();
$stmt->close();
$remaining = floatval($data['total_remaining'] ?? 0);

if ($remaining <= 0) {
    echo json_encode(['success' => false, 'message' => 'No remaining amount!']);
    exit;
}

// Step 1 — Current month carry_forward = 1 mark karo
$stmt = $conn->prepare("
    UPDATE payment_months 
    SET carry_forward = 1 
    WHERE student_id = ? AND fee_month = ? AND fee_year = ?
");
$stmt->bind_param("iii", $student_id, $month, $year);
$stmt->execute();
$stmt->close();

// Step 2 — Next month student_fee mein record banao
$check = $conn->prepare("
    SELECT id FROM student_fee 
    WHERE student_id = ? AND fee_month = ? AND fee_year = ?
");
$check->bind_param("iii", $student_id, $next_month, $next_year);
$check->execute();
$existing_sf = $check->get_result()->fetch_assoc();
$check->close();

if (!$existing_sf) {
    $ins = $conn->prepare("
        INSERT INTO student_fee (student_id, fee_month, fee_year, status) 
        VALUES (?, ?, ?, 'unpaid')
    ");
    $ins->bind_param("iii", $student_id, $next_month, $next_year);
    $ins->execute();
    $ins->close();
}

// Step 3 — Next month payment_months mein carry forward record banao
// Check karo already exist karta hai ya nahi
$check = $conn->prepare("
    SELECT id, month_fee, remaining 
    FROM payment_months 
    WHERE student_id = ? AND fee_month = ? AND fee_year = ? AND payment_id = 0
");
$check->bind_param("iii", $student_id, $next_month, $next_year);
$check->execute();
$existing_pm = $check->get_result()->fetch_assoc();
$check->close();

if ($existing_pm) {
    // Already hai — update karo
    $new_fee       = floatval($existing_pm['month_fee']) + $remaining;
    $new_remaining = floatval($existing_pm['remaining']) + $remaining;
    $stmt = $conn->prepare("
        UPDATE payment_months 
        SET month_fee = ?, remaining = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ddi", $new_fee, $new_remaining, $existing_pm['id']);
    $stmt->execute();
    $stmt->close();
} else {
    // Naya record insert karo
    $next_due = $monthly_fee + $remaining;
    $stmt = $conn->prepare("
        INSERT INTO payment_months 
        (payment_id, student_id, fee_month, fee_year, month_fee, amount_paid, remaining, status, carry_forward)
        VALUES (0, ?, ?, ?, ?, 0, ?, 'unpaid', 0)
    ");
    $stmt->bind_param("iiidd", $student_id, $next_month, $next_year, $next_due, $next_due);
    $stmt->execute();
    $stmt->close();
}

echo json_encode([
    'success'    => true,
    'amount'     => $remaining,
    'next_month' => $next_month,
    'next_year'  => $next_year,
    'message'    => 'Carry forward done!'
]);