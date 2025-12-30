<?php

include 'conection.php';

$sql= "SELECT COUNT(DISTINCT student.id) AS total_unpaid
FROM student
LEFT JOIN fees ON student.id = fees.student_id
WHERE fees.status = 'unpaid' OR fees.status IS NULL
";

$result=mysqli_query($conn,$sql);
$data=mysqli_fetch_assoc($result);

echo $data['total_unpaid'];
