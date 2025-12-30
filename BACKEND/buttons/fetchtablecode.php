<?php
function renderStudentTable($result) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Table</title>
</head>
<body>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>DAS</th>
    <th>Name</th>
    <th>Father</th>
    <th>Contact</th>
    <th>Fee</th>
    <th>Status</th>
</tr>

<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['DAS']}</td>
            <td>{$row['student_name']}</td>
            <td>{$row['father_name']}</td>
            <td>{$row['contact_number']}</td>
            <td>{$row['T_Fee']}</td>
            <td>{$row['status']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No students found</td></tr>";
}
?>

</table>
</body>
</html>

<?php } ?>
