<?php
include __DIR__ . '/../Master/conection.php';
include __DIR__ . '/../Master/user_auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard — Fee Management System</title>
    <link href="../admin/admin.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-layout">
    <?php include __DIR__ . '/usersidebar.php'; ?>

    <main class="main-content">
        <div class="page-container">
            <div class="page-header">
                <div>
                    <h1>Welcome, User 👋</h1>
                    <p>Fee Management System Dashboard</p>
                </div>
            </div>

            <div class="stats-grid">
                <a href="../buttons/totalbutton.php" class="stat-card-link">
                    <div class="stat-card total">
                        <h3>🎓 Total Students</h3>
                        <div class="stat-value">
                            <?php include __DIR__ . '/../buttons/totalstudents.php'; ?>
                        </div>
                    </div>
                </a>
                <a href="../buttons/paidbuttonfetch.php" class="stat-card-link">
                    <div class="stat-card paid">
                        <h3>✅ Paid Fees</h3>
                        <div class="stat-value">
                            <?php include __DIR__ . '/../buttons/paidbutton.php'; ?>
                        </div>
                    </div>
                </a>
                <a href="../buttons/unpaidfetch.php" class="stat-card-link">
                    <div class="stat-card unpaid">
                        <h3>⏳ Pending</h3>
                        <div class="stat-value">
                            <?php include __DIR__ . '/../buttons/unpaid.php'; ?>
                        </div>
                    </div>
                </a>
            </div>

            <div class="card">
                <h2>🔍 Search by DAS</h2>
                <form method="post" action="../search.php" class="form-row" style="align-items:flex-end;">
                    <div class="form-field" style="flex:1;margin:0;">
                        <label for="searchDAS">DAS Number</label>
                        <input type="search" class="form-control" id="searchDAS" placeholder="Enter DAS number" name="DAS" required>
                    </div>
                    <div class="form-field" style="margin:0;">
                        <button type="submit" class="btn btn-primary">Search Student</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>