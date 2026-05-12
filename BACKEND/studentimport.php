<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

set_time_limit(0);
ini_set('memory_limit', '-1');

$folderPath = 'C:/xampp/htdocs/feeproject/feemanagementsystem/students data/';

$csvFiles = [
    $folderPath . 'fee 0.csv',
    $folderPath . 'fee 1.csv',
    $folderPath . 'fee 2.csv',
    $folderPath . 'fee 3.csv',
    $folderPath . 'fee 4.csv',
    $folderPath . 'fee 5.csv',
    $folderPath . 'fee 6.csv',
    $folderPath . 'fee 7.csv',
    $folderPath . 'fee 9.csv',
    $folderPath . 'fee 10.csv',
    $folderPath . 'fee 11.csv',
    $folderPath . 'fee 12.csv',
    $folderPath . 'fee pre9.csv',
    $folderPath . 'Fee push.csv',
];

echo "📋 Checking files...\n";
foreach ($csvFiles as $file) {
    $status = file_exists($file) ? "✅ Found" : "❌ Missing";
    echo "  $status — " . basename($file) . "\n";
}
echo "\n";
flush();

$conn->autocommit(false);

$stmt = $conn->prepare("
    INSERT IGNORE INTO student (DAS, student_name, father_name, contact_number, class_id, T_Fee)
    VALUES (?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("isssid", $DAS, $student_name, $father_name, $contact_number, $class_id, $T_Fee);

$totalCount   = 0;
$skippedCount = 0;
$errors       = [];

foreach ($csvFiles as $csvPath) {
    $fileName = basename($csvPath);

    if (!file_exists($csvPath)) {
        echo "⚠️  Skipping missing file: $fileName\n";
        flush();
        continue;
    }

    $fileCount  = 0;
    $rowIndex   = 0;
    echo "📂 Processing: $fileName\n";
    flush();

    $file = fopen($csvPath, 'r');

    while (($row = fgetcsv($file)) !== false) {
        // ✅ Row 0 = header row (skip it)
        if ($rowIndex === 0) {
            $rowIndex++;
            continue;
        }
        $rowIndex++;

        // ✅ Skip completely empty rows
        if (empty(array_filter($row))) continue;

        // ✅ Column 0 is blank — real data starts at column index 1
        $DAS            = (int)   preg_replace('/[^0-9]/', '', $row[1]);
        $student_name   =         trim($row[2]);
        $father_name    =         trim($row[3]);
        $contact_number =         trim($row[4]);
        $class_id       = (int)   trim($row[5]);
        $T_Fee          = is_numeric(trim($row[6])) ? (float) trim($row[6]) : null;

        // ✅ Skip rows where DAS is still 0 or student name is empty
        if ($DAS === 0 || empty($student_name)) continue;

        if (!$stmt->execute()) {
            $errors[] = "$fileName — Row failed: " . $stmt->error;
        } else {
            if ($conn->affected_rows > 0) {
                $fileCount++;
                $totalCount++;
            } else {
                $skippedCount++;
            }
        }
    }

    fclose($file);
    $conn->commit();
    echo "  ✅ Done — $fileCount rows imported from $fileName\n\n";
    flush();
}

$conn->autocommit(true);
$stmt->close();
$conn->close();

echo "=============================\n";
echo "✅ All 14 files processed!\n";
echo "📊 Total imported:       $totalCount rows\n";
echo "⏭️  Duplicates skipped:  $skippedCount rows\n";

if (!empty($errors)) {
    echo "\n⚠️  Errors (" . count($errors) . "):\n";
    foreach ($errors as $err) {
        echo "  - $err\n";
    }
}
?>