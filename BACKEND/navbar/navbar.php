<?php
include '../conection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Navbar</title>
    <link rel="stylesheet" href="navbar.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-logo">
            <a href="#">Fee Management</a>
        </div>
        <div class="navbar-items">
            <form method="post" action="" id="monthYearForm">
                <select id="month-select" name="month">
                    <option value="" disabled selected>Month</option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                <select id="year-select" name="year">
                    <option value="" disabled selected>Year</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                    <option value="2028">2028</option>
                    <option value="2029">2029</option>
                    <option value="2030">2030</option>
                    <option value="2031">2031</option>
                    <option value="2032">2032</option>
                    <option value="2033">2033</option>
                    <option value="2034">2034</option>
                    <option value="2035">2035</option>
                </select>

                <button class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <script>
        function submitIfBothSelected() {
            const month = document.getElementById('month-select').value;
            const year = document.getElementById('year-select').value;
            if(month && year) {
                document.getElementById('monthYearForm').submit();
            }
        }
    </script>
</body>
</html>
