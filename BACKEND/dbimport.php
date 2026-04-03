<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Increase execution time for large files
set_time_limit(0);
ini_set('memory_limit', '512M');

$csvPath = __DIR__ . '/hifz.csv';

if (!file_exists($csvPath)) {
    die("CSV file not found: $csvPath");
}

// ✅ Turn off autocommit — commit in batches instead of row by row
$conn->autocommit(false);

$file = fopen($csvPath, 'r');
fgetcsv($file); // Skip header

$stmt = $conn->prepare("
    INSERT INTO student (das, student_name, father_name, contact_number, class_id, t_fee, is_deleted)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("isssids", $das, $student_name, $father_name, $contact_number, $class_id, $t_fee, $is_deleted);

$count      = 0;
$batchSize  = 500; // ✅ Commit every 500 rows
$errors     = [];

while (($row = fgetcsv($file)) !== false) {
    if (empty(array_filter($row))) continue;

    $das            = (int)   trim($row[0]);
    $student_name   =         trim($row[1]);
    $father_name    =         trim($row[2]);
    $contact_number =         trim($row[3]);
    $class_id       = (int)   trim($row[4]);
    $t_fee          = is_numeric(trim($row[5])) ? (float) trim($row[5]) : null;
    $is_deleted     = (int)   trim($row[6]);

    if (!$stmt->execute()) {
        $errors[] = "Row $count failed: " . $stmt->error;
    } else {
        $count++;
    }

    // ✅ Commit every 500 rows to free memory
    if ($count % $batchSize === 0) {
        $conn->commit();
        echo "Imported $count rows so far...\n";
        flush(); // Push output to browser in real time
    }
}

// ✅ Commit any remaining rows
$conn->commit();
$conn->autocommit(true);

fclose($file);
$stmt->close();
$conn->close();

echo "\n✅ Done! Successfully imported $count students.\n";

if (!empty($errors)) {
    echo "\n⚠️ Errors (" . count($errors) . "):\n";
    foreach ($errors as $err) {
        echo "  - $err\n";
    }
}
?>