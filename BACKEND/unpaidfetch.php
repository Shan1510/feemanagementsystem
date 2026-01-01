<?php

include 'Master/conection.php';

$sql=$sql="SELECT student.*, student_fee.status, student_fee.fee_month, student_fee.fee_year
FROM student
JOIN student_fee ON student.id = student_fee.student_id 
WHERE student_fee.status = 'unpaid' or  student_fee.status = null   ";

$result=mysqli_query($conn,$sql);

?>
  

<!DOCTYPE html>
<html>
<head>
    <title>Student Table</title>
    <link rel="stylesheet" href="allstudents.css">
</head>
<body>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>DAS</th>
    <th>Name</th>
    <th>Father</th>
    <th>Contact</th>
    <th>Fee</th>
    <th>Status</th>
    <th>Month</th>
    <th>year</th>
</tr>

<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['DAS']}</td>
            <td>{$row['student_name']}</td>
            <td>{$row['father_name']}</td>
            <td>{$row['contact_number']}</td>
            <td>{$row['T_Fee']}</td>
            <td>{$row['status']}</td>
            <td>{$row['fee_month']}</td>
            <td>{$row['fee_year']}</td>

        </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No students found</td></tr>";
}
?>

</table>
</body>
</html>


