<?php
// navbar.php
$conn_exists = isset($conn); // connection check
?>
<link rel="stylesheet" href="<?= BASE_URL ?>navbar.css">

<nav class="top-navbar">
    <div class="nav-brand">💰 Fee System</div>

    <div class="nav-filters">
        <!-- Year -->
        <select id="nav-year" class="nav-select">
            <option value="">📆 Year</option>
            <?php
            $currentYear = date('Y');
            for($y = $currentYear; $y >= $currentYear - 5; $y--) {
                echo "<option value='$y'>$y</option>";
            }
            ?>
        </select>

        <!-- Month -->
        <select id="nav-month" class="nav-select">
            <option value="">📅 Month</option>
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

        <!-- Class -->
        <select id="nav-class" class="nav-select">
            <option value="">🏫 Class</option>
            <?php
            $classes = mysqli_query($conn, "SELECT DISTINCT class_name FROM class ORDER BY class_name");
            while($c = mysqli_fetch_assoc($classes)) {
                echo "<option value='{$c['class_name']}'>{$c['class_name']}</option>";
            }
            ?>
        </select>

        <!-- Section -->
        <select id="nav-sec" class="nav-select">
            <option value="">📋 Section</option>
        </select>

        <!-- Search Button -->
        <button id="nav-search-btn" class="nav-btn" disabled>🔍 Search</button>
    </div>
</nav>

<!-- POPUP -->
<div id="nav-popup-overlay" class="popup-overlay" style="display:none;">
    <div class="popup-box">
        <div class="popup-header">
            <h3 id="popup-title">Fee Status</h3>
            <button onclick="closePopup()" class="popup-close">✕</button>
        </div>
        <div class="popup-body" id="popup-body">
            <p>Loading...</p>
        </div>
    </div>
</div>

<script>
// Class select hone pe section load karo
document.getElementById('nav-class').addEventListener('change', function() {
    let className = this.value;
    let secSelect = document.getElementById('nav-sec');
    secSelect.innerHTML = '<option value="">📋 Section</option>';

    if (!className) return;

    fetch('<?= BASE_URL ?>fetchclass.php?class_name=' + encodeURIComponent(className))
    .then(res => res.json())
    .then(data => {
        data.forEach(sec => {
            secSelect.innerHTML += `<option value="${sec.class_sec}">${sec.class_sec}</option>`;
        });
    });
});

// Jab sab select ho jaye toh button enable karo
['nav-year','nav-month','nav-class','nav-sec'].forEach(id => {
    document.getElementById(id).addEventListener('change', checkAll);
});

function checkAll() {
    let y = document.getElementById('nav-year').value;
    let m = document.getElementById('nav-month').value;
    let c = document.getElementById('nav-class').value;
    let s = document.getElementById('nav-sec').value;
    document.getElementById('nav-search-btn').disabled = !(y && m && c && s);
}

// Search button click → Popup kholo
document.getElementById('nav-search-btn').addEventListener('click', function() {
    let year    = document.getElementById('nav-year').value;
    let month   = document.getElementById('nav-month').value;
    let cls     = document.getElementById('nav-class').value;
    let sec     = document.getElementById('nav-sec').value;

    document.getElementById('nav-popup-overlay').style.display = 'flex';
    document.getElementById('popup-body').innerHTML = '<p class="loading">⏳ Loading...</p>';
    document.getElementById('popup-title').innerText = `Fee Status — ${cls} Section ${sec}`;

    fetch('<?= BASE_URL ?>monthlyview.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `month=${month}&year=${year}&class_name=${encodeURIComponent(cls)}&class_sec=${encodeURIComponent(sec)}&ajax=1`
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById('popup-body').innerHTML = data;
    });
});

function closePopup() {
    document.getElementById('nav-popup-overlay').style.display = 'none';
}

// Overlay pe click se close
document.getElementById('nav-popup-overlay').addEventListener('click', function(e) {
    if (e.target === this) closePopup();
});
</script>