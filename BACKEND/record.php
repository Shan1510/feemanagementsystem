<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/any_auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Status</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f1f5f9; padding: 30px; }

        h2 { color: #0f172a; margin-bottom: 25px; font-size: 1.8rem; }

        /* FILTER BOX */
        .filter-box {
            background: white;
            padding: 20px 25px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 130px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group select {
            padding: 10px 12px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            font-size: 14px;
            color: #334155;
            cursor: pointer;
            outline: none;
            transition: border 0.2s;
        }

        .filter-group select:focus {
            border-color: #6366f1;
        }

        .search-btn {
            padding: 10px 25px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            height: 42px;
            transition: 0.2s;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99,102,241,0.4);
        }

        .search-btn:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* RESULTS */
        #results { display: none; }

        .results-header {
            background: white;
            padding: 15px 20px;
            border-radius: 14px 14px 0 0;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .results-header h3 { color: #0f172a; font-size: 1.1rem; }
        .results-header span { color: #64748b; font-size: 14px; }

        /* TABLE */
        .table-wrap {
            background: white;
            border-radius: 0 0 14px 14px;
            overflow: auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }

        table { width: 100%; border-collapse: collapse; }

        thead tr { background: #1e293b; }
        thead th {
            padding: 13px 15px;
            color: white;
            font-size: 13px;
            text-align: left;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        tbody tr:hover { background: #f8fafc; }
        tbody td { padding: 12px 15px; font-size: 14px; color: #334155; }

        /* STATUS TOGGLE */
        .status-toggle {
            display: flex;
            gap: 8px;
        }

        .status-toggle label {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            border: 2px solid transparent;
            transition: all 0.2s;
        }

        .status-toggle input[type="radio"] { display: none; }

        .paid-label { color: #16a34a; border-color: #dcfce7; background: #f0fdf4; }
        .unpaid-label { color: #dc2626; border-color: #fee2e2; background: #fff5f5; }

        .status-toggle input:checked + .paid-label,
        input[value="paid"]:checked ~ .paid-label {
            background: #16a34a;
            color: white;
            border-color: #16a34a;
        }

        /* Paid radio checked */
        .paid-radio:checked + label {
            background: #16a34a !important;
            color: white !important;
            border-color: #16a34a !important;
        }

        /* Unpaid radio checked */
        .unpaid-radio:checked + label {
            background: #dc2626 !important;
            color: white !important;
            border-color: #dc2626 !important;
        }

        /* SAVE BUTTON */
        .save-wrap {
            background: white;
            padding: 15px 20px;
            border-radius: 0 0 14px 14px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .save-btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99,102,241,0.4);
        }

        #saveMsg {
            display: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .msg-success { background: #d1fae5; color: #065f46; }
        .msg-error   { background: #fee2e2; color: #991b1b; }

        .loading-row td {
            text-align: center;
            padding: 40px;
            color: #64748b;
            font-size: 16px;
        }
    </style>
</head>
<body>

<h2>📅 Monthly Fee Status</h2>

<!-- FILTER BOX -->
<div class="filter-box">
    <div class="filter-group">
        <label>Class</label>
        <select id="f-class">
            <option value="">Select Class</option>
            <?php
            $cls = mysqli_query($conn, "SELECT DISTINCT class_name FROM class ORDER BY class_name");
            while($c = mysqli_fetch_assoc($cls)) {
                echo "<option value='{$c['class_name']}'>{$c['class_name']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="filter-group">
        <label>Section</label>
        <select id="f-sec" disabled>
            <option value="">Select Section</option>
        </select>
    </div>

    <div class="filter-group">
        <label>Month</label>
        <select id="f-month">
            <option value="">Select Month</option>
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
    </div>

    <div class="filter-group">
        <label>Year</label>
        <select id="f-year">
            <option value="">Select Year</option>
            <?php
            for($y = YEAR_START; $y <= YEAR_NOW; $y++) {
                echo "<option value='$y'>$y</option>";
            }
            ?>
        </select>
    </div>

    <button class="search-btn" id="searchBtn" disabled onclick="loadStudents()">
        🔍 Search
    </button>
</div>

<!-- RESULTS -->
<div id="results">
    <div class="results-header">
        <h3 id="resultsTitle">Students</h3>
        <span id="resultsCount"></span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>DAS</th>
                    <th>Student Name</th>
                    <th>Father Name</th>
                    <th>Contact</th>
                    <th>Total Fee</th>
                    <th>Fee Status</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <tr class="loading-row"><td colspan="7">⏳ Loading...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="save-wrap">
        <button class="save-btn" onclick="saveStatus()">💾 Save Fee Status</button>
        <div id="saveMsg"></div>
    </div>
</div>

<script>
let currentMonth, currentYear, currentClassId;

// Class change → sections load
document.getElementById('f-class').addEventListener('change', function() {
    let cls = this.value;
    let secSel = document.getElementById('f-sec');
    secSel.innerHTML = '<option value="">Select Section</option>';
    secSel.disabled = true;
    checkFilters();

    if (!cls) return;

    fetch('<?= BASE_URL ?>fetchrecord.php?class_name=' + encodeURIComponent(cls))
    .then(r => r.json())
    .then(data => {
        data.forEach(s => {
            secSel.innerHTML += `<option value="${s.class_sec}">${s.class_sec}</option>`;
        });
        secSel.disabled = false;
    });
});

// Check all filters → enable button
['f-class','f-sec','f-month','f-year'].forEach(id => {
    document.getElementById(id).addEventListener('change', checkFilters);
});

function checkFilters() {
    let c = document.getElementById('f-class').value;
    let s = document.getElementById('f-sec').value;
    let m = document.getElementById('f-month').value;
    let y = document.getElementById('f-year').value;
    document.getElementById('searchBtn').disabled = !(c && s && m && y);
}

// Load students
function loadStudents() {
    let cls  = document.getElementById('f-class').value;
    let sec  = document.getElementById('f-sec').value;
    let month = document.getElementById('f-month').value;
    let year  = document.getElementById('f-year').value;

    currentMonth   = month;
    currentYear    = year;

    let monthNames = ['','January','February','March','April','May','June',
                      'July','August','September','October','November','December'];

    document.getElementById('results').style.display = 'block';
    document.getElementById('resultsTitle').innerText = `${cls} - Section ${sec} | ${monthNames[month]} ${year}`;
    document.getElementById('studentTableBody').innerHTML = '<tr class="loading-row"><td colspan="7">⏳ Loading...</td></tr>';
    document.getElementById('saveMsg').style.display = 'none';

    fetch('<?= BASE_URL ?>fetchrecord.php?class_name=' + encodeURIComponent(cls) + '&sec=' + encodeURIComponent(sec) + '&month=' + month + '&year=' + year + '&students=1')
    .then(r => r.json())
    .then(data => {
        currentClassId = data.class_id;
        let tbody = document.getElementById('studentTableBody');

        if (data.students.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#64748b;">No students found</td></tr>';
            document.getElementById('resultsCount').innerText = '0 students';
            return;
        }

        document.getElementById('resultsCount').innerText = data.students.length + ' students';
        tbody.innerHTML = '';

        data.students.forEach((s, i) => {
            let paid   = s.status === 'paid'   ? 'checked' : '';
            let unpaid = s.status === 'unpaid' ? 'checked' : '';

            tbody.innerHTML += `
            <tr>
                <td>${i+1}</td>
                <td>${s.DAS}</td>
                <td>${s.student_name}</td>
                <td>${s.father_name}</td>
                <td>${s.contact_number}</td>
                <td>Rs. ${s.T_Fee}</td>
                <td>
                    <div class="status-toggle">
                        <input type="radio" class="paid-radio" 
                               name="status[${s.id}]" 
                               id="paid_${s.id}" 
                               value="paid" ${paid}>
                        <label for="paid_${s.id}" class="paid-label">✅ Paid</label>

                        <input type="radio" class="unpaid-radio" 
                               name="status[${s.id}]" 
                               id="unpaid_${s.id}" 
                               value="unpaid" ${unpaid}>
                        <label for="unpaid_${s.id}" class="unpaid-label">❌ Unpaid</label>
                    </div>
                </td>
            </tr>`;
        });
    });
}

// Save fee status
function saveStatus() {
    let radios = document.querySelectorAll('input[type="radio"]:checked');
    let statuses = {};

    radios.forEach(r => {
        let match = r.name.match(/status\[(\d+)\]/);
        if (match) statuses[match[1]] = r.value;
    });

    if (Object.keys(statuses).length === 0) {
        showMsg('No data to save!', false);
        return;
    }

    let body = `month=${currentMonth}&year=${currentYear}&class_id=${currentClassId}`;
    for (let id in statuses) {
        body += `&status[${id}]=${statuses[id]}`;
    }

    document.querySelector('.save-btn').disabled = true;
    document.querySelector('.save-btn').innerText = 'Saving...';

    fetch('<?= BASE_URL ?>updatefeestatus.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(r => r.text())
    .then(() => {
        showMsg('✅ Fee status saved successfully!', true);
        document.querySelector('.save-btn').disabled = false;
        document.querySelector('.save-btn').innerText = '💾 Save Fee Status';
    })
    .catch(() => {
        showMsg('❌ Something went wrong!', false);
        document.querySelector('.save-btn').disabled = false;
        document.querySelector('.save-btn').innerText = '💾 Save Fee Status';
    });
}

function showMsg(text, success) {
    let msg = document.getElementById('saveMsg');
    msg.style.display = 'block';
    msg.className = success ? 'msg-success' : 'msg-error';
    msg.innerText = text;
    setTimeout(() => { msg.style.display = 'none'; }, 3000);
}
</script>
</body>
</html>