<?php
// simple_navbar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div style="
    background: #3b82f6;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-radius: 8px;
">

    <!-- LEFT: Title -->
    <div style="font-weight: bold; font-size: 1.2rem;">
        Fee Management System
    </div>

    <!-- RIGHT: Back + User + Logout -->
    <div style="display: flex; align-items: center; gap: 12px;">

        <?php if ($current_page != 'dashboard.php'): ?>
            <a href="javascript:history.back()" style="
                color: white;
                text-decoration: none;
                background: rgba(255,255,255,0.2);
                padding: 8px 15px;
                border-radius: 5px;
            ">← Back</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['username'])): ?>
            <span><?php echo $_SESSION['username']; ?></span>

            <a href="../FRONTEND/login.html" style="
                color: white;
                text-decoration: none;
                background: rgba(255,255,255,0.2);
                padding: 8px 15px;
                border-radius: 5px;
            ">Logout</a>
        <?php endif; ?>

    </div>
</div>
