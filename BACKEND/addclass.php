<?php
session_start();
include 'Master/conection.php';

     $classname=$_POST['class_name'];
     $classsec=$_POST['class_sec'];

       
        $query= ("INSERT INTO class (class_name,class_sec) VALUES ('$classname','$classsec')");
        $result=mysqli_query($conn,$query);

        if (!$query)
        {
            echo" error";
        }
        else{

            header ('location:admindashboard.php');
        }
