<?php
include __DIR__ . '/../Master/conection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form method="post" action="monthlyview.php">
        <label for="month">Month:</label>
        <select name="month" id="month" required>
            <option value="" disabled selected>Select Month</option>
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
            <option value="" disabled selected>Select Year</option>
            <?php for($y=2025;$y<=2035;$y++){ echo "<option value='$y'>$y</option>"; } ?>
        </select>
    
        <label for="class">Class:</label>
        <select name="class_id" id="class" required>
            <option value="" disabled selected>Select Class</option>
            <?php
            include '../BACKEND/conection.php';
            $res = $conn->query("SELECT * FROM class");
            while($row = $res->fetch_assoc()){
                echo "<option value='{$row['id']}'>{$row['class_name']}</option>";
            }
            ?>
        </select>
    
        <input type="submit" value="Fetch Students">
    </form>
</body>
</html>
