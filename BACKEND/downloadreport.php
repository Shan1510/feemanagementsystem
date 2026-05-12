<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$month = intval($_GET['month'] ?? 0);
$year  = intval($_GET['year']  ?? 0);

if (!$month || !$year) die("Invalid request");

$monthNames = [
    1=>'January', 2=>'February', 3=>'March',    4=>'April',
    5=>'May',     6=>'June',     7=>'July',      8=>'August',
    9=>'September',10=>'October',11=>'November', 12=>'December'
];

$classes = mysqli_query($conn, "SELECT * FROM class ORDER BY class_name, class_sec");

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Fee_Report_' . $monthNames[$month] . '_' . $year . '.xls"');
header('Cache-Control: max-age=0');

echo '<html><head><meta charset="UTF-8"></head><body>';
echo '<h2>Fee Report - ' . $monthNames[$month] . ' ' . $year . '</h2><br>';

$grand_total_fee       = 0;
$grand_total_collected = 0;
$grand_total_remaining = 0;
$grand_total_students  = 0;

while ($class = mysqli_fetch_assoc($classes)) {
    $class_id  = $class['id'];
    $className = $class['class_name'] . ' - ' . $class['class_sec'];

    $stmt = $conn->prepare("
        SELECT s.DAS, s.student_name, s.father_name, s.contact_number, s.T_Fee,
               COALESCE(sf.status, 'unpaid') AS status,
               MAX(p.payment_method) AS payment_method,
               MAX(p.transaction_id) AS transaction_id,
               COALESCE(SUM(pm.amount_paid), 0) AS amount_paid,
               COALESCE(MAX(pm.remaining), 0) AS remaining,
               MAX(p.payment_date) AS payment_date
        FROM student s
        LEFT JOIN student_fee sf
            ON s.id = sf.student_id
            AND sf.fee_month = ?
            AND sf.fee_year = ?
        LEFT JOIN payment_months pm
            ON s.id = pm.student_id
            AND pm.fee_month = ?
            AND pm.fee_year = ?
        LEFT JOIN payments p
            ON pm.payment_id = p.id
        WHERE s.class_id = ? AND s.is_deleted = 0
        GROUP BY s.id, s.DAS, s.student_name, s.father_name, s.contact_number, s.T_Fee, sf.status
        ORDER BY s.student_name
    ");
    $stmt->bind_param("iiiii", $month, $year, $month, $year, $class_id);
    $stmt->execute();
    $result   = $stmt->get_result();
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();

    if (empty($students)) continue;

    $paid    = count(array_filter($students, fn($s) => $s['status'] === 'paid'));
    $partial = count(array_filter($students, fn($s) => $s['status'] === 'partial'));
    $unpaid  = count($students) - $paid - $partial;

    $class_collected = array_sum(array_column($students, 'amount_paid'));
    $class_fee       = array_sum(array_column($students, 'T_Fee'));
    $class_remaining = $class_fee - $class_collected;

    $grand_total_fee       += $class_fee;
    $grand_total_collected += $class_collected;
    $grand_total_remaining += $class_remaining;
    $grand_total_students  += count($students);

    echo '<h3>' . htmlspecialchars($className) . 
         ' &nbsp;|&nbsp; Students: ' . count($students) .
         ' &nbsp;|&nbsp; Paid: ' . $paid . 
         ' &nbsp;|&nbsp; Partial: ' . $partial . 
         ' &nbsp;|&nbsp; Unpaid: ' . $unpaid . '</h3>';

    echo '<table border="1" cellpadding="5" cellspacing="0">
          <thead style="background:#1e293b; color:white;">
            <tr>
                <th>#</th>
                <th>DAS</th>
                <th>Student Name</th>
                <th>Father Name</th>
                <th>Contact</th>
                <th>Monthly Fee</th>
                <th>Status</th>
                <th>Amount Paid</th>
                <th>Remaining</th>
                <th>Method</th>
                <th>Transaction ID</th>
                <th>Payment Date</th>
            </tr>
          </thead><tbody>';

    foreach ($students as $i => $s) {
        $bg = $s['status'] === 'paid' ? '#d1fae5' : ($s['status'] === 'partial' ? '#fef9c3' : '#fee2e2');
        $amountPaid = ($s['status'] === 'unpaid') ? 'Rs. 0' : 'Rs. ' . number_format($s['amount_paid'], 0);
        $remaining  = ($s['status'] === 'paid')   ? '-'     : 'Rs. ' . number_format($s['remaining'], 0);

        echo '<tr style="background:' . $bg . '">
                <td>' . ($i + 1) . '</td>
                <td>' . htmlspecialchars($s['DAS']) . '</td>
                <td>' . htmlspecialchars($s['student_name']) . '</td>
                <td>' . htmlspecialchars($s['father_name']) . '</td>
                <td>' . htmlspecialchars($s['contact_number']) . '</td>
                <td>Rs. ' . number_format($s['T_Fee'], 0) . '</td>
                <td>' . ucfirst($s['status']) . '</td>
                <td>' . $amountPaid . '</td>
                <td>' . $remaining . '</td>
                <td>' . ucfirst($s['payment_method'] ?? '-') . '</td>
                <td>' . htmlspecialchars($s['transaction_id'] ?? '-') . '</td>
                <td>' . ($s['payment_date'] ?? '-') . '</td>
              </tr>';
    }

    // Class total row
    echo '<tr style="background:#1e293b; color:white; font-weight:bold;">
            <td colspan="5">CLASS TOTAL — ' . htmlspecialchars($className) . '</td>
            <td>Rs. ' . number_format($class_fee, 0) . '</td>
            <td>—</td>
            <td>Rs. ' . number_format($class_collected, 0) . '</td>
            <td>Rs. ' . number_format($class_remaining, 0) . '</td>
            <td colspan="3">—</td>
          </tr>';

    echo '</tbody></table><br><br>';
}

// Grand total summary table
echo '<h2>📊 GRAND TOTAL SUMMARY — ' . $monthNames[$month] . ' ' . $year . '</h2>';
echo '<table border="1" cellpadding="8" cellspacing="0" style="min-width:400px;">
        <thead style="background:#0f172a; color:white;">
          <tr><th>Description</th><th>Amount</th></tr>
        </thead>
        <tbody>
          <tr style="background:#f0fdf4;">
            <td><strong>Total Students</strong></td>
            <td><strong>' . $grand_total_students . '</strong></td>
          </tr>
          <tr style="background:#f0fdf4;">
            <td><strong>Total Fee Billed</strong></td>
            <td><strong>Rs. ' . number_format($grand_total_fee, 0) . '</strong></td>
          </tr>
          <tr style="background:#d1fae5;">
            <td><strong>✅ Total Collected</strong></td>
            <td><strong>Rs. ' . number_format($grand_total_collected, 0) . '</strong></td>
          </tr>
          <tr style="background:#fee2e2;">
            <td><strong>❌ Total Remaining</strong></td>
            <td><strong>Rs. ' . number_format($grand_total_remaining, 0) . '</strong></td>
          </tr>
        </tbody>
      </table>';

echo '</body></html>';