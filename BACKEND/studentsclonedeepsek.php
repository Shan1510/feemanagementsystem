<?php
include 'Master/conection.php';

// Handle form submission for inline editing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $das = mysqli_real_escape_string($conn, $_POST['das']);
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $t_fee = mysqli_real_escape_string($conn, $_POST['t_fee']);
    $class_id = mysqli_real_escape_string($conn, $_POST['class_id']);
    
    $update_sql = "UPDATE student SET 
        DAS = '$das',
        student_name = '$student_name',
        father_name = '$father_name',
        contact_number = '$contact_number',
        T_Fee = '$t_fee',
        class_id = '$class_id'
        WHERE id = $id";
    
    if (mysqli_query($conn, $update_sql)) {
        $success = "Student updated successfully!";
        // Refresh the page to show updated data
        echo "<script>setTimeout(function(){ window.location.reload(); }, 1000);</script>";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM student WHERE id = $delete_id";
    if (mysqli_query($conn, $delete_sql)) {
        header("Location: totalbutton.php?msg=Student deleted");
        exit();
    }
}

$sql = "SELECT 
    student.*,
    class.class_name,
    class.class_sec
FROM student
LEFT JOIN class ON student.class_id = class.id";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Table</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .edit-form {
            background-color: #f9f9f9;
            border: 2px solid #4CAF50;
        }
        .edit-form td {
            padding: 15px;
        }
        .edit-form input, .edit-form select {
            padding: 8px;
            margin: 5px 0;
            width: 95%;
        }
        .btn {
            padding: 8px 16px;
            margin: 2px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-edit {
            background-color: #4CAF50;
            color: white;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        .btn-update {
            background-color: #2196F3;
            color: white;
        }
        .btn-cancel {
            background-color: #ff9800;
            color: white;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .hidden {
            display: none;
        }
    </style>
    <script>
        function toggleEditForm(id) {
            var form = document.getElementById('edit-form-' + id);
            var row = document.getElementById('row-' + id);
            
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                row.classList.add('hidden');
            } else {
                form.classList.add('hidden');
                row.classList.remove('hidden');
            }
        }
        
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this student?")) {
                window.location.href = '?delete_id=' + id;
            }
        }
    </script>
</head>
<body>
    <?php if(isset($success)): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form action="savestatus.php" method="post" name="submit">
    <table border="1" cellpadding="10">
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
            // Fetch all classes for dropdown
            $class_sql = "SELECT * FROM class";
            $class_result = mysqli_query($conn, $class_sql);
            $classes = [];
            while($class = mysqli_fetch_assoc($class_result)) {
                $classes[] = $class;
            }
            
            while ($row = mysqli_fetch_assoc($result)) {
                $student_id = $row['id'];
        ?>
                <!-- Normal Row -->
                <tr id="row-<?php echo $student_id; ?>">
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['DAS']; ?></td>
                    <td><?php echo $row['student_name']; ?></td>
                    <td><?php echo $row['father_name']; ?></td>
                    <td><?php echo $row['contact_number']; ?></td>
                    <td><?php echo $row['T_Fee']; ?></td>
                    <td><?php echo $row['class_name']; ?></td>
                    <td><?php echo $row['class_sec']; ?></td>
                    <td>
                        <button type="button" class="btn btn-edit" onclick="toggleEditForm(<?php echo $student_id; ?>)">Edit</button>
                        <button type="button" class="btn btn-delete" onclick="confirmDelete(<?php echo $student_id; ?>)">Delete</button>
                    </td>
                </tr>
                
                <!-- Edit Form Row (Hidden by default) -->
                <tr id="edit-form-<?php echo $student_id; ?>" class="edit-form hidden">
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $student_id; ?>">
                        <input type="hidden" name="update" value="1">
                        
                        <td><?php echo $student_id; ?></td>
                        
                        <td><input type="text" name="das" value="<?php echo htmlspecialchars($row['DAS']); ?>" required></td>
                        
                        <td><input type="text" name="student_name" value="<?php echo htmlspecialchars($row['student_name']); ?>" required></td>
                        
                        <td><input type="text" name="father_name" value="<?php echo htmlspecialchars($row['father_name']); ?>" required></td>
                        
                        <td><input type="text" name="contact_number" value="<?php echo htmlspecialchars($row['contact_number']); ?>" required></td>
                        
                        <td><input type="number" name="t_fee" value="<?php echo htmlspecialchars($row['T_Fee']); ?>" required></td>
                        
                        <td>
                            <select name="class_id" required>
                                <option value="">Select Class</option>
                                <?php foreach($classes as $class): ?>
                                    <option value="<?php echo $class['id']; ?>" 
                                        <?php echo ($class['id'] == $row['class_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class['class_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        
                        <td><?php echo $row['class_sec']; ?></td>
                        
                        <td>
                            <button type="submit" class="btn btn-update">Update</button>
                            <button type="button" class="btn btn-cancel" onclick="toggleEditForm(<?php echo $student_id; ?>)">Cancel</button>
                        </td>
                    </form>
                </tr>
        <?php 
            }
        } else {
            echo "<tr><td colspan='10'>No students found</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <input type="submit" placeholder="Save">
    <br>
    <a href="../FRONTEND/addstudents.html">ADD STUDENT</a>
</body>
</html>