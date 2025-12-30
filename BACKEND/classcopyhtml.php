<?php

include 'Master/conection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Table</title>
</head>
<body>
<form action="savestatus.php" method="post" name="submit">
<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>DAS</th>
            <th>Student Name</th>
            <th>Father Name</th>
            <th>Contact Number</th>
            <th>T_Fee</th>
            <th>Class</th>
            <th>Section</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
 <?php include 'fetchclass.php'; ?>
    </tbody>
    
</table>
<input type="submit" placeholder="Save" >;
<br>
<a href="../FRONTEND/addstudents.html">ADD STUDENT</a>

</body>
</html>
