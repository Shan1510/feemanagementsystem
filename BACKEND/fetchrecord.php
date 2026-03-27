<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/admindashboard.php");
    exit;
}

$month          = intval($_POST['month']           ?? 0);
$year           = intval($_POST['year']            ?? 0);
$statuses       = $_POST['status']                 ?? [];
$payment_method = $_POST['payment_method']         ?? 'cash';
$transaction_id = trim($_POST['transaction_id']    ?? '');
$sender_number  = trim($_POST['sender_number']     ?? '');
$card_type      = trim($_POST['card_type']         ?? '');
$amount_paid    = floatval($_POST['amount_paid']   ?? 0);

if (!$month || !$year || empty($statuses)) {
    echo "Invalid data.";
    exit;
}

$allowed_statuses = ['paid', 'unpaid'];
$allowed_methods  = ['cash', 'easypaisa', 'card'];
$payment_method   = in_array($payment_method, $allowed_methods) ? $payment_method : 'cash';

foreach ($statuses as $student_id => $status) {
    $student_id = intval($student_id);
    $status     = in_array($status, $allowed_statuses) ? $status : 'unpaid';

    // Check if student_fee exists
    $stmt = $conn->prepare("SELECT id FROM student_fee WHERE student_id = ? AND fee_month = ? AND fee_year = ?");
    $stmt->bind_param("iii", $student_id, $month, $year);
    $stmt->execute();
    $fee_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($fee_row) {
        // Update student_fee
        $stmt = $conn->prepare("UPDATE student_fee SET status = ? WHERE student_id = ? AND fee_month = ? AND fee_year = ?");
        $stmt->bind_param("siii", $status, $student_id, $month, $year);
        $stmt->execute();
        $stmt->close();
        $fee_id = $fee_row['id'];
    } else {
        // Insert student_fee
        $stmt = $conn->prepare("INSERT INTO student_fee (student_id, fee_month, fee_year, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $student_id, $month, $year, $status);
        $stmt->execute();
        $fee_id = $conn->insert_id;
        $stmt->close();
    }

    // Agar paid hai toh payment_details mein save karo
    if ($status === 'paid' && $amount_paid > 0) {

        // Pehle check karo payment exist karta hai ya nahi
        $stmt = $conn->prepare("SELECT id FROM payment_details WHERE student_fee_id = ?");
        $stmt->bind_param("i", $fee_id);
        $stmt->execute();
        $payment_row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($payment_row) {
            // Update
            $stmt = $conn->prepare("
                UPDATE payment_details 
                SET payment_method = ?, transaction_id = ?,
                    sender_number = ?, card_type = ?, amount_paid = ?
                WHERE student_fee_id = ?
            ");
            $stmt->bind_param("ssssdi", 
                $payment_method, $transaction_id,
                $sender_number, $card_type,
                $amount_paid, $fee_id
            );
        } else {
            // Insert
            $stmt = $conn->prepare("
                INSERT INTO payment_details 
                (student_fee_id, student_id, fee_month, fee_year, 
                 payment_method, transaction_id, sender_number, card_type, amount_paid)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iiiissssd",
                $fee_id, $student_id, $month, $year,
                $payment_method, $transaction_id,
                $sender_number, $card_type, $amount_paid
            );
        }
        $stmt->execute();
        $stmt->close();
    }
}

echo "success";
exit;
























<?php
/*
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$class_name = $_GET['class_name'] ?? '';
$sec        = $_GET['sec']        ?? '';
$month      = $_GET['month']      ?? '';
$year       = $_GET['year']       ?? '';
$students   = $_GET['students']   ?? 0;

// Students fetch karna hai
if ($students && $class_name && $sec && $month && $year) {

    // Class ID lo
    $stmt = $conn->prepare("SELECT id FROM class WHERE class_name = ? AND class_sec = ?");
    $stmt->bind_param("ss", $class_name, $sec);
    $stmt->execute();
    $class_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$class_row) {
        echo json_encode(['students' => [], 'class_id' => 0]);
        exit;
    }

    $class_id = $class_row['id'];

    // Students with fee status
    $stmt = $conn->prepare("
        SELECT s.*, 
               COALESCE(sf.status, 'unpaid') AS status
        FROM student s
        LEFT JOIN student_fee sf 
            ON s.id = sf.student_id 
            AND sf.fee_month = ? 
            AND sf.fee_year = ?
        WHERE s.class_id = ? 
          AND s.is_deleted = 0
        ORDER BY s.student_name
    ");
    $stmt->bind_param("iii", $month, $year, $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $studentList = [];
    while($row = $result->fetch_assoc()) {
        $studentList[] = $row;
    }
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['students' => $studentList, 'class_id' => $class_id]);
    exit;
}

// Sirf sections fetch karna hai
$stmt = $conn->prepare("SELECT class_sec FROM class WHERE class_name = ? ORDER BY class_sec");
$stmt->bind_param("s", $class_name);
$stmt->execute();
$result = $stmt->get_result();

$sections = [];
while($row = $result->fetch_assoc()) {
    $sections[] = $row;
}

header('Content-Type: application/json');
echo json_encode($sections);
*/
?>

