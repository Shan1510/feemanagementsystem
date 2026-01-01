<?php
session_start();
include 'Master/conection.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Class - Fee Management</title>
    <link rel="stylesheet" href="select_class.css">
</head>
<body>
    <div class="form-box">
        <h3>Step 1: Select Month, Year and Class</h3>
        
        <form method="post" action="select_section.php">
            <label for="month">Month:</label>
            <select name="month" id="month" required>
                <option value="" selected>Select Month</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
        
            <label for="year">Year:</label>
            <select name="year" id="year" required>
                <option value="" selected>Select Year</option>
                <?php 
                $currentYear = date('Y');
                for($y = $currentYear; $y <= $currentYear + 5; $y++){ 
                    echo "<option value='$y'>$y</option>"; 
                } 
                ?>
            </select>
            
            <label for="class_name">Class:</label>
            <select name="class_name" id="class_name" required>
                <option value="" selected>Select Class</option>
                <?php
                $res = $conn->query("SELECT DISTINCT class_name FROM class ORDER BY class_name");
                while($row = $res->fetch_assoc()){
                    echo "<option value='{$row['class_name']}'>{$row['class_name']}</option>";
                }
                ?>
            </select>
            
            <input type="submit" value="Next: Select Section →">
        </form>
    </div>
</body>
</html>