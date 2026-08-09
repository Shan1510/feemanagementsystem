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

    // Latest outstanding balance for this month.
    // A fully paid month (latest remaining = 0) must NOT be charged again.
    $stmt = $conn->prepare("
        SELECT payment_id, remaining
        FROM payment_months 
        WHERE student_id = ? AND fee_month = ? AND fee_year = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->bind_param("iii", $student_id, $m, $year);
    $stmt->execute();
    $pm = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($pm) {
        $due = max(0, floatval($pm['remaining']));

        if ($due > 0 && intval($pm['payment_id']) === 0) {
            $carried      = max(0, $due - $monthly_fee);
            $has_carried  = true;
            $breakdown   .= "<div style='font-size:12px; color:#854d0e; margin-bottom:4px;'>
                ⚠️ {$monthNames[$m]}: Rs. {$monthly_fee} + Rs. {$carried} carried = <b>Rs. {$due}</b>
            </div>";
        } elseif ($due > 0) {
            $breakdown   .= "<div style='font-size:12px; color:#64748b; margin-bottom:4px;'>
                {$monthNames[$m]}: Rs. {$due}
            </div>";
        } else {
            $breakdown   .= "<div style='font-size:12px; color:#16a34a; margin-bottom:4px;'>
                {$monthNames[$m]}: Paid - Rs. 0 due
            </div>";
        }
    } else {
        // No payment recorded yet — check if the month was already
        // marked paid via the monthly fee-status toggle.
        $stmt = $conn->prepare("
            SELECT status FROM student_fee
            WHERE student_id = ? AND fee_month = ? AND fee_year = ?
            LIMIT 1
        ");
        $stmt->bind_param("iii", $student_id, $m, $year);
        $stmt->execute();
        $sf = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($sf && strtolower($sf['status']) === 'paid') {
            $due = 0;
            $breakdown .= "<div style='font-size:12px; color:#16a34a; margin-bottom:4px;'>
                {$monthNames[$m]}: Paid - Rs. 0 due
            </div>";
        } else {
            $due          = $monthly_fee;
            $breakdown   .= "<div style='font-size:12px; color:#64748b; margin-bottom:4px;'>
                {$monthNames[$m]}: Rs. {$due}
            </div>";
        }
    }
    $total_due += $due;
}

echo json_encode([
    'total_due'   => $total_due,
    'has_carried' => $has_carried,
    'breakdown'   => $breakdown
]);