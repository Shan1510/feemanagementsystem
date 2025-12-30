<?php
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
?>
