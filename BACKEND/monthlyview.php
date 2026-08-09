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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Fee View — Fee Management System</title>
    <link href="<?= BASE_URL ?>admin/admin.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>monthlyview.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-layout">

    <?php include __DIR__ . '/admin/adminsidebar.php'; ?>

    <div class="main-content">
      <div class="page-container">

        <div class="mv-hero">
            <span class="mv-eyebrow">📅 Fee Collection</span>
            <h1>Monthly Fee View</h1>
            <p>Pick a month, year, class and section to view and manage each student's fee status.</p>
        </div>

        <div class="filter-panel">
            <div class="filter-top">
                <span class="filter-title">🔎 Filter Records</span>
                <span class="filter-hint">All fields are required</span>
            </div>

            <div class="filter-grid">
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
                        for($y = YEAR_START; $y <= YEAR_NOW; $y++) {
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
                <div class="filter-group filter-act">
                    <button class="fetch-btn" id="fetchBtn" onclick="fetchStudents()" disabled>
                        🔍 Fetch Students
                    </button>
                </div>
            </div>
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

        <div class="table-wrap" id="tableWrap" style="display:none;">
            <div id="tableBody"></div>
            <div class="save-wrap" id="saveWrap" style="display:none;">
                <button class="save-btn" id="saveBtn" onclick="saveStatus()">💾 Save Fee Status</button>
                <div id="saveMsg"></div>
            </div>
        </div>

      </div>
    </div>
</div>
<script>
const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="monthlyview.js"></script>

</body>
</html>