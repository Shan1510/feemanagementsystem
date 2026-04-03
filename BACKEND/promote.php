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
    <title>Student Promotion</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="admin/style.css" rel="stylesheet">
    <link href="admin/admin.css" rel="stylesheet">

    <style>
        /* Only theme overrides — no layout CSS */
        :root {
            --gold:       #c9a96e;
            --gold-light: #e8d5b0;
            --card-bg:    #141416;
            --card-border:#1f1f28;
            --input-bg:   #0d0d0f;
        }

        .main-content { background: #f0eff0; }

        /* Dark cards */
        .promo-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border) !important;
            transition: transform .25s, box-shadow .25s, border-color .25s;
            overflow: hidden;
        }
        .promo-card::before {
            content: '';
            display: block;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light), var(--gold));
            opacity: 0;
            transition: opacity .3s;
        }
        .promo-card:hover { transform: translateY(-3px); box-shadow: 0 20px 60px rgba(0,0,0,.4) !important; border-color: #2e2e3a !important; }
        .promo-card:hover::before { opacity: 1; }

        /* Badge */
        .gold-badge {
            background: rgba(201,169,110,.1);
            border: 1px solid rgba(201,169,110,.25);
            color: var(--gold);
            font-size: 10px;
            letter-spacing: 2px;
            font-weight: 700;
        }
        .badge-dot {
            width: 6px; height: 6px;
            background: var(--gold);
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s ease infinite;
        }
        @keyframes pulse {
            0%,100%{ opacity:1; transform:scale(1); }
            50%    { opacity:.5; transform:scale(.8); }
        }

        /* Inputs */
        .form-control, .form-select {
            background: var(--input-bg) !important;
            border: 1px solid var(--card-border) !important;
            color: #e8e4de !important;
            border-radius: 10px !important;
        }
        .form-control::placeholder { color: #3a3a46 !important; }
        .form-control:focus, .form-select:focus {
            border-color: var(--gold) !important;
            box-shadow: 0 0 0 3px rgba(201,169,110,.12) !important;
            background: #111113 !important;
        }
        .form-select option { background: #1a1a1f; color: #e8e4de; }
        .form-label { color: #4a4a58; font-size: 10px; text-transform: uppercase; letter-spacing: 1.8px; font-weight: 700; }

        /* Promotes-to pill */
        .arrow-pill {
            background: rgba(201,169,110,.07);
            border: 1px solid rgba(201,169,110,.18);
            color: #6a6a78;
            font-size: 10px;
            letter-spacing: 1.5px;
            font-weight: 600;
        }
        .arrow-pill i { color: var(--gold); }

        /* Student preview */
        .student-preview {
            background: var(--input-bg);
            border: 1px solid var(--card-border) !important;
            border-left: 3px solid var(--gold) !important;
            animation: slideIn .25s ease;
        }
        @keyframes slideIn {
            from { opacity:0; transform:translateY(-6px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .found-tag { color:#6ee7b7; font-size:9px; letter-spacing:2px; font-weight:700; text-transform:uppercase; }
        .found-tag::before { content:''; display:inline-block; width:5px; height:5px; background:#6ee7b7; border-radius:50%; margin-right:5px; vertical-align:middle; }

        /* Gold button */
        .btn-gold {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #0d0d0f;
            border: none;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            border-radius: 10px !important;
            transition: opacity .2s, transform .15s, box-shadow .2s;
        }
        .btn-gold:hover { opacity:.9; transform:translateY(-1px); box-shadow:0 8px 28px rgba(201,169,110,.3); color:#0d0d0f; }
        .btn-gold:disabled { background:#1f1f28; color:#3a3a46; transform:none; box-shadow:none; opacity:1; }

        /* Stat pills */
        .stat-pill { background:#1a1a1f; border:1px solid #232328; }
        .stat-val  { color: var(--gold); font-size:1.2rem; font-weight:800; line-height:1; }
        .stat-lbl  { color:#4a4a58; font-size:9px; text-transform:uppercase; letter-spacing:1.5px; }

        /* Alert messages */
        .promo-msg         { font-size:13px; font-weight:500; border-radius:10px; animation: slideIn .2s ease; }
        .promo-msg-success { background:rgba(110,231,183,.07); color:#6ee7b7; border:1px solid rgba(110,231,183,.2); }
        .promo-msg-error   { background:rgba(252,165,165,.07); color:#fca5a5; border:1px solid rgba(252,165,165,.2); }

        /* Icon box */
        .icon-box { width:52px; height:52px; background:#1a1a1f; border-radius:14px; font-size:24px; box-shadow:0 4px 18px rgba(0,0,0,.2); flex-shrink:0; }

        /* Card text */
        .card-title-text { color:#f0ede8; font-size:1.25rem; font-weight:800; letter-spacing:-.3px; }
        .card-desc-text  { color:#5a5a68; font-size:13px; line-height:1.6; font-weight:300; }
        .preview-name    { color:#f0ede8; font-size:15px; font-weight:700; }
        .preview-detail  { color:#4a4a58; font-size:12px; font-weight:300; }
        hr.card-hr       { border-color:#1f1f28; opacity:1; }
    </style>
</head>
<body>
<div class="dashboard-layout">
    <?php include 'admin/adminsidebar.php'; ?>

    <div class="main-content flex-grow-1 p-4">

        <!-- Page Header -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="icon-box d-flex align-items-center justify-content-center">🎓</div>
            <div>
                <h1 class="fw-bold fs-4 mb-0 text-dark">Student Promotion</h1>
                <p class="text-secondary small mb-0">Promote students to their next class — individually or as a whole class</p>
            </div>
        </div>

        <!-- Stats Strip -->
        <div class="d-flex flex-wrap gap-3 mb-4">
            <div class="stat-pill rounded-3 px-4 py-3 d-flex align-items-center gap-3">
                <span style="font-size:20px">🏫</span>
                <div>
                    <div class="stat-lbl">Total Classes</div>
                    <div class="stat-val"><?= count($classArr) ?></div>
                </div>
            </div>
            <div class="stat-pill rounded-3 px-4 py-3 d-flex align-items-center gap-3">
                <span style="font-size:20px">⬆️</span>
                <div>
                    <div class="stat-lbl">Bulk Promote</div>
                    <div class="stat-val">Class</div>
                </div>
            </div>
            <div class="stat-pill rounded-3 px-4 py-3 d-flex align-items-center gap-3">
                <span style="font-size:20px">👤</span>
                <div>
                    <div class="stat-lbl">Individual</div>
                    <div class="stat-val">By DAS</div>
                </div>
            </div>
        </div>

        <!-- Cards — Bootstrap row, centered -->
        <div class="row justify-content-center g-4">

            <!-- CARD 1: Promote Whole Class -->
            <div class="col-12 col-md-6 col-xl-5">
                <div class="promo-card rounded-4 p-4 h-100 d-flex flex-column">

                    <span class="gold-badge badge rounded-pill px-3 py-2 mb-3 d-inline-flex align-items-center gap-2 align-self-start">
                        <span class="badge-dot"></span> Batch Promotion
                    </span>

                    <h2 class="card-title-text mb-1">Promote Whole Class</h2>
                    <p class="card-desc-text mb-4">Select a class and shift all its students to the next class in one action.</p>

                    <hr class="card-hr mb-4">

                    <div class="mb-3">
                        <label class="form-label">Current Class</label>
                        <select id="currentClass" class="form-select">
                            <option value="">Select Class</option>
                            <?php foreach($classArr as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['class_name']) ?> — <?= htmlspecialchars($c['class_sec']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-center my-2">
                        <span class="arrow-pill badge rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-down"></i> Promotes to
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Promote To</label>
                        <select id="nextClass" class="form-select">
                            <option value="">Select Next Class</option>
                            <?php foreach($classArr as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['class_name']) ?> — <?= htmlspecialchars($c['class_sec']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn btn-gold w-100 py-3 mt-auto" id="promoteClassBtn" onclick="promoteClass()">
                        🎓 &nbsp;Promote Whole Class
                    </button>
                    <div class="promo-msg d-none p-3 mt-3" id="classMsg"></div>
                </div>
            </div>

            <!-- CARD 2: Promote Individual -->
            <div class="col-12 col-md-6 col-xl-5">
                <div class="promo-card rounded-4 p-4 h-100 d-flex flex-column">

                    <span class="gold-badge badge rounded-pill px-3 py-2 mb-3 d-inline-flex align-items-center gap-2 align-self-start">
                        <span class="badge-dot"></span> Individual Promotion
                    </span>

                    <h2 class="card-title-text mb-1">Promote Individual</h2>
                    <p class="card-desc-text mb-4">Enter a student's DAS number to find and promote them to their next class.</p>

                    <hr class="card-hr mb-4">

                    <div class="mb-3">
                        <label class="form-label">DAS Number</label>
                        <input type="text" id="studentDAS" class="form-control"
                               placeholder="Enter DAS number..."
                               oninput="searchStudentForPromote()">
                    </div>

                    <!-- Student Preview -->
                    <div class="student-preview rounded-3 p-3 mb-3 d-none" id="studentPreview">
                        <div class="found-tag mb-2">Student Found</div>
                        <p class="preview-name mb-1" id="previewName"></p>
                        <p class="preview-detail mb-0" id="previewClass"></p>
                        <p class="preview-detail mb-0" id="previewFather"></p>
                    </div>

                    <div class="d-flex justify-content-center my-2">
                        <span class="arrow-pill badge rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-down"></i> Promotes to
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Promote To</label>
                        <select id="nextClassIndividual" class="form-select">
                            <option value="">Select Next Class</option>
                            <?php foreach($classArr as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['class_name']) ?> — <?= htmlspecialchars($c['class_sec']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn btn-gold w-100 py-3 mt-auto" id="promoteStudentBtn" onclick="promoteStudent()">
                        🎓 &nbsp;Promote Student
                    </button>
                    <div class="promo-msg d-none p-3 mt-3" id="studentMsg"></div>
                </div>
            </div>

        </div><!-- /row -->
    </div><!-- /main-content -->
</div><!-- /dashboard-layout -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
let foundStudentId = null;
let searchTimeout  = null;

function searchStudentForPromote() {
    clearTimeout(searchTimeout);
    const DAS     = document.getElementById('studentDAS').value.trim();
    const preview = document.getElementById('studentPreview');

    if (!DAS) {
        preview.classList.add('d-none');
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
                preview.classList.add('d-none');
                foundStudentId = null;
                return;
            }
            foundStudentId = data.id;
            document.getElementById('previewName').innerText   = data.student_name;
            document.getElementById('previewClass').innerText  = '📚 Class: ' + (data.class_name ?? 'N/A') + ' — ' + (data.class_sec ?? '');
            document.getElementById('previewFather').innerText = '👤 Father: ' + data.father_name;
            preview.classList.remove('d-none');
        })
        .catch(() => {
            preview.classList.add('d-none');
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
            document.getElementById('studentPreview').classList.add('d-none');
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
    el.classList.remove('d-none', 'promo-msg-success', 'promo-msg-error');
    el.classList.add(success ? 'promo-msg-success' : 'promo-msg-error');
    el.innerText = text;
    setTimeout(() => el.classList.add('d-none'), 4000);
}
</script>
</body>
</html>