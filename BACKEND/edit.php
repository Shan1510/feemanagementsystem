<?php
// edit.php

include 'Master/conection.php';

// Get student ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    die("Invalid student ID");
}

// Fetch student data
$sql = "SELECT s.*, c.class_name, c.class_sec 
        FROM student s 
        LEFT JOIN class c ON s.class_id = c.id 
        WHERE s.id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Student not found");
}

$student = mysqli_fetch_assoc($result);

// Handle form submission
$message = '';
$message_type = ''; // success or error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $das = mysqli_real_escape_string($conn, $_POST['das']);
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $t_fee = mysqli_real_escape_string($conn, $_POST['t_fee']);
    $class_id = mysqli_real_escape_string($conn, $_POST['class_id']);
    
    // Update query
    $update_sql = "UPDATE student SET 
                    DAS = '$das',
                    student_name = '$student_name',
                    father_name = '$father_name',
                    contact_number = '$contact_number',
                    T_Fee = '$t_fee',
                    class_id = '$class_id'
                   WHERE id = $id";
    
    if (mysqli_query($conn, $update_sql)) {
        $message = "Student updated successfully!";
        $message_type = "success";
        

        $result = mysqli_query($conn, $sql);
        $student = mysqli_fetch_assoc($result);
    } else {
        $message = "Error updating student: " . mysqli_error($conn);
        $message_type = "error";
    }
}


$class_sql = "SELECT * FROM class ORDER BY class_name, class_sec";
$class_result = mysqli_query($conn, $class_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #0e0b42ff;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
            flex: 1;
            text-align: center;
            text-decoration: none;
        }
        
        .btn-update {
            background: #142a53ff;
            color: white;
        }
        
        .btn-update:hover {
            background: #1d053dff;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .student-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #23086eff;
        }
        
        .student-info p {
            margin: 5px 0;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Student</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="student-info">
            <p><strong>Student ID:</strong> <?php echo $student['id']; ?></p>
            <p><strong>Current Class:</strong> <?php echo $student['class_name'] . ' - ' . $student['class_sec']; ?></p>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="das">DAS Number:</label>
                <input type="text" id="das" name="das" 
                       value="<?php echo htmlspecialchars($student['DAS']); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="student_name">Student Name:</label>
                <input type="text" id="student_name" name="student_name" 
                       value="<?php echo htmlspecialchars($student['student_name']); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="father_name">Father Name:</label>
                <input type="text" id="father_name" name="father_name" 
                       value="<?php echo htmlspecialchars($student['father_name']); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" 
                       value="<?php echo htmlspecialchars($student['contact_number']); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="t_fee">Total Fee:</label>
                <input type="number" id="t_fee" name="t_fee" 
                       value="<?php echo htmlspecialchars($student['T_Fee']); ?>" 
                       required step="0.01">
            </div>
            
            <div class="form-group">
                <label for="class_id">Class & Section:</label>
                <select id="class_id" name="class_id" required>
                    <option value="">Select Class & Section</option>
                    <?php while($class = mysqli_fetch_assoc($class_result)): ?>
                        <option value="<?php echo $class['id']; ?>" 
                            <?php echo ($class['id'] == $student['class_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name'] . ' - Section ' . $class['class_sec']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-update">Update Student</button>
                <a href="javascript:history.back()" class="btn btn-cancel">Cancel</a>
            </div>
            
            <div style="margin-top: 20px;">
               
            </div>
        </form>
    </div>
    
    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            var das = document.getElementById('das').value;
            var name = document.getElementById('student_name').value;
            var father = document.getElementById('father_name').value;
            var contact = document.getElementById('contact_number').value;
            var fee = document.getElementById('t_fee').value;
            var classSelect = document.getElementById('class_id').value;
            
            if (!das || !name || !father || !contact || !fee || !classSelect) {
                e.preventDefault();
                alert('Please fill in all fields');
                return false;
            }
            
            // Confirm update
            return confirm('Are you sure you want to update this student?');
        });
    </script>
</body>
</html>