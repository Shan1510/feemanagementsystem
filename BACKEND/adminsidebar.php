
<?php
include 'Master/conection.php';


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="adminsidebar.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <h2>Admin Dashboard</h2>
        </div>
       <ul class="nav-links">
    <li><a href="addyear.php">Fee monthly</a></li>
    <li><a href="../FRONTEND/addstudents.html">Add Student</a></li>
    <li><a href="../FRONTEND/addclass.html">Add Class</a></li>
    <li><a href="totalbutton.php">Student</a></li>
</ul>


            <!-- Fixed Classes submenu -->
            <li class="has-submenu">
                <a href="#" class="submenu-toggle">Classes ▸</a>
                <ul class="submenu">
                    <?php
                    $result = mysqli_query($conn, "SELECT id, class_name FROM class ORDER BY class_name ASC");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<li>
                                <a href="classcopyhtml.php?class_id=' . $row['id'] . '">'
                                . htmlspecialchars($row['class_name']) .
                              '</a>
                              </li>';
                    }
                    ?>
                </ul>
            </li>

            <li><a href="user.php">Users</a></li>
        </ul>
    </div>

    <!-- Fixed JS for submenu -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.submenu-toggle').forEach(function(toggle){
            toggle.addEventListener('click', function(e){
                e.preventDefault();
                this.nextElementSibling.classList.toggle('open');
            });
        });
    });
    </script>
</body>
</html>
