<?php
session_start();
include 'Master/conection.php';


$sql= "SELECT 
    student.*,
    class.class_name,
    class.class_sec
FROM student
LEFT JOIN class ON student.class_id = class.id
WHERE student.is_deleted = 0";


$result=mysqli_query($conn,$sql);

?>
  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Table</title>
    <link href="allstudents.css" rel= "stylesheet">
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
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
 <?php  
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['DAS']}</td>";
            echo "<td>{$row['student_name']}</td>";
            echo "<td>{$row['father_name']}</td>";
            echo "<td>{$row['contact_number']}</td>";
            echo "<td>{$row['T_Fee']}</td>";
            echo "<td>{$row['class_name']}</td>";
            echo "<td>{$row['class_sec']}</td>";

            // STATUS COLUMN
            
            
                  

          // ACTION COLUMN
echo "<td>
        <a href='edit.php?id={$row['id']}'>Edit</a> |
      
      </td>";

echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='10'>No students found</td></tr>";
    }
    ?>
    </tbody>
    
</table>
<input type="submit" placeholder="Save" >;
<br>
<a href="../FRONTEND/addstudents.html">ADD STUDENT</a>

</body>
</html>
</table>  
</body>
</html>