<?php
session_start();
include 'Master/conection.php';

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$id = intval($_GET['id']);
$success = "";
$error = "";


if (isset($_POST['confirm_delete'])) {

    $sql = "UPDATE student SET is_deleted = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Student  deleted successfully.";
    } else {
        $error = "Something went wrong. Try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Student</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        p {
            text-align: center;
            font-size: 16px;
            color: #555;
        }
        .btn {
            padding: 10px 20px;
            background: #15084eff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn-cancel {
            background: #f44336;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .error {
            color: #f44336;
            margin-bottom: 20px;
            padding: 10px;
            background: #ffebee;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            color: #142261ff;
            margin-bottom: 20px;
            padding: 10px;
            background: #e8f5e8;
            border-radius: 5px;
            text-align: center;
        }
        .btn-group {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Delete Student</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
        <div class="btn-group">
            <a href="allstudents.php" class="btn">Go Back</a>
        </div>
    <?php else: ?>
        <p>Are you sure you want to delete this student?</p>

        <form method="post">
            <div class="btn-group">
                <button type="submit" name="confirm_delete" class="btn">Yes, Delete</button>
                <a href="allstudents.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
