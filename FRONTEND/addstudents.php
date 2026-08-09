<?php
include __DIR__ . '/../BACKEND/Master/conection.php';
include __DIR__ . '/../BACKEND/Master/admin_auth.php';
$classRes = $conn->query("SELECT DISTINCT class_name FROM class ORDER BY class_name");
$classes = [];
while ($r = $classRes->fetch_assoc()) $classes[] = $r['class_name'];
$totalStudents = $conn->query("SELECT COUNT(*) c FROM student")->fetch_assoc()['c'];
$activeClasses = $conn->query("SELECT COUNT(DISTINCT class_name) c FROM class")->fetch_assoc()['c'];
$classSections = [];
$secRes = $conn->query("SELECT class_name, class_sec FROM class WHERE class_sec IS NOT NULL AND class_sec <> '' ORDER BY class_name, class_sec");
while ($row = $secRes->fetch_assoc()) $classSections[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student — Fee Management System</title>
    <link href="../BACKEND/admin/admin.css" rel="stylesheet">
    <style>
        .enroll-wrap {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 26px;
            align-items: start;
            height: calc(100vh - 175px);
            min-height: 480px;
        }

        main.main-content {
            height: 100vh;
            overflow: hidden;
        }

        .form-card {
            height: 100%;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .form-card::-webkit-scrollbar { display: none; }

        /* ===== LEFT: LIVE SUMMARY PANEL ===== */
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
            background: radial-gradient(circle, rgba(124, 58, 237, 0.55), transparent 70%);
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

        .preview-avatar {
            width: 74px;
            height: 74px;
            border-radius: 20px;
            margin: 22px 0 14px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            position: relative;
            z-index: 1;
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

        .preview-rows {
            margin: 22px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .preview-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .preview-row small {
            color: #a5b4c8;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .preview-row strong {
            font-size: 0.84rem;
            font-weight: 700;
            text-align: right;
            color: #e2e8f0;
            max-width: 55%;
            min-height: 1.05em;
            word-break: break-word;
        }

        .preview-row strong.fee {
            color: #fbbf24;
            font-size: 1.02rem;
        }

        .preview-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
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

        .section-tag:first-of-type { margin-top: 0; }

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

        .field-icon input,
        .field-icon select {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.94rem;
            font-family: inherit;
            color: #1e293b;
            cursor: pointer;
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

        #responseMsg.msg-success {
            display: flex;
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #86efac;
        }

        #responseMsg.msg-error {
            display: flex;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @media (max-width: 1100px) {
            .enroll-wrap { grid-template-columns: 1fr; }
            .preview-panel { position: static; }
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
                    <h1>👨‍🎓 Add Student</h1>
                    <p>Register a new student to the system</p>
                </div>
            </div>

            <div class="enroll-wrap">
                <!-- LIVE SUMMARY -->
                <aside class="preview-panel">
                    <div class="preview-top">
                        <span class="dot"></span>
                        <span>New Admission</span>
                    </div>

                    <div class="preview-avatar" id="pAvatar">🧑</div>
                    <div class="preview-name" id="pName">Student Name</div>
                    <div class="preview-sub" id="pDAS">Enter a DAS number</div>

                    <div class="preview-rows">
                        <div class="preview-row">
                            <small>Father</small>
                            <strong id="pFather">—</strong>
                        </div>
                        <div class="preview-row">
                            <small>Contact</small>
                            <strong id="pContact">—</strong>
                        </div>
                        <div class="preview-row">
                            <small>Class / Section</small>
                            <strong id="pClass">—</strong>
                        </div>
                        <div class="preview-row">
                            <small>Monthly Fee</small>
                            <strong class="fee" id="pFee">Rs. 0</strong>
                        </div>
                    </div>

                    <div class="preview-stats">
                        <div class="preview-stat">
                            <small>Students</small>
                            <strong><?= $totalStudents ?></strong>
                        </div>
                        <div class="preview-stat">
                            <small>Classes</small>
                            <strong><?= $activeClasses ?></strong>
                        </div>
                    </div>
                </aside>

                <!-- FORM -->
                <section class="form-card">
                    <div class="form-card-head">
                        <div class="fc-icon">✍️</div>
                        <div>
                            <h2>Student Information</h2>
                            <p>Fill in the details below, the summary updates live.</p>
                        </div>
                    </div>

                    <form id="addStudentForm" method="post">
                        <div class="section-tag"><span class="bar"></span> Personal Details</div>

                        <div class="field">
                            <label for="DAS">DAS Number</label>
                            <div class="field-icon">
                                <span class="fi-ico">🪪</span>
                                <input type="text" id="DAS" name="DAS" placeholder="Enter DAS number" required>
                            </div>
                            <span class="field-hint">💡 Unique admission number</span>
                        </div>

                        <div class="field">
                            <label for="Studentname">Student Name</label>
                            <div class="field-icon">
                                <span class="fi-ico">🧑</span>
                                <input type="text" id="Studentname" name="Studentname" placeholder="Full name" required>
                            </div>
                        </div>

                        <div class="field">
                            <label for="Fathername">Father Name</label>
                            <div class="field-icon">
                                <span class="fi-ico">👨</span>
                                <input type="text" id="Fathername" name="Fathername" placeholder="Father's name" required>
                            </div>
                        </div>

                        <div class="field">
                            <label for="Contactnumber">Contact Number</label>
                            <div class="field-icon">
                                <span class="fi-ico">📱</span>
                                <input type="text" id="Contactnumber" name="Contactnumber" placeholder="03XXXXXXXXX" required>
                            </div>
                            <span class="field-hint">📲 Use format 03XXXXXXXXX</span>
                        </div>

                        <div class="section-tag"><span class="bar"></span> Class &amp; Fee</div>

                        <div class="field">
                            <label for="T_fee">Tuition Fee</label>
                            <div class="field-icon">
                                <span class="fi-ico">💰</span>
                                <input type="number" id="T_fee" name="T_fee" placeholder="Monthly fee" required min="0">
                            </div>
                            <span class="field-hint">💵 Monthly fee in Rupees</span>
                        </div>

                        <div class="field">
                            <label for="Class">Class</label>
                            <div class="field-icon">
                                <span class="fi-ico">📚</span>
                                <select id="Class" name="Class" required>
                                    <option value="">Select Class</option>
                                    <?php foreach ($classes as $c): ?>
                                        <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <label for="Section">Section</label>
                            <div class="field-icon">
                                <span class="fi-ico">🗂️</span>
                                <select id="Section" name="Section" required>
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                            <span class="field-hint" id="secHint">🗒️ Pick a class first to see sections</span>
                        </div>

                        <div id="responseMsg"></div>

                        <div class="form-actions">
                            <button type="submit" class="btn-submit-glow" id="submitBtn">💾 Save Student</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
</div>

<script>
const CLASS_SECTIONS = <?= json_encode($classSections) ?>;

document.addEventListener('DOMContentLoaded', function () {
    const secSel = document.getElementById('Section');
    const classSel = document.getElementById('Class');
    const hint = document.getElementById('secHint');
    secSel.disabled = true;

    // live preview
    document.getElementById('Studentname').addEventListener('input', function (e) {
        const v = e.target.value.trim();
        document.getElementById('pName').innerText = v || 'Student Name';
        const p = (v || '?').split(/\s+/);
        const ini = ((p[0] || '?')[0] + (p[1] ? p[1][0] : '')).toUpperCase();
        document.getElementById('pAvatar').innerText = ini || '?';
    });

    document.getElementById('DAS').addEventListener('input', function (e) {
        document.getElementById('pDAS').innerText = e.target.value.trim() || 'Enter a DAS number';
    });

    document.getElementById('Fathername').addEventListener('input', function (e) {
        document.getElementById('pFather').innerText = e.target.value.trim() || '—';
    });

    document.getElementById('Contactnumber').addEventListener('input', function (e) {
        document.getElementById('pContact').innerText = e.target.value.trim() || '—';
    });

    document.getElementById('T_fee').addEventListener('input', function (e) {
        document.getElementById('pFee').innerText = 'Rs. ' + (e.target.value || '0');
    });

    function updatePreviewClass() {
        const cls = classSel.value;
        const sec = secSel.value;
        document.getElementById('pClass').innerText = cls ? cls + (sec ? ' / ' + sec : '') : '—';
    }

    classSel.addEventListener('change', function () {
        const cls = this.value;
        secSel.innerHTML = '<option value="">Select Section</option>';
        secSel.disabled  = !cls;
        CLASS_SECTIONS.filter(s => s.class_name === cls).forEach(s => {
            secSel.innerHTML += `<option value="${s.class_sec}">${s.class_sec}</option>`;
        });
        updatePreviewClass();
        if (hint) hint.innerText = cls ? '🗒️ Select the matching section below' : '🗒️ Pick a class first to see sections';
    });

    secSel.addEventListener('change', updatePreviewClass);
});

document.getElementById('addStudentForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const msgBox = document.getElementById('responseMsg');
    const btn    = document.getElementById('submitBtn');

    msgBox.textContent = '';
    msgBox.className = '';
    btn.disabled = true;
    btn.innerHTML = '⏳ Saving...';

    fetch('../BACKEND/addstudent.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        msgBox.textContent = data.message;
        msgBox.className = data.success ? 'msg-success' : 'msg-error';
        if (data.success) {
            form.reset();
            document.getElementById('Section').innerHTML = '<option value="">Select Section</option>';
            document.getElementById('Section').disabled = true;
            document.getElementById('pName').innerText = 'Student Name';
            document.getElementById('pDAS').innerText   = 'Enter a DAS number';
            document.getElementById('pAvatar').innerText = '🧑';
            document.getElementById('pFather').innerText = '—';
            document.getElementById('pContact').innerText = '—';
            document.getElementById('pClass').innerText  = '—';
            document.getElementById('pFee').innerText    = 'Rs. 0';
        }
    })
    .catch(() => {
        msgBox.textContent = 'Something went wrong. Please try again.';
        msgBox.className = 'msg-error';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '💾 Save Student';
    });
});
</script>

</body>
</html>