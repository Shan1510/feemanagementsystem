<?php
include __DIR__ . '/../Master/conection.php';
include __DIR__ . '/../Master/admin_auth.php';

$sql    = "SELECT student.*, COALESCE(student_fee.status, 'unpaid') AS status 
           FROM student 
           LEFT JOIN student_fee ON student.id = student_fee.student_id 
           WHERE student.is_deleted = 0";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Students — Fee Management System</title>
    <link href="../admin/admin.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-layout">
    <?php include __DIR__ . '/../admin/adminsidebar.php'; ?>

    <main class="main-content">
        <div class="page-container">
            <div class="page-header">
                <div>
                    <h1>🎓 All Students</h1>
                    <p>Complete student records</p>
                </div>
                <span class="badge badge-muted"><?= mysqli_num_rows($result) ?> records</span>
            </div>

            <div class="card card-table">
                <div class="table-header">
                    <h3>Student Records</h3>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>DAS</th>
                                <th>Student Name</th>
                                <th>Father Name</th>
                                <th>Contact</th>
                                <th>Fee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td class="mono strong"><?= htmlspecialchars($row['DAS']) ?></td>
                                <td class="strong"><?= htmlspecialchars($row['student_name']) ?></td>
                                <td><?= htmlspecialchars($row['father_name']) ?></td>
                                <td><?= htmlspecialchars($row['contact_number']) ?></td>
                                <td>Rs. <?= htmlspecialchars($row['T_Fee']) ?></td>
                                <td>
                                    <?php $st = strtolower($row['status'] ?? 'unpaid'); ?>
                                    <span class="badge status-<?= $st === 'paid' ? 'paid' : 'unpaid' ?>">
                                        <?= ucfirst(htmlspecialchars($st)) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile;
                        else: ?>
                            <tr><td colspan="7" class="empty-state">No students found</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>