<?php

include 'Master/conection.php';


$sql="SELECT 
    student.*,
    COALESCE(student_fee.status, 'unpaid') AS status
FROM student
LEFT JOIN student_fee 
    ON student.id = student_fee.student_id;
";


$result=mysqli_query($conn,$sql);

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
            <th>Status</th>
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
            echo "<td>{$row['status']}</td>";

            // STATUS COLUMN
            
            echo "<td>
                    <label>
                        <input type='radio' name='status_{$row['id']}' value='paid'> Paid
                    </label>
                    <label>
                        <input type='radio' name='status_{$row['id']}' value='unpaid'> Not Paid
                    </label>
                  </td>";
                  

            // ACTION COLUMN
            echo "<td>
                    <a href='edit.php?id={$row['id']}'>Edit</a> |
                    <a href='delete.php?id={$row['id']}'>Delete</a>
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