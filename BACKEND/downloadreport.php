<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$month = intval($_GET['month'] ?? 0);
$year  = intval($_GET['year']  ?? 0);

if (!$month || !$year) {
    die("Invalid request");
}

$monthNames = [
    1=>'January', 2=>'February', 3=>'March',    4=>'April',
    5=>'May',     6=>'June',     7=>'July',      8=>'August',
    9=>'September',10=>'October',11=>'November', 12=>'December'
];

// All classes fetch karo
$classes = mysqli_query($conn, "SELECT * FROM class ORDER BY class_name, class_sec");

// Excel headers
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Fee_Report_' . $monthNames[$month] . '_' . $year . '.xls"');
header('Cache-Control: max-age=0');

echo '<html><head><meta charset="UTF-8"></head><body>';
echo '<h2>Fee Report - ' . $monthNames[$month] . ' ' . $year . '</h2>';
echo '<br>';

while($class = mysqli_fetch_assoc($classes)) {
    $class_id  = $class['id'];
    $className = $class['class_name'] . ' - ' . $class['class_sec'];

    // Students fetch karo
    $stmt = $conn->prepare("
        SELECT s.DAS, s.student_name, s.father_name, s.contact_number, s.T_Fee,
               COALESCE(sf.status, 'unpaid') AS status,
               pd.payment_method, pd.transaction_id, pd.amount_paid
        FROM student s
        LEFT JOIN student_fee sf 
            ON s.id = sf.student_id 
            AND sf.fee_month = ? 
            AND sf.fee_year = ?
        LEFT JOIN payment_details pd 
            ON sf.id = pd.student_fee_id
        WHERE s.class_id = ? AND s.is_deleted = 0
        ORDER BY s.student_name
    ");
    $stmt->bind_param("iii", $month, $year, $class_id);
    $stmt->execute();
    $result   = $stmt->get_result();
    $students = [];
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();

    if (empty($students)) continue;

    $paid   = count(array_filter($students, fn($s) => $s['status'] === 'paid'));
    $unpaid = count($students) - $paid;

    echo '<h3>' . $className . ' | Paid: ' . $paid . ' | Unpaid: ' . $unpaid . '</h3>';
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<thead style="background:#1e293b; color:white;">
            <tr>
                <th>#</th>
                <th>DAS</th>
                <th>Student Name</th>
                <th>Father Name</th>
                <th>Contact</th>
                <th>Total Fee</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Transaction ID</th>
                <th>Amount Paid</th>
            </tr>
          </thead><tbody>';

    foreach($students as $i => $s) {
        $bgColor = $s['status'] === 'paid' ? '#d1fae5' : '#fee2e2';
        echo '<tr style="background:' . $bgColor . '">
                <td>' . ($i+1) . '</td>
                <td>' . htmlspecialchars($s['DAS']) . '</td>
                <td>' . htmlspecialchars($s['student_name']) . '</td>
                <td>' . htmlspecialchars($s['father_name']) . '</td>
                <td>' . htmlspecialchars($s['contact_number']) . '</td>
                <td>Rs. ' . $s['T_Fee'] . '</td>
                <td>' . ucfirst($s['status']) . '</td>
                <td>' . ucfirst($s['payment_method'] ?? '-') . '</td>
                <td>' . htmlspecialchars($s['transaction_id'] ?? '-') . '</td>
                <td>' . ($s['amount_paid'] ?? '-') . '</td>
              </tr>';
    }

    echo '</tbody></table><br><br>';
}

echo '</body></html>';
?>