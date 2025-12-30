<?php
include 'Master/conection.php';
include 'adminsidebar.php';
include 'total.php';
?>


 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="admin.css" rel="stylesheet">
</head>
<body>


    <h1>"ADMIN HERE"</h1>
    <h1>search by DAS</h1> 
    <form method="post" action="../BACKEND/search.php">
        
        <input type="search"  placeholder="Enter the DAS number" name="DAS">
        <label for="year">Enter Year:</label>
<input type="number" id="year" name="year" min="2020" max="2099" step="1" placeholder="YYYY" />
         
    </form>

    <div class="sidebar">
        <a href="addstudents.html">New student</a>
        <a href="classes.html">Classes</a> 
        
    </div>
</body>
</html> 