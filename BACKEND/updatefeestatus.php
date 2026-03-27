<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/admindashboard.php");
    exit;
}

$month    = intval($_POST['month'] ?? 0);
$year     = intval($_POST['year'] ?? 0);
$class_id = intval($_POST['class_id'] ?? 0);
$statuses = $_POST['status'] ?? [];

if (!$month || !$year || empty($statuses)) {
    echo "Invalid data submitted.";
    exit;
}

$allowed_statuses = ['paid', 'unpaid'];

foreach ($statuses as $student_id => $status) {

    $student_id = intval($student_id);
    $status     = in_array(strtolower($status), $allowed_statuses) ? strtolower($status) : 'unpaid';

    // 1. Check if record already exists
    $stmt = $conn->prepare("SELECT id FROM student_fee WHERE student_id = ? AND fee_month = ? AND fee_year = ?");
    $stmt->bind_param("iii", $student_id, $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $student_fee_id = $row['id'];

        // Update status
        $stmt = $conn->prepare("UPDATE student_fee SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $student_fee_id);
        $stmt->execute();
        $stmt->close();

    } else {

        // Insert new record
        $stmt = $conn->prepare("INSERT INTO student_fee (student_id, fee_month, fee_year, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $student_id, $month, $year, $status);
        $stmt->execute();

        $student_fee_id = $stmt->insert_id;
        $stmt->close();
    }

    // 2. Agar student PAID hai to payment_details me insert karo
    if ($status === 'paid') {

        $method = $_POST['payment_method'] ?? 'cash';
        $txn    = $_POST['transaction_id'] ?? null;
        $sender = $_POST['sender_number'] ?? null;
        $card   = $_POST['card_type'] ?? null;
        $amount = $_POST['amount_paid'] ?? 0;

        $stmt = $conn->prepare("
            INSERT INTO payment_details
            (student_fee_id, student_id, fee_month, fee_year, payment_method,
             transaction_id, sender_number, card_type, amount_paid)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iiiissssi",
            $student_fee_id,
            $student_id,
            $month,
            $year,
            $method,
            $txn,
            $sender,
            $card,
            $amount
        );

        $stmt->execute();
        $stmt->close();
    }
}

echo "Fee status updated successfully!";
echo "<br><a href='javascript:history.back()'>Go Back</a>";
?>



























<?php
/*
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/admindashboard.php");
    exit;
}

$month    = intval($_POST['month']    ?? 0);
$year     = intval($_POST['year']     ?? 0);
$class_id = intval($_POST['class_id'] ?? 0);
$statuses = $_POST['status'] ?? [];

if (!$month || !$year || empty($statuses)) {
    echo "Invalid data submitted.";
    exit;
}

$allowed_statuses = ['paid', 'unpaid'];

foreach ($statuses as $student_id => $status) {
    $student_id = intval($student_id);
    $status     = in_array(strtolower($status), $allowed_statuses) ? strtolower($status) : 'unpaid';

    $stmt = $conn->prepare("SELECT id FROM student_fee WHERE student_id = ? AND fee_month = ? AND fee_year = ?");
    $stmt->bind_param("iii", $student_id, $month, $year);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if ($exists) {
        $stmt = $conn->prepare("UPDATE student_fee SET status = ? WHERE student_id = ? AND fee_month = ? AND fee_year = ?");
        $stmt->bind_param("siii", $status, $student_id, $month, $year);
    } else {
        $stmt = $conn->prepare("INSERT INTO student_fee (student_id, fee_month, fee_year, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $student_id, $month, $year, $status);
    }
    $stmt->execute();
    $stmt->close();
}

echo "Fee status updated successfully!";
echo "<br><a href='javascript:history.back()'>Go Back</a>";
*/
?>












<?php
/*
include 'Master/conection.php';

$month = $_POST['month'];
$year = $_POST['year'];
$class_id = $_POST['class_id'];
$statuses = $_POST['status']; // array of student_id => status

foreach($statuses as $student_id => $status){
    // Check if record exists
    $check = $conn->query("SELECT * FROM student_fee WHERE student_id='$student_id' AND fee_month='$month' AND fee_year='$year'");
    
    if($check->num_rows > 0){
        // Update
        $conn->query("UPDATE student_fee SET status='$status' WHERE student_id='$student_id' AND fee_month='$month' AND fee_year='$year'");
    } else {
        // Insert new record if not exists
        $conn->query("INSERT INTO student_fee (student_id, fee_month, fee_year, status) VALUES ('$student_id', '$month', '$year', '$status')");
    }
}

echo "Fee status updated successfully!";
echo "<br><a href='javascript:history.back()'>Go Back</a>";
*/
?>
