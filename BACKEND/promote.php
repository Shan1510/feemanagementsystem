<?php
include __DIR__ . '/Master/conection.php';
include __DIR__ . '/Master/admin_auth.php';

$classRes = $conn->query("SELECT * FROM class ORDER BY class_name, class_sec");
$classArr = [];
while($c = $classRes->fetch_assoc()) $classArr[] = $c;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Promotion — Fee Management System</title>
    <link href="admin/admin.css" rel="stylesheet">
    <style>
        .promo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 1080px;
        }

        .promo-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .promo-badge {
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: rgba(79, 70, 229, 0.9);
            color: #fff;
            margin-bottom: 14px;
        }

        .promo-badge + p {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 18px;
        }

        .arrow-pill {
            align-self: center;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 999px;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            color: #4338ca;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin: 14px 0;
        }

        .student-preview {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-left: 3px solid var(--success);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            margin-bottom: 8px;
        }

        .student-preview .found {
            color: var(--success);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .student-preview .name {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 2px;
        }

        .student-preview .detail {
            color: var(--muted);
            font-size: 0.82rem;
        }

        .promo-msg {
            display: none;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 14px;
        }

        .promo-msg-success {
            display: block;
            background: var(--success-light);
            color: var(--success-text);
            border: 1px solid #86efac;
        }

        .promo-msg-error {
            display: block;
            background: var(--danger-light);
            color: var(--danger-text);
            border: 1px solid #fca5a5;
        }

        .stat-strip {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .stat-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 22px;
            box-shadow: var(--shadow-sm);
        }

        .stat-pill .stat-val {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1.1;
        }

        .stat-pill .stat-lbl {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            font-weight: 700;
        }

        @media (max-width: 900px) {
            .promo-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="dashboard-layout">
    <?php include __DIR__ . '/admin/adminsidebar.php'; ?>

    <div class="main-content">
        <div class="page-container">
            <div class="page-header">
                <div>
                    <h1>🚀 Student Promotion</h1>
                    <p>Promote students to their next class — as a whole class or individually</p>
                </div>
            </div>

            <div class="stat-strip">
                <div class="stat-pill">
                    <span style="font-size:22px">🏫</span>
                    <div>
                        <div class="stat-lbl">Total Classes</div>
                        <div class="stat-val"><?= count($classArr) ?></div>
                    </div>
                </div>
                <div class="stat-pill">
                    <span style="font-size:22px">⬆️</span>
                    <div>
                        <div class="stat-lbl">Bulk Promote</div>
                        <div class="stat-val">Whole Class</div>
                    </div>
                </div>
                <div class="stat-pill">
                    <span style="font-size:22px">👤</span>
                    <div>
                        <div class="stat-lbl">Individual</div>
                        <div class="stat-val">By DAS</div>
                    </div>
                </div>
            </div>

            <div class="promo-grid">
                <!-- CARD 1: Whole Class -->
                <div class="card promo-card">
                    <span class="promo-badge">◆ Batch Promotion</span>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#0f172a; margin:0 0 6px;">Promote Whole Class</h2>
                    <p>Select a class and shift all its students to the next class in one action.</p>

                    <hr style="border:none; border-top:1px solid var(--border); margin:16px 0;">

                    <div class="form-field">
                        <label>Current Class</label>
                        <select id="currentClass" class="form-control">
                            <option value="">Select Class</option>
                            <?php foreach($classArr as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['class_name']) ?> — <?= htmlspecialchars($c['class_sec']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <span class="arrow-pill">▼ Promotes to</span>

                    <div class="form-field">
                        <label>Promote To</label>
                        <select id="nextClass" class="form-control">
                            <option value="">Select Next Class</option>
                            <?php foreach($classArr as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['class_name']) ?> — <?= htmlspecialchars($c['class_sec']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn btn-primary btn-block" id="promoteClassBtn" onclick="promoteClass()" style="margin-top:auto;">
                        🎓 &nbsp;Promote Whole Class
                    </button>
                    <div class="promo-msg" id="classMsg"></div>
                </div>

                <!-- CARD 2: Individual -->
                <div class="card promo-card">
                    <span class="promo-badge">◆ Individual Promotion</span>
                    <h2 style="font-size:1.15rem; font-weight:800; color:#0f172a; margin:0 0 6px;">Promote Individual</h2>
                    <p>Enter a student's DAS number to find and promote them to their next class.</p>

                    <hr style="border:none; border-top:1px solid var(--border); margin:16px 0;">

                    <div class="form-field">
                        <label>DAS Number</label>
                        <input type="text" id="studentDAS" class="form-control"
                               placeholder="Enter DAS number..."
                               oninput="searchStudentForPromote()">
                    </div>

                    <div class="student-preview d-none" id="studentPreview" style="display:none;">
                        <div class="found">● Student Found</div>
                        <p class="name" id="previewName"></p>
                        <p class="detail" id="previewClass"></p>
                        <p class="detail" id="previewFather"></p>
                    </div>

                    <span class="arrow-pill">▼ Promotes to</span>

                    <div class="form-field">
                        <label>Promote To</label>
                        <select id="nextClassIndividual" class="form-control">
                            <option value="">Select Next Class</option>
                            <?php foreach($classArr as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['class_name']) ?> — <?= htmlspecialchars($c['class_sec']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn btn-primary btn-block" id="promoteStudentBtn" onclick="promoteStudent()" style="margin-top:auto;">
                        🎓 &nbsp;Promote Student
                    </button>
                    <div class="promo-msg" id="studentMsg"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let foundStudentId = null;
let searchTimeout  = null;

function searchStudentForPromote() {
    clearTimeout(searchTimeout);
    const DAS     = document.getElementById('studentDAS').value.trim();
    const preview = document.getElementById('studentPreview');

    if (!DAS) {
        preview.style.display = 'none';
        foundStudentId = null;
        return;
    }

    searchTimeout = setTimeout(() => {
        fetch('<?= BASE_URL ?>search.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'DAS=' + encodeURIComponent(DAS)
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                preview.style.display = 'none';
                foundStudentId = null;
                return;
            }
            foundStudentId = data.id;
            document.getElementById('previewName').innerText   = data.student_name;
            document.getElementById('previewClass').innerText  = '📚 Class: ' + (data.class_name ?? 'N/A') + ' — ' + (data.class_sec ?? '');
            document.getElementById('previewFather').innerText = '👤 Father: ' + data.father_name;
            preview.style.display = 'block';
        })
        .catch(() => {
            preview.style.display = 'none';
            foundStudentId = null;
        });
    }, 500);
}

function promoteClass() {
    const currentClass = document.getElementById('currentClass').value;
    const nextClass    = document.getElementById('nextClass').value;
    const btn          = document.getElementById('promoteClassBtn');
    const msg          = document.getElementById('classMsg');

    if (!currentClass || !nextClass) { showMsg(msg, '❌ Please select both classes!', false); return; }
    if (currentClass === nextClass)  { showMsg(msg, '❌ Current and next class cannot be the same!', false); return; }
    if (!confirm('Are you sure? All students of this class will be promoted!')) return;

    btn.disabled  = true;
    btn.innerText = '⏳  Promoting...';

    fetch('<?= BASE_URL ?>promoteaction.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=class&current_class_id=${currentClass}&next_class_id=${nextClass}`
    })
    .then(r => r.json())
    .then(data => {
        showMsg(msg, data.success ? '✅ ' + data.message : '❌ ' + data.message, data.success);
        if (data.success) {
            document.getElementById('currentClass').value = '';
            document.getElementById('nextClass').value    = '';
        }
        btn.disabled  = false;
        btn.innerText = '🎓  Promote Whole Class';
    })
    .catch(() => {
        showMsg(msg, '❌ Something went wrong!', false);
        btn.disabled  = false;
        btn.innerText = '🎓  Promote Whole Class';
    });
}

function promoteStudent() {
    const nextClass = document.getElementById('nextClassIndividual').value;
    const btn       = document.getElementById('promoteStudentBtn');
    const msg       = document.getElementById('studentMsg');

    if (!foundStudentId) { showMsg(msg, '❌ Please enter a valid DAS number!', false); return; }
    if (!nextClass)      { showMsg(msg, '❌ Please select next class!', false); return; }
    if (!confirm('Are you sure you want to promote this student?')) return;

    btn.disabled  = true;
    btn.innerText = '⏳  Promoting...';

    fetch('<?= BASE_URL ?>promoteaction.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=individual&student_id=${foundStudentId}&next_class_id=${nextClass}`
    })
    .then(r => r.json())
    .then(data => {
        showMsg(msg, data.success ? '✅ ' + data.message : '❌ ' + data.message, data.success);
        if (data.success) {
            document.getElementById('studentDAS').value             = '';
            document.getElementById('nextClassIndividual').value    = '';
            document.getElementById('studentPreview').style.display = 'none';
            foundStudentId = null;
        }
        btn.disabled  = false;
        btn.innerText = '🎓  Promote Student';
    })
    .catch(() => {
        showMsg(msg, '❌ Something went wrong!', false);
        btn.disabled  = false;
        btn.innerText = '🎓  Promote Student';
    });
}

function showMsg(el, text, success) {
    el.className = 'promo-msg' + (success ? ' promo-msg-success' : ' promo-msg-error');
    el.innerText = text;
    setTimeout(() => { el.className = 'promo-msg'; el.innerText = ''; }, 4000);
}
</script>
</body>
</html>