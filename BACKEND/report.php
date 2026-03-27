<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

// All classes fetch karo
$allClasses = mysqli_query($conn, "SELECT * FROM class ORDER BY class_name, class_sec");
$classArr   = [];
while($c = mysqli_fetch_assoc($allClasses)) {
    $classArr[] = $c;
}

$monthNames = [
    1=>'January', 2=>'February', 3=>'March',    4=>'April',
    5=>'May',     6=>'June',     7=>'July',      8=>'August',
    9=>'September',10=>'October',11=>'November', 12=>'December'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Fee Report</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:#f1f5f9; padding:30px; }

        h2 { font-size:1.8rem; font-weight:700; color:#0f172a; margin-bottom:25px; }

        /* Filter Box */
        .filter-box {
            background: white;
            padding: 20px 25px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group select {
            padding: 9px 12px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            font-size: 14px;
            color: #334155;
            outline: none;
            min-width: 140px;
        }

        .filter-group select:focus { border-color: #6366f1; }

        .load-btn {
            padding: 9px 22px;
            background: #1e293b;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            height: 42px;
        }

        .download-btn {
            padding: 9px 22px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            height: 42px;
            margin-left: auto;
        }

        .download-btn:hover { background: #15803d; }
        .load-btn:hover     { background: #334155; }

        /* Class Pills */
        .classes-wrap {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .class-pill {
            padding: 8px 16px;
            border-radius: 20px;
            border: 0.5px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }

        .class-pill.active {
            background: #1e293b;
            color: white;
            border-color: #1e293b;
        }

        .class-pill:hover:not(.active) {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        /* Table */
        .table-wrap {
            background: white;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            display: none;
        }

        .table-header {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .badges { display: flex; gap: 8px; }

        .badge-paid {
            font-size: 12px;
            padding: 3px 12px;
            border-radius: 20px;
            background: #d1fae5;
            color: #065f46;
            font-weight: 600;
        }

        .badge-unpaid {
            font-size: 12px;
            padding: 3px 12px;
            border-radius: 20px;
            background: #fee2e2;
            color: #991b1b;
            font-weight: 600;
        }

        table { width: 100%; border-collapse: collapse; }

        thead tr { background: #1e293b; }
        thead th {
            padding: 12px 14px;
            color: white;
            font-size: 13px;
            text-align: left;
            font-weight: 600;
        }

        tbody tr { border-bottom: 0.5px solid #f1f5f9; transition: background 0.15s; }
        tbody tr:hover { background: #f8fafc; }
        tbody td { padding: 11px 14px; font-size: 13px; color: #334155; }

        .status-paid {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            background: #d1fae5;
            color: #065f46;
            font-size: 12px;
            font-weight: 600;
        }

        .status-unpaid {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 12px;
            font-weight: 600;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #64748b;
            font-size: 15px;
        }

        #noData {
            display: none;
            text-align: center;
            padding: 40px;
            color: #64748b;
            font-size: 15px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }
    </style>
</head>
<body>

<h2>📊 Monthly Fee Report</h2>

<!-- Filter Box -->
<div class="filter-box">
    <div class="filter-group">
        <label>Month</label>
        <select id="f-month">
            <option value="">Select Month</option>
            <?php foreach($monthNames as $num => $name): ?>
                <option value="<?= $num ?>"><?= $name ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label>Year</label>
        <select id="f-year">
            <option value="">Select Year</option>
            <?php
            $cy = date('Y');
            for($y = $cy; $y >= $cy - 5; $y--) {
                echo "<option value='$y'>$y</option>";
            }
            ?>
        </select>
    </div>
    <button class="load-btn" onclick="loadClasses()">Load Classes</button>
    <button class="download-btn" id="downloadBtn" style="display:none;" onclick="downloadExcel()">
        ⬇️ Download Excel
    </button>
</div>

<!-- Class Pills -->
<div class="classes-wrap" id="classesPills" style="display:none;"></div>

<!-- Table -->
<div class="table-wrap" id="tableWrap">
    <div class="table-header">
        <h3 id="tableTitle">-</h3>
        <div class="badges">
            <span class="badge-paid"  id="paidCount">Paid: 0</span>
            <span class="badge-unpaid" id="unpaidCount">Unpaid: 0</span>
        </div>
    </div>
    <div id="tableBody">
        <div class="loading">⏳ Loading...</div>
    </div>
</div>

<div id="noData">No students found for this class.</div>

<script>
let selectedMonth   = '';
let selectedYear    = '';
let selectedClassId = '';
let selectedClassName = '';
let allClassData    = <?= json_encode($classArr) ?>;

const monthNames = {
    1:'January', 2:'February', 3:'March',    4:'April',
    5:'May',     6:'June',     7:'July',      8:'August',
    9:'September',10:'October',11:'November', 12:'December'
};

// Load class pills
function loadClasses() {
    selectedMonth = document.getElementById('f-month').value;
    selectedYear  = document.getElementById('f-year').value;

    if (!selectedMonth || !selectedYear) {
        alert('Please select month and year!');
        return;
    }

    let pillsWrap = document.getElementById('classesPills');
    pillsWrap.innerHTML = '';
    pillsWrap.style.display = 'flex';

    allClassData.forEach((cls, i) => {
        let pill = document.createElement('button');
        pill.className   = 'class-pill' + (i === 0 ? ' active' : '');
        pill.innerText   = cls.class_name + ' - ' + cls.class_sec;
        pill.onclick     = function() {
            document.querySelectorAll('.class-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            loadClassData(cls.id, cls.class_name + ' - ' + cls.class_sec);
        };
        pillsWrap.appendChild(pill);
    });

    document.getElementById('downloadBtn').style.display = 'block';

    // Auto load first class
    if (allClassData.length > 0) {
        loadClassData(allClassData[0].id, allClassData[0].class_name + ' - ' + allClassData[0].class_sec);
    }
}

// Load class data
function loadClassData(classId, className) {
    selectedClassId   = classId;
    selectedClassName = className;

    document.getElementById('tableTitle').innerText = className + ' | ' + monthNames[selectedMonth] + ' ' + selectedYear;
    document.getElementById('tableWrap').style.display = 'block';
    document.getElementById('noData').style.display    = 'none';
    document.getElementById('tableBody').innerHTML     = '<div class="loading">⏳ Loading...</div>';

    fetch('<?= BASE_URL ?>reportdata.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `class_id=${classId}&month=${selectedMonth}&year=${selectedYear}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.length === 0) {
            document.getElementById('tableWrap').style.display = 'none';
            document.getElementById('noData').style.display    = 'block';
            return;
        }

        let paid   = data.filter(s => s.status === 'paid').length;
        let unpaid = data.filter(s => s.status === 'unpaid').length;

        document.getElementById('paidCount').innerText   = 'Paid: '   + paid;
        document.getElementById('unpaidCount').innerText = 'Unpaid: ' + unpaid;

        let html = `
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>DAS</th>
                    <th>Student Name</th>
                    <th>Father Name</th>
                    <th>Contact</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th>Method</th>
                </tr>
            </thead>
            <tbody>`;

        data.forEach((s, i) => {
            let statusBadge = s.status === 'paid'
                ? '<span class="status-paid">Paid</span>'
                : '<span class="status-unpaid">Unpaid</span>';

            let method = s.payment_method
                ? s.payment_method.charAt(0).toUpperCase() + s.payment_method.slice(1)
                : '-';

            html += `
            <tr>
                <td>${i+1}</td>
                <td>${s.DAS}</td>
                <td>${s.student_name}</td>
                <td>${s.father_name}</td>
                <td>${s.contact_number}</td>
                <td>Rs. ${s.T_Fee}</td>
                <td>${statusBadge}</td>
                <td>${method}</td>
            </tr>`;
        });

        html += '</tbody></table>';
        document.getElementById('tableBody').innerHTML = html;
    })
    .catch(() => {
        document.getElementById('tableBody').innerHTML = '<div class="loading">❌ Something went wrong!</div>';
    });
}

// Download Excel
function downloadExcel() {
    if (!selectedMonth || !selectedYear) {
        alert('Please select month and year first!');
        return;
    }

    window.location.href = '<?= BASE_URL ?>downloadreport.php?month=' + selectedMonth + '&year=' + selectedYear;
}
</script>

</body>
</html>