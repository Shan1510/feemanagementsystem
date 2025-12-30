<?php
include 'conection.php';

$month = $_POST['month'];
$year = $_POST['year'];
$class_id = $_POST['class_id'];
 $month_names = [
    1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
    7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'
];


$query="SELECT * FROM student WHERE class_id='$class_id'";
$students = mysqli_query($conn,$query);

echo "<h2>Fee Status - ".$month_names[$month]."/$year</h2>";

// echo "<h2>Fee Status - $month/$year</h2>";


echo "<form action='updatefeestatus.php' method='post'>";


echo "<table border='1' cellpadding='10'>
<tr>
    <th>Student ID</th>
    <th>DAS</th>
    <th>Student Name</th>
    <th>Father Name</th>
    <th>Contact Number</th>
    <th>T_Fee</th>
    <th>Fee Status</th>
    <th>Action</th>
</tr>";

while($student = $students->fetch_assoc()){
    $student_id = $student['id'];

    // Get fee status
    $fee_q = $conn->query("SELECT status FROM student_fee WHERE student_id='$student_id' AND fee_month='$month' AND fee_year='$year'");
    $fee = $fee_q->fetch_assoc();
    $status = $fee['status'] ?? 'unpaid';

    echo "<tr>
        <td>{$student['id']}</td>
        <td>{$student['DAS']}</td>
        <td>{$student['student_name']}</td>
        <td>{$student['father_name']}</td>
        <td>{$student['contact_number']}</td>
        <td>{$student['T_Fee']}</td>
        <td>
            <label>
                <input type='radio' name='status[$student_id]' value='paid' ".($status=='paid'?'checked':'')."> Paid
            </label>
            <label>
                <input type='radio' name='status[$student_id]' value='unpaid' ".($status=='unpaid'?'checked':'')."> Not Paid
            </label>
        </td>
        <td>
            <a href='edit.php?id={$student['id']}'>Edit</a> |
            <a href='delete.php?id={$student['id']}'>Delete</a>
        </td>
    </tr>";
}


echo "</table>";

// Hidden inputs
echo "<input type='hidden' name='month' value='$month'>";
echo "<input type='hidden' name='year' value='$year'>";
echo "<input type='hidden' name='class_id' value='$class_id'>";

// Submit button
echo "<br><input type='submit' value='Update Fee Status'>";

echo "</form>";

?>
