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
    <link href="admin/style.css" rel="stylesheet">
    <link href="admin/admin.css" rel="stylesheet">
    <style>
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .promo-card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }

        .card-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .card-desc {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
        }

        .form-group label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            font-size: 14px;
            color: #334155;
            outline: none;
            transition: border 0.2s;
        }

        .form-group select:focus,
        .form-group input:focus {
            border-color: #6366f1;
        }

        .btn-promote {
            width: 100%;
            padding: 12px;
            background: #1e293b;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: 0.2s;
        }

        .btn-promote:hover     { background: #334155; }
        .btn-promote:disabled  { background: #cbd5e1; cursor: not-allowed; }

        .msg {
            display: none;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 12px;
        }

        .msg-success { background: #d1fae5; color: #065f46; }
        .msg-error   { background: #fee2e2; color: #991b1b; }

        /* Student preview */
        .student-preview {
            display: none;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .preview-name    { font-size: 15px; font-weight: 600; color: #0f172a; margin: 0 0 4px 0; }
        .preview-detail  { font-size: 13px; color: #64748b; margin: 2px 0; }

        @media (max-width: 768px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="dashboard-layout">

    <?php include __DIR__ . '/admin/adminsidebar.php'; ?>

    <div class="main-content">

        <h2 style="font-size:1.8rem; font-weight:700; color:#0f172a; margin-bottom:25px;">🎓 Student Promotion</h2>

        <div class="grid">

            <!-- CARD 1: Promote Whole Class -->
            <div class="promo-card">
                <p class="card-label">Promote Class</p>
                <p class="card-desc">Poori class ko next class mein shift karo</p>

                <div class="form-group">
                    <label>Current Class</label>
                    <select id="currentClass">
                        <option value="">Select Class</option>
                        <?php foreach($classArr as $c): ?>
                            <option value="<?= $c['id'] ?>">
                                <?= htmlspecialchars($c['class_name']) ?> - <?= htmlspecialchars($c['class_sec']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Promote To</label>
                    <select id="nextClass">
                        <option value="">Select Next Class</option>
                        <?php foreach($classArr as $c): ?>
                            <option value="<?= $c['id'] ?>">
                                <?= htmlspecialchars($c['class_name']) ?> - <?= htmlspecialchars($c['class_sec']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="btn-promote" id="promoteClassBtn" onclick="promoteClass()">
                    🎓 Promote Whole Class
                </button>
                <div class="msg" id="classMsg"></div>
            </div>

            <!-- CARD 2: Promote Individual -->
            <div class="promo-card">
                <p class="card-label">Promote Individual</p>
                <p class="card-desc">Ek student ko next class mein shift karo</p>

                <div class="form-group">
                    <label>DAS Number</label>
                    <input type="text" id="studentDAS"
                           placeholder="Enter DAS number..."
                           oninput="searchStudentForPromote()">
                </div>

                <!-- Student Preview -->
                <div class="student-preview" id="studentPreview">
                    <p class="preview-name"  id="previewName"></p>
                    <p class="preview-detail" id="previewClass"></p>
                    <p class="preview-detail" id="previewFather"></p>
                </div>

                <div class="form-group">
                    <label>Promote To</label>
                    <select id="nextClassIndividual">
                        <option value="">Select Next Class</option>
                        <?php foreach($classArr as $c): ?>
                            <option value="<?= $c['id'] ?>">
                                <?= htmlspecialchars($c['class_name']) ?> - <?= htmlspecialchars($c['class_sec']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button class="btn-promote" id="promoteStudentBtn" onclick="promoteStudent()">
                    🎓 Promote Student
                </button>
                <div class="msg" id="studentMsg"></div>
            </div>

        </div>
    </div>
</div>

<script>
let foundStudentId = null;
let searchTimeout  = null;

// DAS type hone pe student search karo
function searchStudentForPromote() {
    clearTimeout(searchTimeout);
    let DAS     = document.getElementById('studentDAS').value.trim();
    let preview = document.getElementById('studentPreview');

    if (!DAS) {
        preview.style.display = 'none';
        foundStudentId        = null;
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
                foundStudentId        = null;
                return;
            }
            foundStudentId = data.id;
            document.getElementById('previewName').innerText   = data.student_name;
            document.getElementById('previewClass').innerText  = 'Class: ' + (data.class_name ?? 'N/A') + ' - ' + (data.class_sec ?? '');
            document.getElementById('previewFather').innerText = 'Father: ' + data.father_name;
            preview.style.display = 'block';
        })
        .catch(() => {
            preview.style.display = 'none';
            foundStudentId        = null;
        });
    }, 500);
}

// Promote whole class
function promoteClass() {
    let currentClass = document.getElementById('currentClass').value;
    let nextClass    = document.getElementById('nextClass').value;
    let btn          = document.getElementById('promoteClassBtn');
    let msg          = document.getElementById('classMsg');

    if (!currentClass || !nextClass) {
        showMsg(msg, '❌ Please select both classes!', false);
        return;
    }

    if (currentClass === nextClass) {
        showMsg(msg, '❌ Current and next class cannot be same!', false);
        return;
    }

    if (!confirm('Are you sure? All students of this class will be promoted!')) return;

    btn.disabled  = true;
    btn.innerText = '⏳ Promoting...';

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
        btn.innerText = '🎓 Promote Whole Class';
    })
    .catch(() => {
        showMsg(msg, '❌ Something went wrong!', false);
        btn.disabled  = false;
        btn.innerText = '🎓 Promote Whole Class';
    });
}

// Promote individual student
function promoteStudent() {
    let nextClass = document.getElementById('nextClassIndividual').value;
    let btn       = document.getElementById('promoteStudentBtn');
    let msg       = document.getElementById('studentMsg');

    if (!foundStudentId) {
        showMsg(msg, '❌ Please enter a valid DAS number!', false);
        return;
    }

    if (!nextClass) {
        showMsg(msg, '❌ Please select next class!', false);
        return;
    }

    if (!confirm('Are you sure you want to promote this student?')) return;

    btn.disabled  = true;
    btn.innerText = '⏳ Promoting...';

    fetch('<?= BASE_URL ?>promoteaction.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=individual&student_id=${foundStudentId}&next_class_id=${nextClass}`
    })
    .then(r => r.json())
    .then(data => {
        showMsg(msg, data.success ? '✅ ' + data.message : '❌ ' + data.message, data.success);
        if (data.success) {
            document.getElementById('studentDAS').value           = '';
            document.getElementById('nextClassIndividual').value  = '';
            document.getElementById('studentPreview').style.display = 'none';
            foundStudentId = null;
        }
        btn.disabled  = false;
        btn.innerText = '🎓 Promote Student';
    })
    .catch(() => {
        showMsg(msg, '❌ Something went wrong!', false);
        btn.disabled  = false;
        btn.innerText = '🎓 Promote Student';
    });
}

function showMsg(el, text, success) {
    el.style.display = 'block';
    el.className     = 'msg ' + (success ? 'msg-success' : 'msg-error');
    el.innerText     = text;
    setTimeout(() => { el.style.display = 'none'; }, 4000);
}
</script>

</body>
</html>