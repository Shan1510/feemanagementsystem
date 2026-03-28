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

    // 1. Check if record exists in student_fee table
    $check_stmt = $conn->prepare("SELECT id FROM student_fee WHERE student_id = ? AND fee_month = ? AND fee_year = ?");
    $check_stmt->bind_param("iii", $student_id, $month, $year);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $student_fee_id = $row['id'];
        
        // UPDATE existing student_fee record
        $update_stmt = $conn->prepare("UPDATE student_fee SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $status, $student_fee_id);
        $update_stmt->execute();
        $update_stmt->close();
        
    } else {
        // INSERT new student_fee record
        $insert_stmt = $conn->prepare("INSERT INTO student_fee (student_id, fee_month, fee_year, status) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("iiis", $student_id, $month, $year, $status);
        $insert_stmt->execute();
        $student_fee_id = $insert_stmt->insert_id;
        $insert_stmt->close();
    }

    // 2. Handle payment_details - UPDATE if exists, INSERT if not
    if ($status === 'paid') {
        $method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cash';
        $txn    = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : null;
        $sender = isset($_POST['sender_number']) ? $_POST['sender_number'] : null;
        $card   = isset($_POST['card_type']) ? $_POST['card_type'] : null;
        $amount = isset($_POST['amount_paid']) ? floatval($_POST['amount_paid']) : 0;
        
        // Check if payment already exists for this student_fee_id
        $check_payment = $conn->prepare("SELECT id FROM payment_details WHERE student_fee_id = ?");
        $check_payment->bind_param("i", $student_fee_id);
        $check_payment->execute();
        $payment_result = $check_payment->get_result();
        
        if ($payment_result->num_rows > 0) {
            // UPDATE existing payment record
            $update_payment = $conn->prepare("
                UPDATE payment_details 
                SET payment_method = ?, 
                    transaction_id = ?, 
                    sender_number = ?, 
                    card_type = ?, 
                    amount_paid = ?,
                    paid_at = NOW()
                WHERE student_fee_id = ?
            ");
            $update_payment->bind_param("sssssi", $method, $txn, $sender, $card, $amount, $student_fee_id);
            $update_payment->execute();
            $update_payment->close();
        } else {
            // INSERT new payment record
            $insert_payment = $conn->prepare("
                INSERT INTO payment_details 
                (student_fee_id, student_id, fee_month, fee_year, payment_method,
                 transaction_id, sender_number, card_type, amount_paid, paid_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $insert_payment->bind_param(
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
            $insert_payment->execute();
            $insert_payment->close();
        }
        $check_payment->close();
        
    } else {
        // If status is unpaid, delete payment details if exists
        $delete_payment = $conn->prepare("DELETE FROM payment_details WHERE student_fee_id = ?");
        $delete_payment->bind_param("i", $student_fee_id);
        $delete_payment->execute();
        $delete_payment->close();
    }
    
    $check_stmt->close();
}

echo "Fee status updated successfully!";
echo "<br><a href='javascript:history.back()'>Go Back</a>";
?>