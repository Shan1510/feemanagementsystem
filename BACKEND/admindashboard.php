<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';
include __DIR__ .'/adminsidebar.php';



?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="admin.css" rel="stylesheet">
</head>
<body>

 
           Main Content
        
            <main class="main-content">
    <div class="dashboard-container">
            <h1>Welcome, Admin!</h1>
            <p>Fee Management System Dashboard</p>
            
            <div class="stats-grid">
    <a href="totalbutton.php" class="stat-card-link">
        <div class="stat-card total">
            <h3>Total Students</h3>
            <div class="stat-value">
                <?php include 'totalstudents.php'; ?>
            </div>
        </div>
    </a>
    <a href="paidbuttonfetch.php" class="stat-card-link">
        <div class="stat-card paid">
            <h3>Paid Fees</h3>
            <div class="stat-value">
                <?php include 'paidbutton.php'; ?>
            </div>
        </div>
    </a>
    <a href="unpaidfetch.php" class="stat-card-link">
        <div class="stat-card pending">
            <h3>Pending</h3>
            <div class="stat-value">
                <?php include 'unpaid.php'; ?>
            </div>
        </div>
    </a>
</div>
            
            <div class="card">
                <h2>🔍 Search by DAS</h2>
                <form method="post" action="../BACKEND/search.php">
                    <input type="search" class="form-control" placeholder="Enter DAS number" name="DAS" required>
                    <input type="number" class="form-control" placeholder="Enter Name" name="name">
                    <button type="submit" class="btn">Search Student</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html> 