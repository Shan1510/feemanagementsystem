 <?php
include 'conection.php';


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
JOIN class ON student.class_id = class.id
";

$result = mysqli_query($conn, $sql);


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
            echo "<td>{$row['Class_sec']}</td>";

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