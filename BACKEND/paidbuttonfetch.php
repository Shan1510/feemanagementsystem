<?php
include 'Master/conection.php';

include 'fetchtablecode.php';

$sql="SELECT student.*, fees.status
FROM student
JOIN fees ON student.id = fees.student_id
WHERE fees.status = 'paid'";


$result=mysqli_query($conn,$sql);
renderStudentTable($result);

?>

  
