<?php

$students = [];
$class = $_POST['class'];
$sec = $_POST['sec'];

$conn = new mysqli('localhost', 'root', '', 'feemanagementsystem');

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$query = "SELECT students.*, class.class_name, class.Class_sec
          FROM students
          JOIN class ON students.class_id = class.id
          WHERE class.class_name = '$class'
          AND class.Class_sec = '$sec'";

$result = mysqli_query($conn, $query);

header('Content-Type: application/json');

while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

echo json_encode($students);
?>
