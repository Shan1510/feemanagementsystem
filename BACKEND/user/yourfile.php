<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "feemanagement";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$csvFile = "../FRONTEND/play group.csv";

if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip header
    fgetcsv($handle);

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        // Skip empty DAS rows
        if (empty(trim($data[0]))) {
            continue;
        }

        $DAS            = $conn->real_escape_string($data[0]);
        $student_name   = $conn->real_escape_string($data[1]);
        $father_name    = $conn->real_escape_string($data[2]);
        $contact_number = $conn->real_escape_string($data[3]);
        $T_Fee          = $conn->real_escape_string($data[4]);
        $class_id       = $conn->real_escape_string($data[5]);

        $sql = "INSERT INTO student
                (DAS, student_name, father_name, contact_number, T_Fee, class_id)
                VALUES
                ('$DAS', '$student_name', '$father_name', '$contact_number', '$T_Fee', '$class_id')";

        if (!$conn->query($sql)) {
            echo "❌ Failed DAS $DAS : " . $conn->error . "<br>";
        }
    }

    fclose($handle);
    echo "✅ Students imported successfully!";
} else {
    echo "❌ Could not open CSV file.";
}

$conn->close();
?>
