<?php

include __DIR__ . '/../Master/conection.php';

include 'fetchtablecode.php';

$sql="SELECT student.*, student_fee.status, student_fee.fee_month, student_fee.fee_year
FROM student
JOIN student_fee ON student.id = student_fee.student_id 
WHERE student_fee.status = 'paid' ";

$result=mysqli_query($conn,$sql);
renderStudentTable($result);

?>

  
