<?php

include __DIR__ . '/../Master/conection.php';

$sql = "SELECT COUNT(DISTINCT student.id) AS total_paid
        FROM student
        LEFT JOIN student_fee ON student.id = student_fee.student_id
        WHERE student_fee.status = 'paid' OR student_fee.status = 'Paid'";

$result=mysqli_query($conn,$sql);
$data=mysqli_fetch_assoc($result);

echo $data['total_paid'];
