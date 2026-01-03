<?php

include 'Master/conection.php';

// $sql= "SELECT COUNT(*) AS total_unpaid
//         FROM student
//         LEFT JOIN student_fee ON student.id = student_fee.student_id
//         WHERE student_fee.status = 'unpaid' OR student_fee.id IS NULL ";
$sql = "SELECT COUNT(DISTINCT student.id) AS total_unpaid
        FROM student
        LEFT JOIN student_fee 
            ON student.id = student_fee.student_id
        WHERE student.is_deleted = 0
          AND (student_fee.status = 'unpaid'
               OR student_fee.id IS NULL)";


$result=mysqli_query($conn,$sql);
$data=mysqli_fetch_assoc($result);

 echo $data['total_unpaid'] ;
 ?>