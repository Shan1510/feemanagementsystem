<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../FRONTEND/addstudents.html");
    exit;
}

$DAS         = trim($_POST['DAS']            ?? '');
$Studentname = trim($_POST['Studentname']    ?? '');
$Fathername  = trim($_POST['Fathername']     ?? '');
$Contact     = trim($_POST['Contactnumber']  ?? '');
$Class       = trim($_POST['Class']          ?? '');
$sec         = trim($_POST['Section']        ?? '');
$T_fee       = trim($_POST['T_fee']          ?? '');

if (!$DAS || !$Studentname || !$Fathername || !$Contact || !$Class || !$sec || !$T_fee) {
    echo "All fields are required.";
    exit;
}

$stmt = $conn->prepare("SELECT id FROM class WHERE class_name = ? AND Class_sec = ?");
$stmt->bind_param("ss", $Class, $sec);
$stmt->execute();
$classdata = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$classdata) {
    echo "Class/Section not found.";
    exit;
}

$class_id = $classdata['id'];

$stmt = $conn->prepare("INSERT INTO student (DAS, student_name, father_name, contact_number, class_id, T_fee) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssid", $DAS, $Studentname, $Fathername, $Contact, $class_id, $T_fee);

try {
    $stmt->execute();
    $stmt->close();
    echo "Student added successfully!";
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        echo "DAS already exists! Please enter a new DAS.";
    } else {
        echo "Error adding student. Please try again.";
    }
}
?>


















<?php
/*
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

*/
?>