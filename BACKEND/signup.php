<?php

$u_name = $_POST['username'];
$Email = $_POST['Email'];
$p_number = $_POST['Phonenumber'];
$pass = $_POST['Password'];
$c_pass = $_POST['ConfirmPassword'];
$type=null;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$password=password_hash($pass,PASSWORD_BCRYPT);

$passregex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
$emailregex="/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

if(!$u_name || !$Email || !$p_number || !$pass || !$c_pass ) {
    
    echo "All fields are required.";
    exit;
}


if(!preg_match($emailregex,$Email)){
    echo "Invalid email format";
  exit;  
} 


if ($pass != $c_pass) {
    echo "Passwords do not match.";
    exit;
}

if (!preg_match($passregex, $pass)) {
    echo "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one digit, and one special character.";
    exit;
}
       
        
        $conn= new mysqli('localhost','root','','feemanagement');
        if ($conn->connect_error) {
    die("Database connection failed");
}
        $query= ("INSERT INTO signup (username,email,Phonenumber,type,Password) VALUES ('$u_name','$Email','$p_number','$type','$password')");
        
        
        
        
        try {
    $result=mysqli_query($conn, $query);
   
    }
catch (mysqli_sql_exception $error) {
    if ($error->getCode() == 1062) { // Duplicate entry error code
        echo "Email already exists! Please use another email.";
    } else {
        echo "signup succesful";
    }
}

    


?>

