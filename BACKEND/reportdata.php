<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

header('Content-Type: application/json');

$class_id = intval($_POST['class_id'] ?? 0);
$month    = intval($_POST['month']    ?? 0);
$year     = intval($_POST['year']     ?? 0);

if (!$class_id || !$month || !$year) {
    echo json_encode(['students' => [], 'total_fee' => 0, 'total_collected' => 0, 'total_remaining' => 0, 'total_students' => 0]);
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        s.id,
        s.DAS, 
        s.student_name, 
        s.father_name, 
        s.contact_number, 
        s.T_Fee,
        MAX(p.payment_method) AS payment_method,
        COALESCE(SUM(CASE WHEN pm.payment_id > 0 THEN pm.amount_paid ELSE 0 END), 0) AS amount_paid,
        COALESCE(MAX(CASE WHEN pm.payment_id > 0 THEN pm.remaining ELSE NULL END), 0) AS remaining,
        COALESCE(
            MAX(CASE WHEN pm.payment_id > 0 THEN pm.status ELSE NULL END),
            sf.status,
            'unpaid'
        ) AS status
    FROM student s
    LEFT JOIN student_fee sf
        ON s.id = sf.student_id
        AND sf.fee_month = ?
        AND sf.fee_year  = ?
    LEFT JOIN payment_months pm
        ON s.id = pm.student_id
        AND pm.fee_month = ?
        AND pm.fee_year  = ?
    LEFT JOIN payments p
        ON pm.payment_id = p.id AND pm.payment_id > 0
    WHERE s.class_id = ? AND s.is_deleted = 0
    GROUP BY s.id, s.DAS, s.student_name, s.father_name, 
             s.contact_number, s.T_Fee, sf.status
    ORDER BY s.student_name
");

if (!$stmt) {
    echo json_encode(['error' => 'Query prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("iiiii", $month, $year, $month, $year, $class_id);
$stmt->execute();
$result = $stmt->get_result();

$students        = [];
$total_collected = 0;
$total_remaining = 0;
$total_fee       = 0;

while ($row = $result->fetch_assoc()) {
    $total_fee       += floatval($row['T_Fee']);
    $total_collected += floatval($row['amount_paid']);
    $total_remaining += floatval($row['remaining']);
    $students[]       = $row;
}
$stmt->close();

echo json_encode([
    'students'        => $students,
    'total_fee'       => $total_fee,
    'total_collected' => $total_collected,
    'total_remaining' => $total_remaining,
    'total_students'  => count($students),
]);