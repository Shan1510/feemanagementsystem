<?php
session_start();
include __DIR__.'/Master/conection.php';


if (!isset($_POST['month']) || !isset($_POST['year']) || !isset($_POST['class_name'])) {
    header("Location: select_class.php?echo error=Please fill all fields");
    exit();
}


$month = $_POST['month'];
$year = $_POST['year'];
$class_name = $_POST['class_name'];


$month_names = [
    1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
    7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'
];


if (!isset($month_names[$month])) {
    header("Location: select_class.php?error=Invalid month selected");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Section - Fee Management</title>
    <link href="select_section.css" rel="stylesheet">
</head>
<body>
    <div class="form-box">
        <h3>Step 2: Select Section</h3>
        
        <div class="selection-info">
            <strong>Selected:</strong> <?php echo $month_names[$month] . " $year - $class_name"; ?>
        </div>
        
        <form method="post" action="monthlyview.php">
            <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
            <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
            <input type="hidden" name="class_name" value="<?php echo htmlspecialchars($class_name); ?>">
            
            <label for="class_sec">Section:</label>
            <select name="class_sec" id="class_sec" required>
                <option value="" selected>Select Section</option>
                <?php
                $sec_res = $conn->query("SELECT DISTINCT class_sec FROM class 
                                         WHERE class_name = '" . $conn->real_escape_string($class_name) . "' 
                                         ORDER BY class_sec");
                
                if ($sec_res->num_rows > 0) {
                    while($sec_row = $sec_res->fetch_assoc()){
                        echo "<option value='" . htmlspecialchars($sec_row['class_sec']) . "'>" . 
                             htmlspecialchars($sec_row['class_sec']) . "</option>";
                    }
                } else {
                    echo "<option value='' disabled>No sections found for this class</option>";
                }
                ?>
            </select>
            
            <div class="button-group">
                <input type="submit" value="Fetch Students">
                <button type="button" onclick="window.history.back()">← Back</button>
            </div>
        </form>
    </div>
</body>
</html>