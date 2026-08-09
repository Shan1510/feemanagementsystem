<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$student_id     = intval($_POST['student_id']    ?? 0);
$year           = intval($_POST['year']          ?? 0);
$months         = $_POST['months']               ?? [];
$amount_paid    = floatval($_POST['amount_paid'] ?? 0);
$payment_method = $_POST['payment_method']       ?? 'cash';
$transaction_id = trim($_POST['transaction_id']  ?? '');
$sender_number  = trim($_POST['sender_number']   ?? '');
$card_type      = trim($_POST['card_type']       ?? '');
$notes          = trim($_POST['notes']           ?? '');

if (!$student_id || !$year || empty($months) || $amount_paid <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields!']);
    exit;
}

// Student monthly fee fetch
$stmt = $conn->prepare("SELECT T_Fee FROM student WHERE id = ? AND is_deleted = 0");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    echo json_encode(['success' => false, 'message' => 'Student not found!']);
    exit;
}

$monthly_fee = floatval($student['T_Fee']);

// Per month actual due calculate karo
$month_dues = [];
$total_due  = 0;

foreach ($months as $m) {
    $m = intval($m);

    // Latest outstanding balance for this month.
    // A fully paid month (latest remaining = 0) must NOT be charged again.
    $stmt = $conn->prepare("
        SELECT remaining
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
        $month_dues[$m] = max(0, floatval($pm['remaining']));
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
            $month_dues[$m] = 0;
        } else {
            $month_dues[$m] = $monthly_fee;
        }
    }
    $total_due += $month_dues[$m];
}

$overall_remaining = $total_due - $amount_paid;

// Receipt number generate
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM payments");
$stmt->execute();
$count          = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();
$receipt_number = 'RCP-' . YEAR_NOW . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

$payment_date = date('Y-m-d');
$payment_time = date('H:i:s');

// payments table insert
$stmt = $conn->prepare("
    INSERT INTO payments 
    (student_id, receipt_number, total_due, amount_paid, remaining_amount,
     payment_method, transaction_id, sender_number, card_type,
     payment_date, payment_time, notes)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("isdddsssssss",
    $student_id, $receipt_number, $total_due, $amount_paid, $overall_remaining,
    $payment_method, $transaction_id, $sender_number, $card_type,
    $payment_date, $payment_time, $notes
);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $stmt->error]);
    exit;
}

$payment_id = $conn->insert_id;
$stmt->close();

// Amount distribute month by month
$remaining_to_distribute = $amount_paid;

foreach ($months as $month) {
    $month    = intval($month);
    $this_due = $month_dues[$month];

    if ($remaining_to_distribute >= $this_due) {
        $month_paid      = $this_due;
        $month_remaining = 0;
        $month_status    = 'paid';
    } elseif ($remaining_to_distribute > 0) {
        $month_paid      = $remaining_to_distribute;
        $month_remaining = $this_due - $month_paid;
        $month_status    = 'partial';
    } else {
        $month_paid      = 0;
        $month_remaining = $this_due;
        $month_status    = 'unpaid';
    }

    $remaining_to_distribute -= $month_paid;

    // payment_months insert
    $stmt = $conn->prepare("
        INSERT INTO payment_months 
        (payment_id, student_id, fee_month, fee_year, month_fee, amount_paid, remaining, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiiidds",
        $payment_id, $student_id, $month, $year,
        $this_due, $month_paid, $month_remaining, $month_status
    );
    $stmt->execute();
    $stmt->close();

    // ✅ student_fee check — $existing define karo pehle!
    $check = $conn->prepare("
        SELECT id FROM student_fee 
        WHERE student_id = ? AND fee_month = ? AND fee_year = ?
    ");
    $check->bind_param("iii", $student_id, $month, $year);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();
    $check->close();

    if ($existing) {
        $upd = $conn->prepare("UPDATE student_fee SET status = ? WHERE id = ?");
        $upd->bind_param("si", $month_status, $existing['id']);
        $upd->execute();
        $upd->close();
    } else {
        $ins = $conn->prepare("
            INSERT INTO student_fee (student_id, fee_month, fee_year, status) 
            VALUES (?, ?, ?, ?)
        ");
        $ins->bind_param("iiis", $student_id, $month, $year, $month_status);
        $ins->execute();
        $ins->close();
    }
}

echo json_encode([
    'success'        => true,
    'message'        => 'Payment saved!',
    'payment_id'     => $payment_id,
    'receipt_number' => $receipt_number
]);