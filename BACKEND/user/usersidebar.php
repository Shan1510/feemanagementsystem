<?php
include __DIR__ . '/../Master/conection.php';
include __DIR__ . '/../Master/user_auth.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar-main" id="sidebar">
    <div class="sidebar-logo">
        <?php if ($current_page !== 'userdashboard.php'): ?>
            <a href="javascript:history.back()" class="sidebar-back">← Back</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>user/userdashboard.php" class="brand">
            <span class="brand-icon">💰</span>
            <span class="brand-text">
                <h2>Fee System</h2>
                <p>User Panel</p>
            </span>
        </a>
    </div>

    <nav class="sidebar-menu">
        <div class="sidebar-divider">Management</div>
        <a href="../record.php" class="item <?= $current_page === 'record.php' ? 'active' : '' ?>">
            <span class="ico">📅</span> Monthly Fees
        </a>

        <div class="sidebar-divider">Data</div>
        <a href="../allstudentsuser.php" class="item <?= $current_page === 'allstudentsuser.php' ? 'active' : '' ?>">
            <span class="ico">📋</span> All Students
        </a>
    </nav>

    <div class="sidebar-logout">
        <form action="../Master/logout.php" method="post">
            <button type="submit">🚪 Logout (<?= htmlspecialchars($_SESSION['Email'] ?? $_SESSION['username'] ?? 'User') ?>)</button>
        </form>
    </div>
</aside>