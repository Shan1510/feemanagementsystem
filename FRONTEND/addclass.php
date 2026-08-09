<?php
include __DIR__ . '/../BACKEND/Master/conection.php';
include __DIR__ . '/../BACKEND/Master/admin_auth.php';
$totalClasses   = $conn->query("SELECT COUNT(*) AS c FROM class")->fetch_assoc()['c'];
$totalSections  = $conn->query("SELECT COUNT(DISTINCT class_sec) AS c FROM class WHERE class_sec IS NOT NULL AND class_sec <> ''")->fetch_assoc()['c'];
$studentCount   = $conn->query("SELECT COUNT(*) AS c FROM student")->fetch_assoc()['c'];
$recent = [];
$recentRes = $conn->query("SELECT class_name, class_sec FROM class ORDER BY id DESC LIMIT 6");
while ($r = $recentRes->fetch_assoc()) $recent[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Class — Fee Management System</title>
    <link href="../BACKEND/admin/admin.css" rel="stylesheet">
    <style>
        .enroll-wrap {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 26px;
            align-items: start;
        }

        /* ===== LEFT: OVERVIEW PANEL ===== */
        .preview-panel {
            position: sticky;
            top: 24px;
            background: linear-gradient(165deg, #111c3a 0%, #1e1b4b 60%, #312e81 100%);
            border-radius: 20px;
            padding: 26px;
            color: #fff;
            overflow: hidden;
            box-shadow: 0 22px 46px rgba(30, 27, 75, 0.4);
        }

        .preview-panel::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            right: -90px;
            top: -90px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.5), transparent 70%);
        }

        .preview-top {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .preview-top .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.2); }
            50%      { box-shadow: 0 0 0 8px rgba(52, 211, 153, 0.05); }
        }

        .preview-top span {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: #c7d2fe;
        }

        .preview-icon {
            width: 74px;
            height: 74px;
            border-radius: 20px;
            margin: 22px 0 14px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.1rem;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 24px rgba(79, 70, 229, 0.45);
        }

        .preview-name {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            position: relative;
            z-index: 1;
        }

        .preview-sub {
            color: #a5b4c8;
            font-size: 0.82rem;
            margin-top: 3px;
            position: relative;
            z-index: 1;
        }

        .preview-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 24px;
            position: relative;
            z-index: 1;
        }

        .preview-stat {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .preview-stat small {
            display: block;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #a5b4c8;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .preview-stat strong {
            font-size: 1.15rem;
            font-weight: 800;
        }

        .recent-list {
            margin-top: 16px;
            position: relative;
            z-index: 1;
        }

        .recent-title {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #a5b4c8;
            margin-bottom: 10px;
        }

        .recent-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 9px 13px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 7px;
            font-size: 0.82rem;
        }

        .recent-item span { color: #e2e8f0; font-weight: 600; }
        .recent-item em { font-style: normal; font-size: 0.62rem; color: #a5b4c8; text-transform: uppercase; }

        /* ---------- RIGHT: FORM CARD ========== */
        .form-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #e8edf3;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
            padding: 30px 32px;
        }

        .form-card-head {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 18px;
            margin-bottom: 24px;
            border-bottom: 1px solid #eef2f7;
        }

        .fc-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.35);
        }

        .form-card-head h2 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
        }

        .form-card-head p {
            font-size: 0.8rem;
            color: #64748b;
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.68rem;
            font-weight: 800;
            color: #6366f1;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin: 22px 0 14px;
        }

        .section-tag .bar {
            width: 22px;
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
        }

        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            font-size: 0.76rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .field-icon {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            transition: all 0.2s ease;
        }

        .field-icon:focus-within {
            border-color: #6366f1;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .field-icon .fi-ico {
            font-size: 1.05rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        .field-icon input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.94rem;
            font-family: inherit;
            color: #1e293b;
        }

        .field-icon input::placeholder { color: #94a3b8; }

        .field-hint {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #eef2f7;
        }

        .form-actions .btn-submit-glow {
            flex: 1;
        }

        .btn-submit-glow {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            font-weight: 700;
            padding: 13px 22px;
            border: none;
            border-radius: 12px;
            font-size: 0.94rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }

        .btn-submit-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(79, 70, 229, 0.45);
        }

        #responseMsg {
            margin-top: 16px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 600;
            display: none;
            align-items: center;
            gap: 8px;
        }

        .alert-success {
            display: flex;
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #86efac;
        }

        .alert-error {
            display: flex;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @media (max-width: 1100px) {
            .enroll-wrap { grid-template-columns: 1fr; }
            .preview-panel { position: static; }
        }

        /* ===== FIXED: compact single-screen layout (no page scroll) ===== */
        .main-content {
            padding: 18px 36px;
        }

        .page-header {
            margin-bottom: 14px;
        }

        .enroll-wrap {
            gap: 20px;
        }

        .preview-panel {
            padding: 20px 22px;
        }

        .preview-icon {
            margin: 14px 0 10px;
        }

        .preview-stats {
            margin-top: 16px;
        }

        .recent-list {
            margin-top: 12px;
        }

        .form-card {
            padding: 22px 26px;
        }

        .form-card-head {
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .section-tag {
            margin: 14px 0 10px;
        }

        .field {
            margin-bottom: 12px;
        }

        .form-actions {
            margin-top: 16px;
            padding-top: 14px;
        }
    </style>
</head>
<body>
<div class="dashboard-layout">
    <?php include __DIR__ . '/../BACKEND/admin/adminsidebar.php'; ?>

    <main class="main-content">
        <div class="page-container">
            <div class="page-header">
                <div>
                    <h1>🏫 Add Class</h1>
                    <p>Register a new class and section</p>
                </div>
            </div>

            <div class="enroll-wrap">
                <!-- OVERVIEW -->
                <aside class="preview-panel">
                    <div class="preview-top">
                        <span class="dot"></span>
                        <span>Class Setup</span>
                    </div>

                    <div class="preview-icon">🏫</div>
                    <div class="preview-name">New Class</div>
                    <div class="preview-sub">Add it once, use it everywhere</div>

                    <div class="preview-stats">
                        <div class="preview-stat">
                            <small>Classes</small>
                            <strong><?= $totalClasses ?></strong>
                        </div>
                        <div class="preview-stat">
                            <small>Sections</small>
                            <strong><?= $totalSections ?></strong>
                        </div>
                    </div>

                    <?php if (!empty($recent)): ?>
                    <div class="recent-list">
                        <div class="recent-title">Recent Classes</div>
                        <?php foreach ($recent as $r): ?>
                            <div class="recent-item">
                                <span><?= htmlspecialchars($r['class_name']) ?></span>
                                <em><?= htmlspecialchars($r['class_sec'] ?? '—') ?></em>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </aside>

                <!-- FORM -->
                <section class="form-card">
                    <div class="form-card-head">
                        <div class="fc-icon">➕</div>
                        <div>
                            <h2>Class Information</h2>
                            <p>A class + section gives students a home.</p>
                        </div>
                    </div>

                    <form id="addClassForm" action="../BACKEND/addclass.php" method="post">
                        <div class="section-tag"><span class="bar"></span> Class Details</div>

                        <div class="field">
                            <label for="class_name">Class Name</label>
                            <div class="field-icon">
                                <span class="fi-ico">📚</span>
                                <input type="text" id="class_name" name="class_name"
                                       placeholder="e.g. Class 6" required>
                            </div>
                            <span class="field-hint">💡 The name is used across all students</span>
                        </div>

                        <div class="field">
                            <label for="class_sec">Class Section</label>
                            <div class="field-icon">
                                <span class="fi-ico">🗂️</span>
                                <input type="text" id="class_sec" name="class_sec"
                                       placeholder="e.g. Section A">
                            </div>
                            <span class="field-hint">🧩 Optional — add if the class has sections</span>
                        </div>

                        <div id="responseMsg"></div>

                        <div class="form-actions">
                            <button type="submit" class="btn-submit-glow" id="submitBtn">💾 Save Class</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
</div>

<script>
document.getElementById('addClassForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const msgBox = document.getElementById('responseMsg');
    const btn    = document.getElementById('submitBtn');

    msgBox.textContent = '';
    msgBox.className = '';
    btn.disabled = true;
    btn.innerHTML = '⏳ Saving...';

    fetch('../BACKEND/addclass.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        msgBox.textContent = data.message;
        msgBox.className = data.success ? 'alert-success' : 'alert-error';
        if (data.success) {
            form.reset();
            setTimeout(() => window.location.reload(), 1200);
        }
    })
    .catch(() => {
        msgBox.textContent = 'Something went wrong. Please try again.';
        msgBox.className = 'alert-error';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '💾 Save Class';
    });
});
</script>

</body>
</html>