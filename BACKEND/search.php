<?php
session_start();
$DAS=$_POST['DAS'];

$conn=new mysqli('localhost','root','','feemanagement');
$query= "SELECT * FROM student WHERE DAS='$DAS'";

$result= mysqli_query($conn,$query);

$classdata= mysqli_fetch_assoc($result);


if ($classdata) {
    echo "Student Name: " . $classdata['student_name'] . "<br>";
    echo "Father Name: " . $classdata['father_name'] . "<br>";
} else {
    echo "No record found.";
}
?>