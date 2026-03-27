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

<!-- POPUP OVERLAY -->
<div id="searchOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:14px; width:520px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.3);">

        <!-- Header -->
        <div style="background:#1e293b; padding:14px 18px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:10;">
            <div>
                <p style="color:#f1f5f9; font-size:15px; font-weight:600; margin:0;">Student Details</p>
                <p style="color:#94a3b8; font-size:12px; margin:4px 0 0 0;" id="popupDAS"></p>
            </div>
            <button onclick="closePopup()" style="background:#e74c3c; color:white; border:none; border-radius:6px; padding:5px 12px; cursor:pointer; font-size:16px;">✕</button>
        </div>

        <!-- Student Info -->
        <div style="padding:16px 18px; border-bottom:1px solid #f1f5f9;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div style="background:#f8fafc; padding:10px 12px; border-radius:8px;">
                    <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase; letter-spacing:0.5px;">Student</p>
                    <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;" id="popupName"></p>
                </div>
                <div style="background:#f8fafc; padding:10px 12px; border-radius:8px;">
                    <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase; letter-spacing:0.5px;">Father</p>
                    <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;" id="popupFather"></p>
                </div>
                <div style="background:#f8fafc; padding:10px 12px; border-radius:8px;">
                    <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase; letter-spacing:0.5px;">Contact</p>
                    <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;" id="popupContact"></p>
                </div>
                <div style="background:#f8fafc; padding:10px 12px; border-radius:8px;">
                    <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase; letter-spacing:0.5px;">Total Fee</p>
                    <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;" id="popupFee"></p>
                </div>
                <div style="background:#f8fafc; padding:10px 12px; border-radius:8px;">
                    <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase; letter-spacing:0.5px;">Class</p>
                    <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;" id="popupClass"></p>
                </div>
                <div style="background:#f8fafc; padding:10px 12px; border-radius:8px;">
                    <p style="font-size:11px; color:#64748b; margin:0 0 3px 0; text-transform:uppercase; letter-spacing:0.5px;">Section</p>
                    <p style="font-size:14px; font-weight:600; color:#0f172a; margin:0;" id="popupSec"></p>
                </div>
            </div>
        </div>

        <!-- Month Year Select -->
        <div style="padding:14px 18px; border-bottom:1px solid #f1f5f9;">
            <p style="font-size:13px; font-weight:600; color:#64748b; margin:0 0 10px 0;">Select month & year</p>
            <div style="display:flex; gap:8px;">
                <select id="popupMonth" style="flex:1; padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none;" onchange="loadFeeStatus()">
                    <option value="">Month</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
                <select id="popupYear" style="flex:1; padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none;" onchange="loadFeeStatus()">
                    <option value="">Year</option>
                    <?php
                    $cy = date('Y');
                    for($y = $cy; $y >= $cy - 5; $y--) {
                        echo "<option value='$y'>$y</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Fee Status -->
        <div id="feeStatusSection" style="display:none; padding:14px 18px; border-bottom:1px solid #f1f5f9;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <p style="font-size:13px; font-weight:600; color:#64748b; margin:0;" id="feeStatusLabel">Fee status</p>
                <span id="feeBadge" style="font-size:12px; font-weight:600; padding:3px 12px; border-radius:20px;"></span>
            </div>
            <div style="display:flex; gap:10px;">
                <label id="paidLabel" style="flex:1; display:flex; align-items:center; justify-content:center; gap:6px; padding:12px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.2s;">
                    <input type="radio" name="feeStatus" id="paidRadio" value="paid" onchange="updateStatusUI()"> Paid
                </label>
                <label id="unpaidLabel" style="flex:1; display:flex; align-items:center; justify-content:center; gap:6px; padding:12px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.2s;">
                    <input type="radio" name="feeStatus" id="unpaidRadio" value="unpaid" onchange="updateStatusUI()"> Unpaid
                </label>
            </div>
        </div>

        <!-- Payment Method -->
        <div id="paymentSection" style="display:none; padding:14px 18px; border-bottom:1px solid #f1f5f9;">
            <p style="font-size:13px; font-weight:600; color:#64748b; margin:0 0 10px 0;">Payment Method</p>
            <div style="display:flex; gap:8px; margin-bottom:12px;">
                <label id="cashLbl" onclick="selectMethod('cash')"
                    style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; padding:12px 8px; border-radius:8px; border:2px solid #1e293b; background:#1e293b; color:white; cursor:pointer; font-size:13px; font-weight:500;">
                    💵 Cash
                </label>
                <label id="easypaisaLbl" onclick="selectMethod('easypaisa')"
                    style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; padding:12px 8px; border-radius:8px; border:1px solid #e2e8f0; background:#f8fafc; color:#64748b; cursor:pointer; font-size:13px;">
                    📱 EasyPaisa
                </label>
                <label id="cardLbl" onclick="selectMethod('card')"
                    style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; padding:12px 8px; border-radius:8px; border:1px solid #e2e8f0; background:#f8fafc; color:#64748b; cursor:pointer; font-size:13px;">
                    💳 Card
                </label>
            </div>

            <!-- EasyPaisa Details -->
            <div id="easypaisaDetails" style="display:none; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <input type="text" id="ep_transaction" placeholder="Transaction ID (e.g. EP-123456)"
                        style="padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; width:100%;">
                    <input type="text" id="ep_sender" placeholder="Sender Number (e.g. 03001234567)"
                        style="padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; width:100%;">
                    <input type="number" id="ep_amount" placeholder="Amount Paid"
                        style="padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; width:100%;">
                </div>
            </div>

            <!-- Card Details -->
            <div id="cardDetails" style="display:none; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <input type="text" id="card_transaction" placeholder="Transaction ID (e.g. TXN-987654)"
                        style="padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; width:100%;">
                    <select id="card_type_select"
                        style="padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; width:100%;">
                        <option value="">Select Card Type</option>
                        <option value="visa">Visa</option>
                        <option value="mastercard">Mastercard</option>
                        <option value="other">Other</option>
                    </select>
                    <input type="number" id="card_amount" placeholder="Amount Paid"
                        style="padding:9px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:13px; outline:none; width:100%;">
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div id="saveSection" style="display:none; padding:14px 18px; gap:8px; flex-direction:row;">
            <button onclick="saveFeeStatus()" id="saveBtn"
                style="flex:1; padding:11px; background:#1e293b; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                Save Status
            </button>
            <button onclick="closePopup()"
                style="padding:11px 18px; background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; border-radius:8px; font-size:14px; cursor:pointer;">
                Cancel
            </button>
        </div>

        <!-- Save Message -->
        <div id="saveMsg" style="display:none; margin:0 18px 14px; padding:10px 15px; border-radius:8px; font-size:14px; font-weight:600;"></div>

    </div>
</div>

    <script>
        window.addEventListener("pageshow", function(e) {
            if (e.persisted) { window.location.reload(); }
        });
    </script>

    <script>
    let currentStudentId = null;
    let currentMethod    = 'cash';
    const monthNames     = ['','January','February','March','April','May','June',
                            'July','August','September','October','November','December'];

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

            currentStudentId = data.id;

            document.getElementById('popupDAS').innerText     = 'DAS: ' + data.DAS;
            document.getElementById('popupName').innerText    = data.student_name;
            document.getElementById('popupFather').innerText  = data.father_name;
            document.getElementById('popupContact').innerText = data.contact_number;
            document.getElementById('popupFee').innerText     = 'Rs. ' + data.T_Fee;
            document.getElementById('popupClass').innerText   = data.class_name ?? 'N/A';
            document.getElementById('popupSec').innerText     = data.class_sec  ?? 'N/A';

            document.getElementById('popupMonth').value              = '';
            document.getElementById('popupYear').value               = '';
            document.getElementById('feeStatusSection').style.display = 'none';
            document.getElementById('saveSection').style.display      = 'none';
            document.getElementById('paymentSection').style.display   = 'none';
            document.getElementById('saveMsg').style.display          = 'none';

            document.getElementById('searchOverlay').style.display = 'flex';
        })
        .catch(() => alert('Something went wrong!'));
    }

    // Load fee status
    function loadFeeStatus() {
        let month = document.getElementById('popupMonth').value;
        let year  = document.getElementById('popupYear').value;
        if (!month || !year || !currentStudentId) return;

        document.getElementById('feeStatusLabel').innerText =
            'Fee status for ' + monthNames[month] + ' ' + year;

        fetch('<?= BASE_URL ?>getfeestatus.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `student_id=${currentStudentId}&month=${month}&year=${year}`
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'paid') {
                document.getElementById('paidRadio').checked = true;
            } else {
                document.getElementById('unpaidRadio').checked = true;
            }

            currentMethod = data.payment_method || 'cash';
            updateStatusUI();

            // Fill payment details
            if (data.payment_method === 'easypaisa') {
                document.getElementById('ep_transaction').value = data.transaction_id ?? '';
                document.getElementById('ep_sender').value      = data.sender_number  ?? '';
                document.getElementById('ep_amount').value      = data.amount_paid    ?? '';
            } else if (data.payment_method === 'card') {
                document.getElementById('card_transaction').value = data.transaction_id ?? '';
                document.getElementById('card_type_select').value = data.card_type      ?? '';
                document.getElementById('card_amount').value      = data.amount_paid    ?? '';
            }

            document.getElementById('feeStatusSection').style.display = 'block';
            document.getElementById('saveSection').style.display       = 'flex';
            document.getElementById('saveMsg').style.display           = 'none';
        });
    }

    // Update UI
    function updateStatusUI() {
        let isPaid     = document.getElementById('paidRadio').checked;
        let paidLbl    = document.getElementById('paidLabel');
        let unpaidLbl  = document.getElementById('unpaidLabel');
        let badge      = document.getElementById('feeBadge');
        let paySection = document.getElementById('paymentSection');

        if (isPaid) {
            paidLbl.style.background   = '#16a34a';
            paidLbl.style.border       = '2px solid #16a34a';
            paidLbl.style.color        = 'white';
            unpaidLbl.style.background = '#f8fafc';
            unpaidLbl.style.border     = '1px solid #e2e8f0';
            unpaidLbl.style.color      = '#64748b';
            badge.style.background     = '#16a34a';
            badge.style.color          = 'white';
            badge.innerText            = 'Paid';
            paySection.style.display   = 'block';
            selectMethod(currentMethod);
        } else {
            unpaidLbl.style.background = '#dc2626';
            unpaidLbl.style.border     = '2px solid #dc2626';
            unpaidLbl.style.color      = 'white';
            paidLbl.style.background   = '#f8fafc';
            paidLbl.style.border       = '1px solid #e2e8f0';
            paidLbl.style.color        = '#64748b';
            badge.style.background     = '#dc2626';
            badge.style.color          = 'white';
            badge.innerText            = 'Unpaid';
            paySection.style.display   = 'none';
        }
    }

    // Select payment method
    function selectMethod(method) {
        currentMethod = method;
        let methods   = ['cash', 'easypaisa', 'card'];
        methods.forEach(m => {
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
            if (det) {
                det.style.display = (m === method && m !== 'cash') ? 'block' : 'none';
            }
        });
    }

    // Save fee status
    function saveFeeStatus() {
        let month  = document.getElementById('popupMonth').value;
        let year   = document.getElementById('popupYear').value;
        let status = document.querySelector('input[name="feeStatus"]:checked').value;
        let btn    = document.getElementById('saveBtn');

        let paymentData = `payment_method=${currentMethod}`;

        if (currentMethod === 'easypaisa') {
            paymentData += `&transaction_id=${encodeURIComponent(document.getElementById('ep_transaction').value)}`;
            paymentData += `&sender_number=${encodeURIComponent(document.getElementById('ep_sender').value)}`;
            paymentData += `&amount_paid=${document.getElementById('ep_amount').value}`;
        } else if (currentMethod === 'card') {
            paymentData += `&transaction_id=${encodeURIComponent(document.getElementById('card_transaction').value)}`;
            paymentData += `&card_type=${document.getElementById('card_type_select').value}`;
            paymentData += `&amount_paid=${document.getElementById('card_amount').value}`;
        }

        btn.disabled  = true;
        btn.innerText = 'Saving...';

        fetch('<?= BASE_URL ?>updatefeestatus.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `month=${month}&year=${year}&status[${currentStudentId}]=${status}&${paymentData}`
        })
        .then(r => r.text())
        .then(() => {
            let msg          = document.getElementById('saveMsg');
            msg.style.display    = 'block';
            msg.style.background = '#d1fae5';
            msg.style.color      = '#065f46';
            msg.innerText        = '✅ Fee status saved successfully!';
            btn.disabled         = false;
            btn.innerText        = 'Save Status';
            setTimeout(() => { msg.style.display = 'none'; }, 3000);
        })
        .catch(() => {
            let msg          = document.getElementById('saveMsg');
            msg.style.display    = 'block';
            msg.style.background = '#fee2e2';
            msg.style.color      = '#991b1b';
            msg.innerText        = '❌ Something went wrong!';
            btn.disabled         = false;
            btn.innerText        = 'Save Status';
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
    </script>

</body>
</html>
```