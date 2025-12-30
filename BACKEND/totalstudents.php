<?php

include 'Master/conection.php';
$sql="SELECT COUNT(*) AS total FROM student";

$result=mysqli_query($conn,$sql);
$data=mysqli_fetch_assoc($result);

echo $data['total'];




