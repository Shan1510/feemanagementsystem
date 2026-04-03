<?php
include __DIR__ . '/../Master/conection.php';
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
    <div class="sidebar-main">
        <div class="sidebar-logo">
            <?php if ($current_page != 'admindashboard.php'): ?>
                <a href="javascript:history.back()" style="color:white;text-decoration:none;padding:8px 15px;border-radius:5px;font-size:28px;font-weight:900;display:inline-block;line-height:1;">←</a>
            <?php endif; ?>
              <a href="<?= BASE_URL ?>/admin/admindashboard.php" style="text-decoration: none; color: white;"   style="text-decoration: none; color: white; display: inline-block; transition: opacity 0.3s;"
   onmouseover="this.style.opacity='0.8'" 
   onmouseout="this.style.opacity='1'">>
                
            <h2>💰 Fee System  </h2>
            <p>Admin Panel</p>
            </a>
        </div>
        <nav class="sidebar-menu">
            <a href="<?= BASE_URL ?>monthlyview.php">📅 Monthly Fees</a>
            <a href="<?= FRONTEND_URL ?>addstudents.php">👨‍🎓 Add Student</a>
            <a href="<?= FRONTEND_URL ?>addclass.html">🏫 Add Class</a>
            <a href="<?= BASE_URL ?>allstudents.php">📊 All Students</a>
            <a href="<?= BASE_URL ?>user.php">👥 Users</a>
            <a href="<?= BASE_URL ?>report.php">📊 Monthly Report</a>
            <a href="<?= BASE_URL ?>promote.php">📊Promote</a>
           <form action="<?= BASE_URL ?>Master/logout.php" method="post">
                <button type="submit" style="width:100%;padding:10px;background:#e74c3c;color:white;border:none;border-radius:5px;cursor:pointer;font-size:15px;margin-top:10px;">🚪 Logout</button>
            </form>
        </nav>
    </div>




















<?php
/*
include __DIR__ . '/../Master/conection.php';
// include __DIR__ . '/../Master/admin_auth.php';
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
    <div class="sidebar-main">
        <div class="sidebar-logo">
            <?php if ($current_page != 'admindashboard.php'): ?>
                <a href="javascript:history.back()" style="color:white;text-decoration:none;padding:8px 15px;border-radius:5px;font-size:28px;font-weight:900;display:inline-block;line-height:1;">←</a>
            <?php endif; ?>
            <h2>💰 Fee System</h2>
            <p>Admin Panel</p>
        </div>
        <nav class="sidebar-menu">
            <!-- <a href="../select_class.php">📅 Monthly Fees</a>
            <a href="../../FRONTEND/addstudents.html">👨‍🎓 Add Student</a> -->
            <a href="<?= BASE_URL ?>select_class.php">📅 Monthly Fees</a>
             <a href="<?= FRONTEND_URL ?>addstudents.html">👨‍🎓 Add Student</a>
            <a href="<?=FRONTEND_URL?>addclass.html">🏫 Add Class</a>
            <a href="<?= BASE_URL ?>allstudents.php">📊 All Students</a>
            <a href="<?= BASE_URL ?>user.php">👥 Users</a>
            <form action="../Master/logout.php" method="post">
                <button type="submit" style="width:100%;padding:10px;background:#e74c3c;color:white;border:none;border-radius:5px;cursor:pointer;font-size:15px;margin-top:10px;">🚪 Logout</button>
            </form>
        </nav>
    </div>



*/

?>
<?php
/*
include __DIR__ . '/../Master/conection.php';
include __DIR__ . '/../Master/admin_auth.php';
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
                <a href="../allstudents.php">📊 All Students</a>
                <a href="../user.php">👥 Users</a>
                
                <!-- Logout - Goes to login page -->
               <form action="master/logout.php" method="post">
    <button type="submit">Logout</button>
</form>

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
*/
?>