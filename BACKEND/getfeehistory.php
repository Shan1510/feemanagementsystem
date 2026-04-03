<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

header('Content-Type: application/json');

$student_id = intval($_POST['student_id'] ?? 0);
$year       = intval($_POST['year']       ?? 0);

if (!$student_id || !$year) {
    echo json_encode([]);
    exit;
}

// Student monthly fee
$stmt = $conn->prepare("SELECT T_Fee FROM student WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student     = $stmt->get_result()->fetch_assoc();
$stmt->close();
$monthly_fee = floatval($student['T_Fee'] ?? 0);

// All months fetch karo including carry forward entries
$stmt = $conn->prepare("
    SELECT 
        sf.fee_month,
        sf.fee_year,
        sf.status,
        COALESCE(SUM(CASE WHEN pm.payment_id > 0 THEN pm.amount_paid ELSE 0 END), 0) AS amount_paid,
        COALESCE(MAX(CASE WHEN pm.payment_id > 0 THEN pm.remaining ELSE NULL END), 0) AS remaining,
        COALESCE(MAX(pm.carry_forward), 0) AS carry_forward,
        -- Check if this month has carried amount from previous month
        COALESCE(MAX(CASE WHEN pm.payment_id = 0 THEN pm.month_fee ELSE NULL END), 0) AS carried_due,
        MAX(p.payment_date)    AS payment_date,
        MAX(p.receipt_number)  AS receipt_number
    FROM student_fee sf
    LEFT JOIN payment_months pm 
        ON sf.student_id = pm.student_id 
        AND sf.fee_month  = pm.fee_month 
        AND sf.fee_year   = pm.fee_year
    LEFT JOIN payments p 
        ON pm.payment_id = p.id AND pm.payment_id > 0
    WHERE sf.student_id = ? AND sf.fee_year = ?
    GROUP BY sf.fee_month, sf.fee_year, sf.status
    ORDER BY sf.fee_month ASC
");
$stmt->bind_param("ii", $student_id, $year);
$stmt->execute();
$result  = $stmt->get_result();
$history = [];
while($row = $result->fetch_assoc()) {
    // Carried amount calculate karo
    $carried = floatval($row['carried_due']) > $monthly_fee 
        ? floatval($row['carried_due']) - $monthly_fee 
        : 0;
    $row['carried_amount'] = $carried;
    $row['monthly_fee']    = $monthly_fee;
    $history[] = $row;
}
$stmt->close();

echo json_encode($history);