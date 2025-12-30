<?php
include 'Master/conection.php';

$sql = "
SELECT student.id,
       student.DAS,
       student.student_name,
       student.father_name,
       student.contact_number,
       student.T_Fee,
       class.class_name,
       class.Class_sec
FROM student
JOIN class
ON student.class_id = class.id
";


$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Table</title>
</head>
<body>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>DAS</th>
                <th>Student Name</th>
                <th>Father Name</th>
                <th>Contact Number</th>
                <th>T_Fee </th>
                <th>Class</th>
                <th>Section</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        <?php
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        echo "<tr>";
        echo "<td>". $row['id'] ."</td>";
        echo "<td>". $row['DAS'] ."</td>";
        echo "<td>". $row['student_name'] ."</td>";
        echo "<td>". $row['father_name'] ."</td>";
        echo "<td>". $row['contact_number'] ."</td>";
        echo "<td>". $row['T_Fee'] ."</td>";
        echo "<td>". $row['class_name'] ."</td>";
        echo "<td>". $row['Class_sec'] ."</td>";
echo "<td>"
        <label>
            <input type='radio' name='status_{$row['id']}' value='paid'> ;
        </label>
        <label>
            <input type='radio' name='status_{$row['id']}' value='unpaid'> ;
        </label> 

        echo "<td>
                <a href='edit.php?id=".$row['id']."'>Edit</a> |
                <a href='delete.php?id=".$row['id']."'>Delete</a>
              "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No students found</td></tr>";
}
?>

        ?>
<a href="../FRONTEND/addstudents.html">ADD STUDENT</a>


        </tbody>
    </table>
</body>
</html>
