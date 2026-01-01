<!-- <?php
include 'Master/conection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Fee Management</title>
</head>
<body>
    <form method="post" action="monthlyview.php">
        <label for="month">Month:</label>
        <select name="month" required>
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
        <select name="year" required>
            <option value="" selected>Select Year</option>
            <?php 
            $currentYear = date('Y');
            for($y = $currentYear; $y <= $currentYear + 5; $y++){ 
                echo "<option value='$y'>$y</option>"; 
            } 
            ?>
        </select>
        
        <label for="class_section">Class & Section:</label>
        <select name="class_section" required>
            <option value="" selected>Select Class & Section</option>
            <?php
            // Show all class-section combinations in user-friendly format
            $res = $conn->query("SELECT CONCAT(class_name, ' - Section ', class_sec) as display, 
                                        class_name, class_sec 
                                 FROM class 
                                 ORDER BY class_name, class_sec");
            while($row = $res->fetch_assoc()){
                $value = $row['class_name'] . '|' . $row['class_sec'];
                echo "<option value='$value'>{$row['display']}</option>";
            }
            ?>
        </select>
    
        <br><br>
        <input type="submit" value="Fetch Students">
    </form>
</body>
</html> -->