<?php

include __DIR__ . '/../Master/conection.php';
include __DIR__ . '/../Master/user_auth.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sidebar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <div class="sidebar-main">
            <div class="sidebar-logo">
                     <?php if ($current_page != 'dashboard.php'): ?>
            <a href="javascript:history.back()" style="
                   color: white; 
        text-decoration: none; 
        padding: 8px 15px; 
        border-radius: 5px;
        font-size: 28px;
        font-weight: 900;
        display: inline-block;
        line-height: 1;
            ">← </a>
        <?php endif; ?>
                <h2>💰 Fee System</h2>
                <p>Admin Panel</p>
            </div>
            
            <nav class="sidebar-menu">
                <a href="../select_class.php">📅 Monthly Fees</a>
                <a href="../../FRONTEND/addstudents.html">👨‍🎓 Add Student</a>
                <a href="../../FRONTEND/addclass.html">🏫 Add Class</a>
                <a href="../allstudentsuser.php">📊 All Students</a>
                <!-- <a href="user.php">👥 Users</a> -->
                
                <!-- Logout - Goes to login page -->
                <a href="../FRONTEND/login.html" class="logout-link">
                    🚪 Logout (<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'; ?>)
                </a>
            </nav>
        </div>
        
        <!-- Main Content
        <main class="main-content">
            <h1>Welcome, Admin!</h1>
            <p>Fee Management System Dashboard</p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Students</h3>
                    <div class="stat-value"><?php include 'total.php'; ?></div>
                </div>
                <div class="stat-card paid">
                    <h3>Paid Fees</h3>
                    <div class="stat-value"><?php include 'paid.php'; ?></div>
                </div>
                <div class="stat-card pending">
                    <h3>Pending</h3>
                    <div class="stat-value"><?php include 'unpaidbutton.php'?></div>
                </div>
            </div>
            
            <div class="card">
                <h2>🔍 Search by DAS</h2>
                <form method="post" action="../BACKEND/search.php">
                    <input type="search" class="form-control" placeholder="Enter DAS number" name="DAS" required>
                    <input type="number" class="form-control" placeholder="Year (YYYY)" name="year" min="2020" max="2099">
                    <button type="submit" class="btn">Search Student</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html> -->