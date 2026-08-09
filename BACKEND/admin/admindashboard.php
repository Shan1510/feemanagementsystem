<?php
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

include __DIR__ . '/../Master/conection.php';
include __DIR__ . '/../Master/admin_auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Fee Management System</title>
    <link href="admin.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-layout">
    <?php include __DIR__ . '/adminsidebar.php'; ?>

    <main class="main-content">
        <div class="page-container">
            <div class="dash-hero">
                <div>
                    <span class="hero-eyebrow">📅 <?= date('l, F j, Y') ?></span>
                    <h1>Welcome back, Admin 👋</h1>
                    <p>Here's what's happening with student fees today.</p>
                </div>
                <div class="hero-actions">
                    <a href="<?= FRONTEND_URL ?>addstudents.php" class="btn btn-primary">＋ Add Student</a>
                    <a href="<?= BASE_URL ?>monthlyview.php" class="btn">📅 Manage Fees</a>
                    <a href="#" onclick="openExcelReportModal(); return false;" class="btn">📥 Excel Report</a>
                </div>
            </div>

            <div class="stats-grid">
                <a href="<?= BASE_URL ?>buttons/totalbutton.php" class="stat-card-link">
                    <div class="stat-card total">
                        <div class="stat-icon total">🎓</div>
                        <h3>Total Students</h3>
                        <div class="stat-value">
                            <?php include __DIR__ . '/../buttons/totalstudents.php'; ?>
                        </div>
                        <span class="stat-trend">📚 View all students →</span>
                    </div>
                </a>
                <a href="<?= BASE_URL ?>buttons/paidbuttonfetch.php" class="stat-card-link">
                    <div class="stat-card paid">
                        <div class="stat-icon paid">✅</div>
                        <h3>Paid Fees</h3>
                        <div class="stat-value">
                            <?php include __DIR__ . '/../buttons/paidbutton.php'; ?>
                        </div>
                        <span class="stat-trend">💸 Fully cleared</span>
                    </div>
                </a>
                <a href="<?= BASE_URL ?>buttons/unpaidfetch.php" class="stat-card-link">
                    <div class="stat-card unpaid">
                        <div class="stat-icon pending">⏳</div>
                        <h3>Pending</h3>
                        <div class="stat-value">
                            <?php include __DIR__ . '/../buttons/unpaid.php'; ?>
                        </div>
                        <span class="stat-trend">⚡ Still outstanding</span>
                    </div>
                </a>
            </div>

            <div class="card">
                <h2>🔍 Search Student</h2>
                <p style="color:var(--muted); font-size:0.85rem; margin:-8px 0 16px;">
                    Search by DAS number to record a payment or view history.
                </p>
                <form onsubmit="searchStudent(event)" class="form-row" style="align-items:flex-end;">
                    <div class="form-field" style="flex:1;margin:0;">
                        <label for="searchDAS">DAS Number</label>
                        <input type="search" id="searchDAS" class="form-control"
                               placeholder="Enter DAS number" required>
                    </div>
                    <div class="form-field" style="margin:0;">
                        <button type="submit" class="btn btn-primary">Search Student</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<!-- PAYMENT POPUP -->
<div id="searchOverlay" class="popup-overlay">
<div class="popup">

    <!-- ===== HEADER ===== -->
    <div class="popup-header">
        <div class="ph-top">
            <div class="ph-title">
                <small>Fee Collection</small>
                <h3>Payment Entry</h3>
            </div>
            <div class="ph-right">
                <span class="ph-das-badge" id="popupDAS">DAS —</span>
                <button onclick="closePopup()" class="popup-close" aria-label="Close">✕</button>
            </div>
        </div>

        <div class="student-strip">
            <div class="s-avatar" id="popupAvatar">🧑‍🎓</div>
            <div class="s-info">
                <strong id="popupName">Selecting student…</strong>
                <span id="popupClass">—</span>
            </div>
            <div class="s-meta">
                <div class="mini">
                    <small>Father</small>
                    <strong id="popupFather">—</strong>
                </div>
                <div class="mini">
                    <small>Monthly Fee</small>
                    <strong id="popupFee">—</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== BODY ===== -->
    <div class="popup-body">

        <!-- STEP 1: Year + history -->
        <div class="pay-step">
            <div class="step-head">
                <span class="step-num">1</span>
                <div>
                    <strong>Fee Year</strong>
                    <small>Pick the academic year to charge</small>
                </div>
            </div>
            <select id="popupYear" class="form-control" onchange="loadFeeHistory(); updateTotalDue()">
                <option value="">Select Year</option>
                <?php
                for($y = YEAR_START; $y <= YEAR_NOW; $y++) {
                    echo "<option value='$y'" . ($y == YEAR_NOW ? ' selected' : '') . ">$y</option>";
                }
                ?>
            </select>

            <div id="feeHistorySection" class="hist-wrap" style="display:none;">
                <div class="step-head" style="margin-top:4px;">
                    <span class="step-num" style="background:#0ea5e9;">↺</span>
                    <div>
                        <strong>Fee History — <span id="historyYear" style="color:#0ea5e9;"></span></strong>
                        <small>Already recorded payments for this year</small>
                    </div>
                </div>
                <div id="feeHistoryTable"></div>
            </div>
        </div>

        <!-- STEP 2: Months -->
        <div class="pay-step">
            <div class="step-head">
                <span class="step-num">2</span>
                <div>
                    <strong>Select Months</strong>
                    <small>Tap the months you are charging for</small>
                </div>
            </div>
            <div class="month-grid" id="monthGrid">
                <?php
                $monthList = ['Jan'=>1,'Feb'=>2,'Mar'=>3,'Apr'=>4,'May'=>5,'Jun'=>6,
                              'Jul'=>7,'Aug'=>8,'Sep'=>9,'Oct'=>10,'Nov'=>11,'Dec'=>12];
                foreach($monthList as $name => $num): ?>
                <label id="mlabel_<?= $num ?>" onclick="toggleMonth(<?= $num ?>, this)" class="month-chip">
                    <?= $name ?>
                </label>
                <?php endforeach; ?>
            </div>
            <div class="month-summary">
                <span class="sel-text">Selected: <b id="selectedMonthsText">None</b></span>
                <span class="count-pill" id="monthCountPill">0 / 12</span>
            </div>
        </div>

        <!-- STEP 3: Amount -->
        <div class="pay-step">
            <div class="step-head">
                <span class="step-num">3</span>
                <div>
                    <strong>Amount</strong>
                    <small>The total due updates automatically</small>
                </div>
            </div>
            <div class="amount-cards">
                <div class="amt-card">
                    <small>Total Due</small>
                    <strong id="totalDue">Rs. 0</strong>
                </div>
                <div class="amt-card">
                    <small>Amount Paying</small>
                    <input type="number" id="amountPaying" placeholder="Enter amount"
                           oninput="updateRemaining()" min="0">
                </div>
                <div class="amt-card">
                    <small>Remaining</small>
                    <strong id="remainingAmount" class="positive">Rs. 0</strong>
                </div>
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>

        <!-- STEP 4: Method -->
        <div class="pay-step">
            <div class="step-head">
                <span class="step-num">4</span>
                <div>
                    <strong>Payment Method</strong>
                    <small>How is the payment being made?</small>
                </div>
            </div>
            <div class="method-row">
                <label id="cashLbl" class="method-btn selected" onclick="selectMethod('cash')">💵 Cash</label>
                <label id="easypaisaLbl" class="method-btn" onclick="selectMethod('easypaisa')">📱 EasyPaisa</label>
                <label id="cardLbl" class="method-btn" onclick="selectMethod('card')">💳 Card</label>
            </div>

            <div id="easypaisaDetails" class="method-detail">
                <input type="text" id="ep_transaction" class="form-control" placeholder="Transaction ID">
                <input type="text" id="ep_sender" class="form-control" placeholder="Sender Number (03XXXXXXXXX)">
            </div>

            <div id="cardDetails" class="method-detail">
                <input type="text" id="card_transaction" class="form-control" placeholder="Transaction ID">
                <select id="card_type_select" class="form-control">
                    <option value="">Select Card Type</option>
                    <option value="visa">Visa</option>
                    <option value="mastercard">Mastercard</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>

        <!-- STEP 5: Notes -->
        <div class="pay-step">
            <div class="step-head">
                <span class="step-num">5</span>
                <div>
                    <strong>Notes</strong>
                    <small>Optional — any additional information</small>
                </div>
            </div>
            <textarea id="paymentNotes" class="form-control"
                      placeholder="Any additional notes..."></textarea>
        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    <div class="popup-footer">
        <button onclick="savePayment()" id="savePayBtn" class="btn btn-primary">💾 &nbsp;Save &amp; Generate Receipt</button>
        <button onclick="closePopup()" class="btn btn-outline">Cancel</button>
    </div>

    <div id="saveMsg" style="display:none;"></div>
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

        document.getElementById('popupDAS').innerText = 'DAS — ' + data.DAS;
        document.getElementById('popupName').innerText   = data.student_name;
        document.getElementById('popupFather').innerText = (data.father_name || '—');
        document.getElementById('popupClass').innerText  = (data.class_name ?? 'N/A') + ' — ' + (data.class_sec ?? '');
        document.getElementById('popupFee').innerText    = 'Rs. ' + data.T_Fee + '/mo';

        // Initials avatar
        let parts = (data.student_name || '?').trim().split(/\s+/);
        let initials = ((parts[0] || '?')[0] + (parts[1] ? parts[1][0] : '')).toUpperCase();
        document.getElementById('popupAvatar').innerText = initials;

        // Reset everything
        selectedMonths = [];
        document.querySelectorAll('.month-chip').forEach(lbl => {
            lbl.classList.remove('selected');
        });
        updateMonthUI();
        document.getElementById('amountPaying').value        = '';
        document.getElementById('totalDue').innerText        = 'Rs. 0';
        document.getElementById('remainingAmount').innerText = 'Rs. 0';
        document.getElementById('remainingAmount').className = 'positive';
        document.getElementById('progressFill').style.width  = '0%';
        document.getElementById('paymentNotes').value        = '';
        document.getElementById('saveMsg').style.display     = 'none';
        document.getElementById('saveMsg').innerText         = '';
        document.getElementById('feeHistorySection').style.display = 'none';
        selectMethod('cash');

        document.getElementById('popupYear').value = '<?= YEAR_NOW ?>';
        loadFeeHistory();

        document.getElementById('searchOverlay').classList.add('open');
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

        let html = `<table class="data-table"><thead>
            <tr>
                <th>Month</th>
                <th style="text-align:center;">Status</th>
                <th style="text-align:right;">Paid</th>
                <th style="text-align:right;">Remaining</th>
                <th style="text-align:center;">Action</th>
            </tr>
        </thead><tbody>`;

        data.forEach(row => {
            let statusColor = row.status === 'paid'    ? '#d1fae5' :
                              row.status === 'partial'  ? '#fef9c3' : '#fee2e2';
            let textColor   = row.status === 'paid'    ? '#065f46' :
                              row.status === 'partial'  ? '#854d0e' : '#991b1b';
            let statusEmoji = row.status === 'paid'    ? '✅' :
                              row.status === 'partial'  ? '⚠️' : '❌';

            let carriedNote = '';
            if (row.carried_amount > 0) {
                carriedNote = `<br><span style="color:#854d0e; font-size:10px;">
                    📌 Includes Rs.${parseFloat(row.carried_amount).toFixed(0)} from prev month
                </span>`;
            }

            let actionBtns = '';

            if (row.remaining > 0 && row.carry_forward == 0) {
                let nm = row.fee_month == 12 ? 1  : parseInt(row.fee_month) + 1;
                let ny = row.fee_month == 12 ? parseInt(row.fee_year) + 1 : row.fee_year;
                actionBtns += `
                    <button onclick="carryForward(${row.fee_month}, ${row.fee_year}, ${nm}, ${ny})"
                        class="link-btn edit" style="display:block; width:100%; margin-bottom:3px;">
                        ➕ Add to ${monthShort[nm]}
                    </button>`;
            }

            if (row.carry_forward == 1) {
                actionBtns += `
                    <button onclick="undoCarryForward(${row.fee_month}, ${row.fee_year})"
                        class="link-btn del" style="display:block; width:100%; margin-bottom:3px;">
                        ↩️ Undo Forward
                    </button>`;
            }

            if (row.status === 'paid' || row.status === 'partial') {
                actionBtns += `
                    <button onclick="markUnpaid(${row.fee_month}, ${row.fee_year})"
                        class="link-btn del" style="display:block; width:100%;">
                        ❌ Mark Unpaid
                    </button>`;
            }

            if (row.carry_forward == 1 && row.remaining == 0) {
                actionBtns = `<span style="font-size:10px; color:#16a34a; font-weight:600;">✅ Forwarded</span>`;
            }

            html += `<tr>
                <td class="strong">
                    ${monthNames[row.fee_month]}
                    ${carriedNote}
                </td>
                <td style="text-align:center;">
                    <span style="font-size:10px; padding:2px 7px; border-radius:20px;
                        background:${statusColor}; color:${textColor}; font-weight:600;">
                        ${statusEmoji} ${row.status.charAt(0).toUpperCase() + row.status.slice(1)}
                    </span>
                </td>
                <td style="text-align:right; color:#16a34a; font-weight:600;">
                    Rs. ${parseFloat(row.amount_paid).toFixed(0)}
                </td>
                <td style="text-align:right; font-weight:600;
                    color:${row.remaining > 0 ? '#dc2626' : '#16a34a'};">
                    ${row.remaining > 0 ? 'Rs. ' + parseFloat(row.remaining).toFixed(0) : '-'}
                </td>
                <td style="text-align:center; min-width:100px;">
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
        label.classList.add('selected');
    } else {
        selectedMonths.splice(idx, 1);
        label.classList.remove('selected');
    }
    updateMonthUI();
    updateTotalDue();
}

// Update month UI
function updateMonthUI() {
    let sorted = selectedMonths.sort((a,b)=>a-b);
    let text = sorted.length > 0
        ? sorted.map(m => monthShort[m]).join(', ')
        : 'None';
    document.getElementById('selectedMonthsText').innerText = text;
    document.getElementById('monthCountPill').innerText = sorted.length + ' / 12';
}

// Update total due
function updateTotalDue() {
    let year = document.getElementById('popupYear').value;
    if (selectedMonths.length === 0 || !year || !currentStudentId) {
        document.getElementById('totalDue').innerText = 'Rs. 0';
        document.getElementById('amountPaying').value = '';
        return;
    }

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

    // remaining card colour
    el.className = remaining > 0 ? 'negative' : 'positive';

    // progress bar fill
    let pct = total > 0 ? Math.min(100, Math.max(0, (paying / total) * 100)) : 0;
    document.getElementById('progressFill').style.width = pct + '%';
}

// Select payment method
function selectMethod(method) {
    currentMethod = method;
    ['cash', 'easypaisa', 'card'].forEach(m => {
        let lbl = document.getElementById(m + 'Lbl');
        let det = document.getElementById(m + 'Details');
        if (lbl) {
            if (m === method) lbl.classList.add('selected');
            else lbl.classList.remove('selected');
        }
        if (det) {
            if (m === method && m !== 'cash') det.classList.add('open');
            else det.classList.remove('open');
        }
    });
}

// Save payment
function savePayment() {
    if (!currentStudentId)          { alert('No student selected!'); return; }
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
    document.getElementById('searchOverlay').classList.remove('open');
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