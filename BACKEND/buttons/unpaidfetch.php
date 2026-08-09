<?php
include __DIR__ . '/../Master/conection.php';
include __DIR__ . '/../Master/admin_auth.php';

$monthNames = [
    1 => 'January',  2 => 'February', 3 => 'March',    4 => 'April',
    5 => 'May',      6 => 'June',     7 => 'July',      8 => 'August',
    9 => 'September',10 => 'October', 11 => 'November', 12 => 'December'
];

$sql    = "SELECT student.*, student_fee.status, student_fee.fee_month, student_fee.fee_year FROM student LEFT JOIN student_fee ON student.id = student_fee.student_id WHERE (student_fee.status = 'unpaid' OR student_fee.id IS NULL) AND student.is_deleted = 0";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unpaid Students — Fee Management System</title>
    <link href="../admin/admin.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-layout">
    <?php include __DIR__ . '/../admin/adminsidebar.php'; ?>

    <main class="main-content">
        <div class="page-container">
            <div class="page-header">
                <div>
                    <h1>⏳ Unpaid Students</h1>
                    <p>Records with an outstanding or unrecorded fee</p>
                </div>
                <span class="badge status-unpaid"><?= mysqli_num_rows($result) ?> unpaid</span>
            </div>

            <div class="card card-table">
                <div class="table-header">
                    <h3>Unpaid Records</h3>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>DAS</th>
                                <th>Name</th>
                                <th>Father</th>
                                <th>Contact</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th>Month</th>
                                <th>Year</th>
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
                                <td><span class="badge status-unpaid">Unpaid</span></td>
                                <td><?= ($m = (int)($row['fee_month'] ?? 0)) ? ($monthNames[$m] ?? '-') : '-' ?></td>
                                <td><?= htmlspecialchars($row['fee_year'] ?? '-') ?></td>
                            </tr>
                            <?php endwhile;
                        else: ?>
                            <tr><td colspan="9" class="empty-state">No unpaid students found</td></tr>
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