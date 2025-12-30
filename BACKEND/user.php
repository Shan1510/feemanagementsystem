<?php
include 'Master/conection.php';

$sql=" SELECT  username,email,phonenumber, Password, type
FROM signup
WHERE type = 'user' or type = 'admin' or type = ' '";


$result=mysqli_query($conn,$sql);
// $data=mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Table</title>
</head>
<body>

<table border="1" cellpadding="10">
<tr>
    
    
    <th>User Name</th>
    <th>Email</th>
    <th>Contact</th>
    <th>Paasword</th>
    <th>Type</th>
</tr>

<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
            <td>{$row['username']}</td>
            <td>{$row['email']}</td>
            <td>{$row['phonenumber']}</td>
            <td>{$row['Password']}</td>
            <td>{$row['type']}</td>
            
        </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No user found</td></tr>";
}
?>

</table>
</body>
</html>

