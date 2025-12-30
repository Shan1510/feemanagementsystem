<?php
session_start();
$email=$_POST['Email'];
$pass=$_POST['password'];


$conn=new mysqli('localhost','root','','feemanagement');
$query= "SELECT * FROM signup  WHERE email='$email'";

$result= mysqli_query($conn,$query);
$data= mysqli_fetch_assoc($result);


if($data)
   {
      if(password_verify($pass,$data['Password']))
         
         {
            
            $_SESSION ['type'] =$data['type'];
            $_SESSION ['Email'] =$data['email'];
            $_SESSION ['logged_in'] =true;
        
            if($_SESSION['type'] == 'admin')
            {
               header('location:admindashboard.php');
               exit;
            }

       else
    {
        header('location:userdashboard.php');
        exit;
    }
     }
     else
     {
        echo "Invalid Password";
        
     }
    }
    else{
        echo"Invalid email";
    }
