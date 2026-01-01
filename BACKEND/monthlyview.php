<?php
session_start();
include 'Master/conection.php';


if (!isset($_POST['month']) || !isset($_POST['year']) || !isset($_POST['class_name']) || !isset($_POST['class_sec'])) {
    header("Location: select_class.php?error=Please complete all steps");
    exit();
}


$month = $_POST['month'];
$year = $_POST['year'];
$class_name = $_POST['class_name'];
$class_sec = $_POST['class_sec'];


$month_names = [
    1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
    7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'
];


if (!isset($month_names[$month])) {
    header("Location: select_class.php? error=Invalid month selected");
    exit();
}


$class_query = "SELECT id FROM class WHERE class_name = '" . $conn->real_escape_string($class_name) . "' 
                AND class_sec = '" . $conn->real_escape_string($class_sec) . "' LIMIT 1";
$class_result = mysqli_query($conn, $class_query);

if (mysqli_num_rows($class_result) == 0) {
    die("<h3>Error: Class '$class_name - Section $class_sec' not found!</h3>
         <p><a href='select_class.php'>← Go back and try again</a></p>");
}

$class_row = mysqli_fetch_assoc($class_result);
$class_id = $class_row['id'];


$query = "SELECT * FROM student WHERE class_id = '$class_id'";
$students = mysqli_query($conn, $query);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Fee View - Fee Management</title>
    <link href="monthlyview.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <h2>Fee Status - <?php echo $month_names[$month] . " $year - $class_name Section $class_sec"; ?></h2>
        <p>Total Students: <?php echo mysqli_num_rows($students); ?></p>
    </div>
    
    <?php if (mysqli_num_rows($students) > 0): ?>
    <form action='updatefeestatus.php' method='post'>
        <table>
            <tr>
                <th>ID</th>
                <th>DAS</th>
                <th>Student Name</th>
                <th>Father Name</th>
                <th>Contact</th>
                <th>Total Fee</th>
                <th>Fee Status</th>
                
            </tr>
            
            <?php while($student = $students-> mysqli_fetch_assoc()):
                $student_id = $student['id'];
                
                // Get fee status
                $fee_q = $conn->query("SELECT status FROM student_fee WHERE student_id='$student_id' AND fee_month='$month' AND fee_year='$year'");
                $fee = $fee_q->fetch_assoc();
                $status = $fee['status'] ?? 'unpaid';
            ?>
            <tr>
                <td><?php echo $student['id']; ?></td>
                <td><?php echo htmlspecialchars($student['DAS']); ?></td>
                <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                <td><?php echo htmlspecialchars($student['father_name']); ?></td>
                <td><?php echo htmlspecialchars($student['contact_number']); ?></td>
                <td><?php echo $student['T_Fee']; ?></td>
                <td>
                    <label class="status-label">
                        <input type='radio' name='status[<?php echo $student_id; ?>]' value='paid' <?php echo ($status=='paid'?'checked':''); ?>> Paid
                    </label>
                    <label class="status-label">
                        <input type='radio' name='status[<?php echo $student_id; ?>]' value='unpaid' <?php echo ($status=='unpaid'?'checked':''); ?>> Unpaid
                    </label>
                </td>
                <!-- <td class="actions">
                    <a href='edit.php?id=<?php echo $student['id']; ?>'>Edit</a> |
                    <a href='delete.php?id=<?php echo $student['id']; ?>' onclick='return confirm("Delete this student?")'>Delete</a>
                </td> -->
            </tr>
            <?php endwhile; ?>
        </table>
        
        <!-- Hidden inputs -->
        <input type='hidden' name='month' value='<?php echo $month; ?>'>
        <input type='hidden' name='year' value='<?php echo $year; ?>'>
        <input type='hidden' name='class_id' value='<?php echo $class_id; ?>'>
        <input type='hidden' name='class_name' value='<?php echo($class_name); ?>'>
        <input type='hidden' name='class_sec' value='<?php echo ($class_sec); ?>'>
        
        <br>
        <input type='submit' value='Update Fee Status' class='submit-btn'>
    </form>
    <?php else: ?>
    <div style="background: white; padding: 30px; border-radius: 10px; text-align: center;">
        <h3>No students found in <?php echo $class_name . " Section " . $class_sec; ?></h3>
        <p>There are no students enrolled in this class and section.</p>
    </div>
    <?php endif; ?>
    
    <a href='select_class.php' class='back-link'>← Select Another Class</a>
</body>
</html>