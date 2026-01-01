<?php
session_start();
include 'Master/conection.php';

$DAS=$_POST['DAS'];
$Studentname=$_POST['Studentname'];
$Fathername=$_POST['Fathername'];
$Contactnumbr=$_POST['Contactnumber'];
$Class=$_POST['Class'];
$sec=$_POST['Section'];
$T_fee=$_POST['T_fee'];


$query= "SELECT id FROM class WHERE class_name='$Class' and Class_sec='$sec'";

$result= mysqli_query($conn,$query);

$classdata= mysqli_fetch_assoc($result);

$class_id=$classdata['id'];


$insert = "INSERT INTO student (DAS,student_name, father_name, contact_number, class_id,T_fee)
           VALUES ('$DAS','$Studentname', '$Fathername', '$Contactnumbr', '$class_id','$T_fee')";

 try {
    $result=mysqli_query($conn, $insert);
      echo "Added successful!";
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) { // Duplicate entry error code
        echo "DAS already exists! Please enter new DAS.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
