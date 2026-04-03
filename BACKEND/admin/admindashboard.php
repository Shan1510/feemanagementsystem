<?php
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

include __DIR__ . '/../Master/conection.php';
include __DIR__ . '/../Master/admin_auth.php';
include __DIR__ . '/adminsidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="admin.css" rel="stylesheet">
</head>
<body>
    <main class="main-content">
        <div class="dashboard-container">
            <h1>Welcome, Admin!</h1>
            <p>Fee Management System Dashboard</p>

            <div class="stats-grid">
                <a href="<?= BASE_URL ?>buttons/totalbutton.php" class="stat-card-link">
                    <div class="stat-card total">
                        <h3>Total Students</h3>
                        <div class="stat-value">
                            <?php include __DIR__ . '/../buttons/totalstudents.php'; ?>
                        </div>
                    </div>
                </a>
                <a href="<?= BASE_URL ?>buttons/paidbuttonfetch.php" class="stat-card-link">
                    <div class="stat-card paid">
                        <h3>Paid Fees</h3>
                        <div class="stat-value">
                            <?php include __DIR__ . '/../buttons/paidbutton.php'; ?>
                        </div>
                    </div>
                </a>
                <a href="<?= BASE_URL ?>buttons/unpaidfetch.php" class="stat-card-link">
                    <div class="stat-card pending">
                        <h3>Pending</h3>
                        <div class="stat-value">
                            <?php include __DIR__ . '/../buttons/unpaid.php'; ?>
                        </div>
                    </div>
                </a>
            </div>

            <div class="card">
                <h2>🔍 Search by DAS</h2>
                <form onsubmit="searchStudent(event)">
                    <input type="search" class="form-control"
                           placeholder="Enter DAS number"
                           id="searchDAS" required>
                    <button type="submit" class="btn">Search Student</button>
                </form>
            </div>
        </div>
    </main>

<!-- PAYMENT POPUP -->
<div id="searchOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
<div style="background:white; border-radius:14px; width:580px; max-height:92vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.3);">

    <!-- Header -->
    <div style="background:#1e293b; padding:14px 18px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:10;">
        <div>
            <p style="color:#f1f5f9; font-size:15px; font-weight:600; margin:0;">Payment Entry</p>
            <p style="color:#94a3b8; font-size:12px; margin:4px 0 0 0;" id="popupDAS"></p>
        </div>
        <button onclick="closePopup()" style="background:#e74c3c; color:white; border:none; border-radius:6px; padding:5px 12px; cursor:pointer; font-size:16px;">✕</button>
    </div>

    <!-- Student Info -->
    <div style="padding:16px 18px; border-bottom:1px solid #f1f5f9;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div style="background:#f8fafc; padding:10px 12px; border-radius:8px;">
                <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase;">Student</p>
                <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;" id="popupName"></p>
            </div>
            <div style="background:#f8fafc; padding:10px 12px; border-radius:8px;">
                <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase;">Father</p>
                <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;" id="popupFather"></p>
            </div>
            <div style="background:#f8fafc; padding:10px 12px; border-radius:8px;">
                <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase;">Class</p>
                <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;" id="popupClass"></p>
            </div>
            <div style="background:#f8fafc; padding:10px 12px; border-radius:8px;">
                <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase;">Monthly Fee</p>
                <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;" id="popupFee"></p>
            </div>
        </div>
    </div>

    <!-- Year -->
    <div style="padding:14px 18px; border-bottom:1px solid #f1f5f9;">
        <p style="font-size:12px; font-weight:600; color:#64748b; margin:0 0 8px 0; text-transform:uppercase;">Year</p>
        <select id="popupYear" style="width:100%; padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:14px; outline:none;" onchange="loadFeeHistory(); updateTotalDue()">
            <option value="">Select Year</option>
            <?php
            $cy = date('Y');
            for($y = $cy; $y >= $cy - 5; $y--) {
                echo "<option value='$y'" . ($y == $cy ? ' selected' : '') . ">$y</option>";
            }
            ?>
        </select>
    </div>

    <!-- Month Selection -->
    <div style="padding:14px 18px; border-bottom:1px solid #f1f5f9;">
        <p style="font-size:12px; font-weight:600; color:#64748b; margin:0 0 10px 0; text-transform:uppercase;">Select Months</p>
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:6px;" id="monthGrid">
            <?php
            $monthList = ['Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'May'=>5,'Jun'=>6,
                          'Jul'=>7,'Aug'=>8,'Sep'=>9,'Oct'=>10,'Nov'=>11,'Dec'=>12];
            foreach($monthList as $name => $num): ?>
            <label id="mlabel_<?= $num ?>"
                onclick="toggleMonth(<?= $num ?>, this)"
                style="display:flex; align-items:center; justify-content:center; padding:8px 4px; border-radius:8px; border:0.5px solid #e2e8f0; background:#f8fafc; color:#64748b; cursor:pointer; font-size:12px; font-weight:500; transition:all 0.2s; user-select:none;">
                <?= $name ?>
            </label>
            <?php endforeach; ?>
        </div>
        <p style="font-size:12px; color:#64748b; margin:8px 0 0 0;">Selected: <span id="selectedMonthsText">None</span></p>
    </div>

    <!-- Fee History -->
    <div id="feeHistorySection" style="display:none; padding:14px 18px; border-bottom:1px solid #f1f5f9;">
        <p style="font-size:12px; font-weight:600; color:#64748b; margin:0 0 10px 0; text-transform:uppercase;">
            Fee History — <span id="historyYear"></span>
        </p>
        <div id="feeHistoryTable"></div>
    </div>

    <!-- Amount -->
    <div style="padding:14px 18px; border-bottom:1px solid #f1f5f9;">
        <p style="font-size:12px; font-weight:600; color:#64748b; margin:0 0 10px 0; text-transform:uppercase;">Amount</p>
        <div style="display:flex; gap:10px; margin-bottom:10px;">
            <div style="flex:1; background:#f8fafc; padding:10px 12px; border-radius:8px; border:1px solid #e2e8f0;">
                <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase;">Total Due</p>
                <p style="font-size:16px; font-weight:700; color:#0f172a; margin:0;" id="totalDue">Rs. 0</p>
            </div>
            <div style="flex:1;">
                <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase;">Amount Paying</p>
                <input type="number" id="amountPaying" placeholder="Enter amount"
                    style="width:100%; padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:14px; outline:none;"
                    oninput="updateRemaining()">
            </div>
        </div>
        <div style="background:#f8fafc; padding:10px 14px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
            <p style="font-size:13px; color:#64748b; margin:0;">Remaining after payment:</p>
            <p style="font-size:14px; font-weight:700; margin:0;" id="remainingAmount">Rs. 0</p>
        </div>
    </div>

    <!-- Payment Method -->
    <div style="padding:14px 18px; border-bottom:1px solid #f1f5f9;">
        <p style="font-size:12px; font-weight:600; color:#64748b; margin:0 0 10px 0; text-transform:uppercase;">Payment Method</p>
        <div style="display:flex; gap:8px; margin-bottom:10px;">
            <label id="cashLbl" onclick="selectMethod('cash')"
                style="flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:10px; border-radius:8px; border:2px solid #1e293b; background:#1e293b; color:white; cursor:pointer; font-size:13px; font-weight:500;">
                💵 Cash
            </label>
            <label id="easypaisaLbl" onclick="selectMethod('easypaisa')"
                style="flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:10px; border-radius:8px; border:1px solid #e2e8f0; background:#f8fafc; color:#64748b; cursor:pointer; font-size:13px;">
                📱 EasyPaisa
            </label>
            <label id="cardLbl" onclick="selectMethod('card')"
                style="flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:10px; border-radius:8px; border:1px solid #e2e8f0; background:#f8fafc; color:#64748b; cursor:pointer; font-size:13px;">
                💳 Card
            </label>
        </div>

        <!-- EasyPaisa Details -->
        <div id="easypaisaDetails" style="display:none; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
            <div style="display:flex; flex-direction:column; gap:8px;">
                <input type="text" id="ep_transaction" placeholder="Transaction ID"
                    style="padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; width:100%;">
                <input type="text" id="ep_sender" placeholder="Sender Number (03XXXXXXXXX)"
                    style="padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; width:100%;">
            </div>
        </div>

        <!-- Card Details -->
        <div id="cardDetails" style="display:none; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
            <div style="display:flex; flex-direction:column; gap:8px;">
                <input type="text" id="card_transaction" placeholder="Transaction ID"
                    style="padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; width:100%;">
                <select id="card_type_select"
                    style="padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; width:100%;">
                    <option value="">Select Card Type</option>
                    <option value="visa">Visa</option>
                    <option value="mastercard">Mastercard</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <div style="padding:14px 18px; border-bottom:1px solid #f1f5f9;">
        <p style="font-size:12px; font-weight:600; color:#64748b; margin:0 0 8px 0; text-transform:uppercase;">Notes (Optional)</p>
        <textarea id="paymentNotes" placeholder="Any additional notes..."
            style="width:100%; padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; resize:none; height:70px;"></textarea>
    </div>

    <!-- Save Button -->
    <div style="padding:14px 18px; display:flex; gap:8px;">
        <button onclick="savePayment()" id="savePayBtn"
            style="flex:1; padding:12px; background:#1e293b; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
            💾 Save & Generate Receipt
        </button>
        <button onclick="closePopup()"
            style="padding:12px 18px; background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:8px; font-size:14px; cursor:pointer;">
            Cancel
        </button>
    </div>

    <!-- Save Message -->
    <div id="saveMsg" style="display:none; margin:0 18px 14px; padding:10px 15px; border-radius:8px; font-size:14px; font-weight:600;"></div>

</div>
</div>

<script>
let currentStudentId  = null;
let currentStudentFee = 0;
let selectedMonths    = [];
let currentMethod     = 'cash';

const monthNames = {
    1:'January', 2:'February', 3:'March',    4:'April',
    5:'May',     6:'June',     7:'July',      8:'August',
    9:'September',10:'October',11:'November', 12:'December'
};

const monthShort = {
    1:'Jan', 2:'Feb', 3:'Mar',  4:'Apr',
    5:'May', 6:'Jun', 7:'Jul',  8:'Aug',
    9:'Sep', 10:'Oct',11:'Nov', 12:'Dec'
};

// Search student
function searchStudent(e) {
    e.preventDefault();
    let DAS = document.getElementById('searchDAS').value.trim();
    if (!DAS) return;

    fetch('<?= BASE_URL ?>search.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'DAS=' + encodeURIComponent(DAS)
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }

        currentStudentId  = data.id;
        currentStudentFee = parseFloat(data.T_Fee);

        document.getElementById('popupDAS').innerText    = 'DAS: ' + data.DAS;
        document.getElementById('popupName').innerText   = data.student_name;
        document.getElementById('popupFather').innerText = data.father_name;
        document.getElementById('popupClass').innerText  = (data.class_name ?? 'N/A') + ' - ' + (data.class_sec ?? '');
        document.getElementById('popupFee').innerText    = 'Rs. ' + data.T_Fee + ' / month';

        // Reset everything
        selectedMonths = [];
        document.querySelectorAll('[id^="mlabel_"]').forEach(lbl => {
            lbl.style.background = '#f8fafc';
            lbl.style.border     = '0.5px solid #e2e8f0';
            lbl.style.color      = '#64748b';
        });
        updateMonthUI();
        document.getElementById('amountPaying').value        = '';
        document.getElementById('totalDue').innerText        = 'Rs. 0';
        document.getElementById('remainingAmount').innerText = 'Rs. 0';
        document.getElementById('remainingAmount').style.color = '#64748b';
        document.getElementById('paymentNotes').value        = '';
        document.getElementById('saveMsg').style.display     = 'none';
        document.getElementById('feeHistorySection').style.display = 'none';
        selectMethod('cash');

        document.getElementById('popupYear').value = '<?= date('Y') ?>';
        loadFeeHistory();

        document.getElementById('searchOverlay').style.display = 'flex';
    })
    .catch(() => alert('Something went wrong!'));
}

function loadFeeHistory() {
    let year = document.getElementById('popupYear').value;
    if (!year || !currentStudentId) return;

    document.getElementById('historyYear').innerText = year;

    fetch('<?= BASE_URL ?>getfeehistory.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `student_id=${currentStudentId}&year=${year}`
    })
    .then(r => r.json())
    .then(data => {
        let section = document.getElementById('feeHistorySection');
        let table   = document.getElementById('feeHistoryTable');

        if (data.length === 0) {
            section.style.display = 'none';
            return;
        }

        section.style.display = 'block';

        let html = `<table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr style="background:#1e293b;">
                    <th style="padding:8px 10px; color:white; text-align:left;">Month</th>
                    <th style="padding:8px 10px; color:white; text-align:center;">Status</th>
                    <th style="padding:8px 10px; color:white; text-align:right;">Paid</th>
                    <th style="padding:8px 10px; color:white; text-align:right;">Remaining</th>
                    <th style="padding:8px 10px; color:white; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>`;

        data.forEach(row => {
            let statusColor = row.status === 'paid'    ? '#d1fae5' :
                              row.status === 'partial'  ? '#fef9c3' : '#fee2e2';
            let textColor   = row.status === 'paid'    ? '#065f46' :
                              row.status === 'partial'  ? '#854d0e' : '#991b1b';
            let statusEmoji = row.status === 'paid'    ? '✅' :
                              row.status === 'partial'  ? '⚠️' : '❌';

            // Carried amount note
            let carriedNote = '';
            if (row.carried_amount > 0) {
                carriedNote = `<br><span style="color:#854d0e; font-size:10px;">
                    📌 Includes Rs.${parseFloat(row.carried_amount).toFixed(0)} from prev month
                </span>`;
            }

            // Action buttons
            let actionBtns = '';

            // Remaining hai aur carry forward nahi hua
            if (row.remaining > 0 && row.carry_forward == 0) {
                let nm = row.fee_month == 12 ? 1  : parseInt(row.fee_month) + 1;
                let ny = row.fee_month == 12 ? parseInt(row.fee_year) + 1 : row.fee_year;
                actionBtns += `
                    <button onclick="carryForward(${row.fee_month}, ${row.fee_year}, ${nm}, ${ny})"
                        style="padding:3px 7px; background:#fef9c3; color:#854d0e; 
                               border:1px solid #fde68a; border-radius:5px; 
                               font-size:10px; font-weight:600; cursor:pointer; 
                               display:block; width:100%; margin-bottom:3px;">
                        ➕ Add to ${monthShort[nm]}
                    </button>`;
            }

            // Carry forward hua tha — undo button
            if (row.carry_forward == 1) {
                actionBtns += `
                    <button onclick="undoCarryForward(${row.fee_month}, ${row.fee_year})"
                        style="padding:3px 7px; background:#fee2e2; color:#991b1b; 
                               border:1px solid #fca5a5; border-radius:5px; 
                               font-size:10px; font-weight:600; cursor:pointer;
                               display:block; width:100%; margin-bottom:3px;">
                        ↩️ Undo Forward
                    </button>`;
            }

            // Mark unpaid button — agar paid ya partial hai
            if (row.status === 'paid' || row.status === 'partial') {
                actionBtns += `
                    <button onclick="markUnpaid(${row.fee_month}, ${row.fee_year})"
                        style="padding:3px 7px; background:#fee2e2; color:#991b1b; 
                               border:1px solid #fca5a5; border-radius:5px; 
                               font-size:10px; font-weight:600; cursor:pointer;
                               display:block; width:100%;">
                        ❌ Mark Unpaid
                    </button>`;
            }

            // Carry forwarded badge
            if (row.carry_forward == 1 && row.remaining == 0) {
                actionBtns = `<span style="font-size:10px; color:#16a34a; font-weight:600;">✅ Forwarded</span>`;
            }

            html += `<tr style="border-bottom:0.5px solid #f1f5f9;">
                <td style="padding:8px 10px; color:#0f172a;">
                    ${monthNames[row.fee_month]}
                    ${carriedNote}
                </td>
                <td style="padding:8px 10px; text-align:center;">
                    <span style="font-size:10px; padding:2px 7px; border-radius:20px; 
                        background:${statusColor}; color:${textColor}; font-weight:600;">
                        ${statusEmoji} ${row.status.charAt(0).toUpperCase() + row.status.slice(1)}
                    </span>
                </td>
                <td style="padding:8px 10px; text-align:right; color:#16a34a; font-weight:600;">
                    Rs. ${parseFloat(row.amount_paid).toFixed(0)}
                </td>
                <td style="padding:8px 10px; text-align:right; font-weight:600;
                    color:${row.remaining > 0 ? '#dc2626' : '#16a34a'};">
                    ${row.remaining > 0 ? 'Rs. ' + parseFloat(row.remaining).toFixed(0) : '-'}
                </td>
                <td style="padding:8px 10px; text-align:center; min-width:100px;">
                    ${actionBtns}
                </td>
            </tr>`;
        });

        html += '</tbody></table>';
        table.innerHTML = html;
    });
}

// Carry forward
function carryForward(month, year, nextMonth, nextYear) {
    if (!confirm(`Add remaining to ${monthNames[nextMonth]} ${nextYear}?`)) return;

    fetch('<?= BASE_URL ?>carryforward.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `student_id=${currentStudentId}&month=${month}&year=${year}&next_month=${nextMonth}&next_year=${nextYear}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(`✅ Rs. ${data.amount} added to ${monthNames[data.next_month]} ${data.next_year}!`);
            loadFeeHistory();
        } else {
            alert('❌ ' + data.message);
        }
    });
}

// Undo carry forward
function undoCarryForward(month, year) {
    if (!confirm('Undo carry forward?')) return;

    fetch('<?= BASE_URL ?>carryforward.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `student_id=${currentStudentId}&month=${month}&year=${year}&action=undo`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✅ Carry forward undone!');
            loadFeeHistory();
        } else {
            alert('❌ ' + data.message);
        }
    });
}

// Mark unpaid
function markUnpaid(month, year) {
    if (!confirm('Mark this month as unpaid?')) return;

    fetch('<?= BASE_URL ?>updatefeestatus.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `month=${month}&year=${year}&status[${currentStudentId}]=unpaid`
    })
    .then(r => r.text())
    .then(() => {
        loadFeeHistory();
    });
}

// Toggle month
function toggleMonth(month, label) {
    let idx = selectedMonths.indexOf(month);
    if (idx === -1) {
        selectedMonths.push(month);
        label.style.background = '#1e293b';
        label.style.border     = '2px solid #1e293b';
        label.style.color      = 'white';
    } else {
        selectedMonths.splice(idx, 1);
        label.style.background = '#f8fafc';
        label.style.border     = '0.5px solid #e2e8f0';
        label.style.color      = '#64748b';
    }
    updateMonthUI();
    updateTotalDue();
}

// Update month UI
function updateMonthUI() {
    let text = selectedMonths.length > 0
        ? selectedMonths.sort((a,b)=>a-b).map(m => monthShort[m]).join(', ')
        : 'None';
    document.getElementById('selectedMonthsText').innerText = text;
}

// Update total due
function updateTotalDue() {
    let year = document.getElementById('popupYear').value;
    if (selectedMonths.length === 0 || !year || !currentStudentId) {
        document.getElementById('totalDue').innerText = 'Rs. 0';
        document.getElementById('amountPaying').value = '';
        return;
    }

    // Server se actual due fetch karo
    let body = `student_id=${currentStudentId}&year=${year}`;
    selectedMonths.forEach(m => { body += `&months[]=${m}`; });

    fetch('<?= BASE_URL ?>getcalcdue.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('totalDue').innerText = 'Rs. ' + data.total_due;
        document.getElementById('amountPaying').value = data.total_due;
        updateRemaining();

        // Show breakdown agar carried amount hai
        if (data.has_carried) {
            document.getElementById('dueBreakdown').style.display = 'block';
            document.getElementById('dueBreakdown').innerHTML = data.breakdown;
        } else {
            document.getElementById('dueBreakdown').style.display = 'none';
        }
    });
}

// Update remaining
function updateRemaining(totalOverride) {
    let totalText = document.getElementById('totalDue').innerText.replace('Rs. ', '');
    let total     = totalOverride !== undefined ? totalOverride : (parseFloat(totalText) || 0);
    let paying    = parseFloat(document.getElementById('amountPaying').value) || 0;
    let remaining = total - paying;
    let el        = document.getElementById('remainingAmount');
    el.innerText   = 'Rs. ' + remaining.toFixed(0);
    el.style.color = remaining > 0 ? '#dc2626' : '#16a34a';
}

// Select payment method
function selectMethod(method) {
    currentMethod = method;
    ['cash', 'easypaisa', 'card'].forEach(m => {
        let lbl = document.getElementById(m + 'Lbl');
        let det = document.getElementById(m + 'Details');
        if (lbl) {
            if (m === method) {
                lbl.style.background = '#1e293b';
                lbl.style.border     = '2px solid #1e293b';
                lbl.style.color      = 'white';
            } else {
                lbl.style.background = '#f8fafc';
                lbl.style.border     = '1px solid #e2e8f0';
                lbl.style.color      = '#64748b';
            }
        }
        if (det) det.style.display = (m === method && m !== 'cash') ? 'block' : 'none';
    });
}

// Save payment
function savePayment() {
    if (!currentStudentId)        { alert('No student selected!'); return; }
    if (selectedMonths.length === 0) { alert('Please select at least one month!'); return; }

    let amount = parseFloat(document.getElementById('amountPaying').value) || 0;
    if (amount <= 0) { alert('Please enter amount!'); return; }

    let year = document.getElementById('popupYear').value;
    if (!year) { alert('Please select year!'); return; }

    let btn       = document.getElementById('savePayBtn');
    btn.disabled  = true;
    btn.innerText = '⏳ Saving...';

    let body = `student_id=${currentStudentId}&year=${year}&amount_paid=${amount}&payment_method=${currentMethod}&notes=${encodeURIComponent(document.getElementById('paymentNotes').value)}`;
    selectedMonths.forEach(m => { body += `&months[]=${m}`; });

    if (currentMethod === 'easypaisa') {
        body += `&transaction_id=${encodeURIComponent(document.getElementById('ep_transaction').value)}`;
        body += `&sender_number=${encodeURIComponent(document.getElementById('ep_sender').value)}`;
    } else if (currentMethod === 'card') {
        body += `&transaction_id=${encodeURIComponent(document.getElementById('card_transaction').value)}`;
        body += `&card_type=${document.getElementById('card_type_select').value}`;
    }

    fetch('<?= BASE_URL ?>savepayment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closePopup();
            // Receipt naye tab mein kholo
            window.open(
                '<?= BASE_URL ?>printreceipt.php?payment_id=' + data.payment_id,
                '_blank',
                'width=750,height=900,scrollbars=yes'
            );
        } else {
            let msg          = document.getElementById('saveMsg');
            msg.style.display    = 'block';
            msg.style.background = '#fee2e2';
            msg.style.color      = '#991b1b';
            msg.innerText        = '❌ ' + data.message;
        }
        btn.disabled  = false;
        btn.innerText = '💾 Save & Generate Receipt';
    })
    .catch(() => {
        btn.disabled  = false;
        btn.innerText = '💾 Save & Generate Receipt';
        alert('Something went wrong!');
    });
}

// Close popup
function closePopup() {
    document.getElementById('searchOverlay').style.display = 'none';
    document.getElementById('searchDAS').value             = '';
}

document.getElementById('searchOverlay').addEventListener('click', function(e) {
    if (e.target === this) closePopup();
});

window.addEventListener("pageshow", function(e) {
    if (e.persisted) window.location.reload();
});
</script>

</body>
</html>