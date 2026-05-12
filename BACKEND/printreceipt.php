    <?php
    include __DIR__ . '/Master/conection.php';
    include __DIR__ . '/Master/admin_auth.php';

    $payment_id = intval($_GET['payment_id'] ?? 0);
    if (!$payment_id) die("Invalid request");

    // Payment + Student info
    $stmt = $conn->prepare("
        SELECT p.*, s.student_name, s.father_name, s.DAS, s.contact_number,
            s.T_Fee, c.class_name, c.class_sec
        FROM payments p
        JOIN student s ON p.student_id = s.id
        LEFT JOIN class c ON s.class_id = c.id
        WHERE p.id = ?
    ");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$p) die("Payment not found");

    // Payment months
    $stmt = $conn->prepare("SELECT * FROM payment_months WHERE payment_id = ? ORDER BY fee_month");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $months = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $monthNames = [
        1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr',
        5=>'May', 6=>'Jun', 7=>'Jul', 8=>'Aug',
        9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dec'
    ];

    // Amount in words
    function amountInWords($amount) {
        $amount = intval($amount);
        $ones = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
                'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen',
                'Seventeen','Eighteen','Nineteen'];
        $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
        
        if ($amount == 0) return 'Zero';
        
        $result = '';
        if ($amount >= 1000) {
            $result .= $ones[floor($amount/1000)] . ' Thousand ';
            $amount %= 1000;
        }
        if ($amount >= 100) {
            $result .= $ones[floor($amount/100)] . ' Hundred ';
            $amount %= 100;
        }
        if ($amount >= 20) {
            $result .= $tens[floor($amount/10)] . ' ';
            $amount %= 10;
        }
        if ($amount > 0) {
            $result .= $ones[$amount] . ' ';
        }
        return trim($result) . ' Only';
    }

    $amountWords = amountInWords($p['amount_paid']);
    $paymentDate = date('d-M-Y', strtotime($p['payment_date']));
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Fee Receipt - <?= $p['receipt_number'] ?></title>
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }

            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                display: flex;
                justify-content: center;
                padding: 20px;
            }

            .receipt-wrapper {
                display: flex;
                gap: 0;
            }

            /* Single receipt */
            .receipt {
                width: 300px;
                background: white;
                border: 1px solid #ccc;
                font-size: 11px;
                position: relative;
            }

            /* Dotted separator between copies */
            .receipt + .receipt {
                border-left: 2px dashed #999;
            }

            /* School Header */
            .school-header {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 12px;
                border-bottom: 1px solid #333;
            }

            .school-logo {
                width: 45px;
                height: 45px;
                border: 1px solid #333;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 8px;
                text-align: center;
                color: #333;
            }

            .school-info { flex: 1; }
            .school-name { font-size: 13px; font-weight: bold; text-align: center; }
            .school-sub  { font-size: 10px; text-align: center; color: #333; }

            /* Bank Info */
            .bank-box {
                border: 1px solid #333;
                margin: 6px 8px;
                padding: 5px 8px;
                text-align: center;
                font-size: 10px;
                line-height: 1.5;
            }

            /* Invoice Info */
            .invoice-info {
                padding: 6px 10px;
                border-bottom: 1px solid #ccc;
                line-height: 1.8;
            }

            .invoice-info table {
                width: 100%;
                border-collapse: collapse;
            }

            .invoice-info td {
                font-size: 11px;
                padding: 1px 0;
                vertical-align: top;
            }

            .invoice-info td:first-child {
                font-weight: bold;
                white-space: nowrap;
                padding-right: 5px;
            }

            /* Fee Table */
            .fee-table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
            }

            .fee-table td, .fee-table th {
                border: 1px solid #ccc;
                padding: 4px 8px;
                font-size: 11px;
            }

            .fee-table .label { font-weight: normal; }
            .fee-table .amount { text-align: right; font-weight: bold; }
            .fee-table .total-row td { font-weight: bold; background: #f5f5f5; }

            /* Amount in words */
            .amount-words {
                padding: 5px 10px;
                font-size: 10px;
                border-bottom: 1px solid #ccc;
            }

            /* Due date */
            .due-date {
                display: flex;
                justify-content: space-between;
                padding: 5px 8px;
                border-bottom: 1px solid #ccc;
            }

            .due-date .box {
                border: 1px solid #333;
                padding: 3px 8px;
                font-size: 10px;
                font-weight: bold;
            }

            /* Terms */
            .terms {
                padding: 6px 10px;
                border-top: 1px solid #ccc;
            }

            .terms ol {
                padding-left: 14px;
                font-size: 9px;
                line-height: 1.6;
                color: #333;
            }

            /* Copy label */
            .copy-label {
                text-align: center;
                font-size: 9px;
                font-weight: bold;
                color: #666;
                padding: 4px;
                border-top: 1px solid #ccc;
                letter-spacing: 1px;
            }

            /* Print styles */
            @media print {
                body { background: white; padding: 0; }
                .no-print { display: none !important; }
                .receipt { border: 1px solid #000; }
            }
        </style>
    </head>
    <body>

    <!-- Print Button -->
    <div class="no-print" style="position:fixed; top:20px; right:20px; display:flex; gap:10px; z-index:999;">
        <button onclick="window.print()"
            style="padding:10px 20px; background:#1e293b; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
            🖨️ Print Receipt
        </button>
        <button onclick="window.close()"
            style="padding:10px 20px; background:#64748b; color:white; border:none; border-radius:8px; font-size:14px; cursor:pointer;">
            ✕ Close
        </button>
    </div>

    <div class="receipt-wrapper">

    <?php
    // 2 copies print hongi — School Copy + Parent Copy
    $copies = ['SCHOOL COPY', 'PARENT COPY'];
    foreach($copies as $copy):
    ?>

    <div class="receipt">

        <!-- School Header -->
        <div class="school-header">
            <div class="school-logo">
                <?php if(file_exists(__DIR__ . '/../images/logo100x295.png')): ?>
                    <img src="<?= BASE_URL ?>../images/logo100x295.png" style="width:43px; height:43px; object-fit:contain;">
                <?php else: ?>
                    LOGO
                <?php endif; ?>
            </div>
            <div class="school-info">
                <div class="school-name">DAR-E-ARQAM SCHOOL</div>
                <div class="school-sub">Your City / Area</div>
                <div class="school-sub">Fee Invoice <?= htmlspecialchars($p['student_name']) ?></div>
            </div>
        </div>

        <!-- Bank Info -->
        <div class="bank-box">
            <strong>Bank Name</strong><br>
            Bank Address<br>
            Account Number: XXXXXXXXXXXXXXXX<br>
            Account Title: Your School Name
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <table>
                <tr>
                    <td>Invoice #:</td>
                    <td><?= htmlspecialchars($p['receipt_number']) ?></td>
                    <td style="text-align:right; font-weight:bold;">Date:</td>
                    <td style="text-align:right;"><?= $paymentDate ?></td>
                </tr>
                <tr>
                    <td>Student Name:</td>
                    <td colspan="3"><?= htmlspecialchars($p['student_name']) ?></td>
                </tr>
                <tr>
                    <td>Father Name:</td>
                    <td colspan="3"><?= htmlspecialchars($p['father_name']) ?></td>
                </tr>
                <tr>
                    <td>Grade/Section:</td>
                    <td><?= htmlspecialchars($p['class_name'] . '-' . $p['class_sec']) ?></td>
                    <td style="text-align:right; font-weight:bold;">Month:</td>
                    <td style="text-align:right;">
                        <?= implode(', ', array_map(fn($m) => $monthNames[$m['fee_month']] . '-' . $m['fee_year'], $months)) ?>
                    </td>
                </tr>
                <tr>
                    <td>DAS #:</td>
                    <td colspan="3"><?= htmlspecialchars($p['DAS']) ?></td>
                </tr>
            </table>
        </div>

        <!-- Fee Table -->
        <table class="fee-table">
            <?php foreach($months as $m): ?>
            <tr>
                <td class="label">
                    Tuition Fee (<?= $monthNames[$m['fee_month']] . '-' . $m['fee_year'] ?>)
                </td>
                <td class="amount">
                    <?= number_format($m['month_fee'], 0) ?>
                </td>
            </tr>
            <?php if($m['remaining'] > 0): ?>
            <tr>
                <td class="label" style="color:#dc2626;">
                    Remaining (<?= $monthNames[$m['fee_month']] . '-' . $m['fee_year'] ?>)
                </td>
                <td class="amount" style="color:#dc2626;">
                    <?= number_format($m['remaining'], 0) ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>

            <tr class="total-row">
                <td class="label"><strong>Amount Payable:</strong></td>
                <td class="amount"><?= number_format($p['total_due'], 0) ?></td>
            </tr>
            <tr>
                <td class="label"><strong>Amount Paid:</strong></td>
                <td class="amount" style="color:#16a34a;"><strong><?= number_format($p['amount_paid'], 0) ?></strong></td>
            </tr>
            <?php if($p['remaining_amount'] > 0): ?>
            <tr>
                <td class="label" style="color:#dc2626;"><strong>Balance Due:</strong></td>
                <td class="amount" style="color:#dc2626;"><strong><?= number_format($p['remaining_amount'], 0) ?></strong></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="label"><strong>Payment Method:</strong></td>
                <td class="amount"><?= ucfirst($p['payment_method']) ?></td>
            </tr>
            <?php if($p['transaction_id']): ?>
            <tr>
                <td class="label">Transaction ID:</td>
                <td class="amount"><?= htmlspecialchars($p['transaction_id']) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="label">Amount Payable After Due Date:</td>
                <td class="amount"></td>
                
            </tr>
        </table>

        <!-- Amount in Words -->
        <div class="amount-words">
            <strong>Amount In Words:</strong> <?= $amountWords ?>
            <?php if($p['notes']): ?>
            <br><strong>Comments:</strong> <?= htmlspecialchars($p['notes']) ?>
            <?php endif; ?>
        </div>

        <!-- Time of Payment -->
        <div style="padding:4px 10px; font-size:10px; border-bottom:1px solid #ccc; color:#555;">
            <strong>Payment Time:</strong> <?= $p['payment_time'] ?>
            &nbsp;&nbsp;&nbsp;
            <strong>Received By:</strong> _______________
        </div>

        <!-- Due Date -->
        <div class="due-date">
            <div class="box">Due Date: <?= $paymentDate ?></div>
            <div class="box">Validity: <?= date('d-M-Y', strtotime($p['payment_date'] . ' +30 days')) ?></div>
        </div>

        <!-- Terms -->
        <div class="terms">
            <ol>
                <li>Fee paid after the due date is subjected to a fine/charity of Rs.50/ per day until the expiry of voucher.</li>
                <li>Name would be struck off on non-payment of two consecutive months.</li>
                <li>Ensuring the timely receipt of fee voucher is the responsibility of parents.</li>
                <li>Parents must retain their copy of the paid fee voucher in safe custody for future reference.</li>
                <li>Fee once paid is not transferable and non-refundable.</li>
                <li>Fee will not be accepted without the fee voucher.</li>
                <li>Fee will be enhanced after one year.</li>
                <li>We reserve all legal rights and remedies.</li>
            </ol>
        </div>

        <!-- Copy Label -->
        <div class="copy-label"><?= $copy ?></div>

    </div>

    <?php endforeach; ?>

    </div>

    </body>
    </html>