<?php
session_start();
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$sql = "SELECT username, email, phonenumber, Password, type
        FROM signup
        WHERE type='user' OR type='admin' OR type=' '";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users — Fee Management System</title>
    <link href="admin/admin.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-layout">
    <?php include __DIR__ . '/admin/adminsidebar.php'; ?>

    <main class="main-content">
        <div class="page-container">
            <div class="page-header">
                <div>
                    <h1>👥 Users</h1>
                    <p>Registered system accounts</p>
                </div>
                <span class="badge badge-muted"><?= mysqli_num_rows($result) ?> users</span>
            </div>

            <div class="card card-table">
                <div class="table-header">
                    <h3>User Accounts</h3>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $typeBadge = ($row['type'] === 'admin')
                                        ? '<span class="badge status-paid">Admin</span>'
                                        : '<span class="badge badge-muted">' . htmlspecialchars(ucfirst($row['type'] ?: 'user')) . '</span>';
                                    echo "<tr>
                                        <td class='strong'>{$row['username']}</td>
                                        <td>{$row['email']}</td>
                                        <td class='mono'>{$row['phonenumber']}</td>
                                        <td>{$typeBadge}</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='empty-state'>No user found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>