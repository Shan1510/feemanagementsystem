<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$sql = "SELECT 
    student.*,
    class.class_name,
    class.class_sec
FROM student
LEFT JOIN class ON student.class_id = class.id
WHERE student.is_deleted = 0";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Students — Fee Management System</title>
    <link href="admin/admin.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-layout">
    <?php include __DIR__ . '/admin/adminsidebar.php'; ?>

    <main class="main-content">
        <div class="page-container">
            <div class="page-header">
                <div>
                    <h1>📋 All Students</h1>
                    <p>Manage and view every enrolled student</p>
                </div>
                <a href="<?= FRONTEND_URL ?>addstudents.php" class="btn btn-success">
                    ➕ Add Student
                </a>
            </div>

            <div class="card card-table">
                <div class="table-header">
                    <h3>Student Records</h3>
                    <span class="badge badge-muted"><?= mysqli_num_rows($result) ?> records</span>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>DAS</th>
                                <th>Student Name</th>
                                <th>Father Name</th>
                                <th>Contact Number</th>
                                <th>T_Fee</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>{$row['id']}</td>";
                                    echo "<td class='mono strong'>{$row['DAS']}</td>";
                                    echo "<td class='strong'>{$row['student_name']}</td>";
                                    echo "<td>{$row['father_name']}</td>";
                                    echo "<td>{$row['contact_number']}</td>";
                                    echo "<td>Rs. {$row['T_Fee']}</td>";
                                    echo "<td>{$row['class_name']}</td>";
                                    echo "<td><span class='badge badge-muted'>{$row['class_sec']}</span></td>";
                                    echo "<td>
                                            <div class='action-btns'>
                                                <a href='edit.php?id={$row['id']}' class='link-btn edit'>✏️ Edit</a>
                                                <a href='delete.php?id={$row['id']}' 
                                                   onclick=\"return confirm('Are you sure you want to delete?')\"
                                                   class='link-btn del'>🗑️ Delete</a>
                                            </div>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='empty-state'>No students found</td></tr>";
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