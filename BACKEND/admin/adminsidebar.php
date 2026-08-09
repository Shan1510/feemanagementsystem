<?php
include __DIR__ . '/../Master/conection.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar-main" id="sidebar">
    <div class="sidebar-logo">
        <?php if ($current_page !== 'admindashboard.php'): ?>
            <a href="javascript:history.back()" class="sidebar-back">← Back</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>admin/admindashboard.php" class="brand">
            <span class="brand-icon">💰</span>
            <span class="brand-text">
                <h2>Fee System</h2>
                <p>Admin Panel</p>
            </span>
        </a>
    </div>

    <nav class="sidebar-menu">
        <div class="sidebar-divider">Management</div>
        <a href="<?= BASE_URL ?>monthlyview.php" class="item <?= $current_page === 'monthlyview.php' ? 'active' : '' ?>">
            <span class="ico">📅</span> Monthly Fees
        </a>
        <a href="<?= FRONTEND_URL ?>addstudents.php" class="item <?= $current_page === 'addstudents.php' ? 'active' : '' ?>">
            <span class="ico">👨‍🎓</span> Add Student
        </a>
        <a href="<?= FRONTEND_URL ?>addclass.php" class="item <?= $current_page === 'addclass.php' ? 'active' : '' ?>">
            <span class="ico">🏫</span> Add Class
        </a>

        <div class="sidebar-divider">Data</div>
        <a href="<?= BASE_URL ?>allstudents.php" class="item <?= $current_page === 'allstudents.php' ? 'active' : '' ?>">
            <span class="ico">📋</span> All Students
        </a>
        <a href="<?= BASE_URL ?>user.php" class="item <?= $current_page === 'user.php' ? 'active' : '' ?>">
            <span class="ico">👥</span> Users
        </a>
        <a href="<?= BASE_URL ?>report.php" class="item <?= $current_page === 'report.php' ? 'active' : '' ?>">
            <span class="ico">📊</span> Monthly Report
        </a>
        <a href="<?= BASE_URL ?>promote.php" class="item <?= $current_page === 'promote.php' ? 'active' : '' ?>">
            <span class="ico">🚀</span> Promote Students
        </a>
    </nav>

    <div class="sidebar-logout">
        <form action="<?= BASE_URL ?>Master/logout.php" method="post">
            <button type="submit">🚪 Logout</button>
        </form>
    </div>
</aside>

<style>
    .xr-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(3px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .xr-modal-overlay.open { display: flex; }

    .xr-modal {
        background: #fff;
        width: 92%;
        max-width: 430px;
        border-radius: 18px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
        overflow: hidden;
    }

    .xr-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 20px 22px;
        background: linear-gradient(120deg, #1e1b4b 0%, #312e81 100%);
        color: #fff;
    }

    .xr-modal-head h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .xr-modal-close {
        border: none;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        font-size: 0.95rem;
        cursor: pointer;
        line-height: 1;
    }

    .xr-modal-close:hover { background: rgba(255, 255, 255, 0.25); }

    .xr-modal-body { padding: 22px 24px; }

    .xr-field { margin-bottom: 16px; }

    .xr-field label {
        display: block;
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #475569;
        margin-bottom: 6px;
    }

    .xr-field select {
        width: 100%;
        padding: 11px 13px;
        border: 1.5px solid #e2e8f0;
        border-radius: 11px;
        background: #f8fafc;
        font-size: 0.94rem;
        color: #1e293b;
        outline: none;
        font-family: inherit;
        cursor: pointer;
    }

    .xr-field select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
    }

    .xr-hint {
        font-size: 0.74rem;
        color: #94a3b8;
        margin-top: -8px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .xr-modal .xr-download {
        width: 100%;
        padding: 13px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #fff;
        font-size: 0.95rem;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
    }

    .xr-modal .xr-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(79, 70, 229, 0.45);
    }

    .xr-modal .xr-download:disabled { opacity: 0.6; transform: none; }
</style>

<div class="xr-modal-overlay" id="xrModalOverlay">
    <div class="xr-modal" role="dialog" aria-modal="true" aria-labelledby="xrTitle">
        <div class="xr-modal-head">
            <h3 id="xrTitle">📥 Download Excel Report</h3>
            <button type="button" class="xr-modal-close" onclick="closeExcelReportModal()" aria-label="Close">✕</button>
        </div>
        <div class="xr-modal-body">
            <div class="xr-field">
                <label for="xrMonth">Month</label>
                <select id="xrMonth">
                    <?php foreach ([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $num => $name): ?>
                        <option value="<?= $num ?>" <?= (int)date('n') === $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="xr-field">
                <label for="xrYear">Year</label>
                <select id="xrYear">
                    <?php for ($y = YEAR_START; $y <= YEAR_NOW; $y++): ?>
                        <option value="<?= $y ?>" <?= (int)date('Y') === $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="xr-hint">💡 Report includes every class and student for the selected month.</div>
            <button type="button" class="xr-download" id="xrDownloadBtn" onclick="downloadExcelReport()">⬇️ Download Report</button>
        </div>
    </div>
</div>

<script>
    function openExcelReportModal() {
        document.getElementById('xrModalOverlay').classList.add('open');
    }

    function closeExcelReportModal() {
        document.getElementById('xrModalOverlay').classList.remove('open');
    }

    function downloadExcelReport() {
        var month = document.getElementById('xrMonth').value;
        var year  = document.getElementById('xrYear').value;

        if (!month || !year) {
            alert('Please select both month and year.');
            return;
        }

        var btn = document.getElementById('xrDownloadBtn');
        btn.disabled = true;
        btn.innerHTML = '⏳ Generating...';

        window.location.href = '<?= BASE_URL ?>downloadreport.php?month=' + month + '&year=' + year;
    }

    document.getElementById('xrModalOverlay').addEventListener('click', function (e) {
        if (e.target === this) closeExcelReportModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('xrModalOverlay').classList.contains('open')) {
            closeExcelReportModal();
        }
    });
</script>