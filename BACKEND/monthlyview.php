<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$monthNames = [
    1=>'January',  2=>'February', 3=>'March',     4=>'April',
    5=>'May',      6=>'June',     7=>'July',       8=>'August',
    9=>'September',10=>'October', 11=>'November',  12=>'December'
];

$classRes = $conn->query("SELECT DISTINCT class_name FROM class ORDER BY class_name");
$classes  = [];
while($c = $classRes->fetch_assoc()) $classes[] = $c['class_name'];
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Fee View</title>
    <link href="admin/style.css" rel="stylesheet">
    <link href="admin/admin.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>monthlyview.css" rel="stylesheet">
    
    <style>
        .dashboard-layout {
            display: flex !important;
        }
        .main-content {
            flex: 1 !important;
            min-width: 0 !important;
            padding: 30px !important;
            justify-content: flex-start !important;
            align-items: stretch !important;
        }
        .filter-box, .table-wrap, #resultHeader {
            width: 100% !important;
            box-sizing: border-box !important;
        }
    </style>

</head>
<body>
<div class="dashboard-layout">

    <?php include __DIR__ . '/admin/adminsidebar.php'; ?>

    <div class="main-content">

        <h2 style="font-size:1.8rem; font-weight:700; color:#0f172a; margin-bottom:25px;">📅 Monthly Fee View</h2>

        <div class="filter-box">
            <div class="filter-group">
                <label>Month</label>
                <select id="f-month" onchange="checkFilters()">
                    <option value="">Select Month</option>
                    <?php foreach($monthNames as $num => $name): ?>
                        <option value="<?= $num ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Year</label>
                <select id="f-year" onchange="checkFilters()">
                    <option value="">Select Year</option>
                    <?php
                    $cy = date('Y');
                    for($y = $cy; $y >= $cy - 5; $y--) {
                        echo "<option value='$y'>$y</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Class</label>
                <select id="f-class" onchange="loadSections()">
                    <option value="">Select Class</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Section</label>
                <select id="f-sec" onchange="checkFilters()" disabled>
                    <option value="">Select Section</option>
                </select>
            </div>
            <button class="fetch-btn" id="fetchBtn" onclick="fetchStudents()" disabled>
                🔍 Fetch Students
            </button>
        </div>

        <div id="resultHeader" style="display:none;">
            <div class="result-header">
                <h3 id="resultTitle">-</h3>
                <div class="badges">
                    <span class="badge-paid"   id="paidCount">Paid: 0</span>
                    <span class="badge-unpaid" id="unpaidCount">Unpaid: 0</span>
                </div>
            </div>
        </div>

        <!-- ✅ THIS WAS THE BUG: tableWrap was visible on page load -->
        <div class="table-wrap" id="tableWrap" style="display:none;">
            <div id="tableBody"></div>
            <div class="save-wrap" id="saveWrap" style="display:none;">
                <button class="save-btn" id="saveBtn" onclick="saveStatus()">💾 Save Fee Status</button>
                <div id="saveMsg"></div>
            </div>
        </div>

    </div>
</div>

<script>
    console.log(document.querySelector('.dashboard-layout').getBoundingClientRect().width)
    
    console.log("rooshan");
    
    
    
const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="monthlyview.js"></script>

</body>
</html>