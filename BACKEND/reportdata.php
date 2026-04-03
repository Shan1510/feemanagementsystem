<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

header('Content-Type: application/json');

$class_id = intval($_POST['class_id'] ?? 0);
$month    = intval($_POST['month']    ?? 0);
$year     = intval($_POST['year']     ?? 0);

if (!$class_id || !$month || !$year) {
    echo json_encode([]);
    exit;
}

   $stmt = $conn->prepare("
    SELECT s.DAS, s.student_name, s.father_name, s.contact_number, s.T_Fee,
           COALESCE(sf.status, 'unpaid') AS status,
           MAX(p.payment_method) AS payment_method,
           COALESCE(SUM(pm.amount_paid), 0) AS amount_paid
    FROM student s
    LEFT JOIN student_fee sf
        ON s.id = sf.student_id
        AND sf.fee_month = ?
        AND sf.fee_year = ?
    LEFT JOIN payment_months pm
        ON s.id = pm.student_id
        AND pm.fee_month = ?
        AND pm.fee_year = ?
    LEFT JOIN payments p
        ON pm.payment_id = p.id
    WHERE s.class_id = ? AND s.is_deleted = 0
    GROUP BY s.id, s.DAS, s.student_name, s.father_name, s.contact_number, s.T_Fee, sf.status
    ORDER BY s.student_name
");
$stmt->bind_param("iiiii", $month, $year, $month, $year, $class_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();

echo json_encode($students);