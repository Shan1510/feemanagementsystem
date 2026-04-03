<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

header('Content-Type: application/json');

$student_id = intval($_POST['student_id'] ?? 0);
$year       = intval($_POST['year']       ?? 0);
$months     = $_POST['months']            ?? [];

if (!$student_id || !$year || empty($months)) {
    echo json_encode(['total_due' => 0]);
    exit;
}

// Student fee
$stmt = $conn->prepare("SELECT T_Fee FROM student WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student     = $stmt->get_result()->fetch_assoc();
$stmt->close();
$monthly_fee = floatval($student['T_Fee']);

$monthNames = [
    1=>'January', 2=>'February', 3=>'March',    4=>'April',
    5=>'May',     6=>'June',     7=>'July',      8=>'August',
    9=>'September',10=>'October',11=>'November', 12=>'December'
];

$total_due   = 0;
$has_carried = false;
$breakdown   = '';

foreach ($months as $m) {
    $m = intval($m);

    $stmt = $conn->prepare("
        SELECT 
            COALESCE(SUM(remaining), 0)  as prev_remaining,
            COALESCE(SUM(month_fee), 0)  as month_fee_total,
            COUNT(*) as has_record
        FROM payment_months 
        WHERE student_id = ? AND fee_month = ? AND fee_year = ?
    ");
    $stmt->bind_param("iii", $student_id, $m, $year);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row['has_record'] > 0 && $row['prev_remaining'] > 0) {
        $due          = floatval($row['prev_remaining']);
        $carried      = $due - $monthly_fee;
        $has_carried  = true;
        $breakdown   .= "<div style='font-size:12px; color:#854d0e; margin-bottom:4px;'>
            ⚠️ {$monthNames[$m]}: Rs. {$monthly_fee} + Rs. {$carried} carried = <b>Rs. {$due}</b>
        </div>";
    } else {
        $due          = $monthly_fee;
        $breakdown   .= "<div style='font-size:12px; color:#64748b; margin-bottom:4px;'>
            {$monthNames[$m]}: Rs. {$due}
        </div>";
    }
    $total_due += $due;
}

echo json_encode([
    'total_due'   => $total_due,
    'has_carried' => $has_carried,
    'breakdown'   => $breakdown
]);