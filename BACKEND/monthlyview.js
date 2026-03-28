let currentClassId = null;
let currentMonth   = null;
let currentYear    = null;

const monthNames = {
    1:'January',  2:'February', 3:'March',     4:'April',
    5:'May',      6:'June',     7:'July',       8:'August',
    9:'September',10:'October', 11:'November',  12:'December'
};

function loadSections() {
    let cls    = document.getElementById('f-class').value;
    let secSel = document.getElementById('f-sec');
    secSel.innerHTML = '<option value="">Select Section</option>';
    secSel.disabled  = true;
    checkFilters();
    if (!cls) return;

    fetch(BASE_URL + 'fetchclass.php?class_name=' + encodeURIComponent(cls))
    .then(r => r.json())
    .then(data => {
        data.forEach(s => {
            secSel.innerHTML += `<option value="${s.class_sec}">${s.class_sec}</option>`;
        });
        secSel.disabled = false;
    });
}

function checkFilters() {
    let m = document.getElementById('f-month').value;
    let y = document.getElementById('f-year').value;
    let c = document.getElementById('f-class').value;
    let s = document.getElementById('f-sec').value;
    document.getElementById('fetchBtn').disabled = !(m && y && c && s);
}

function fetchStudents() {
    let month = document.getElementById('f-month').value;
    let year  = document.getElementById('f-year').value;
    let cls   = document.getElementById('f-class').value;
    let sec   = document.getElementById('f-sec').value;

    currentMonth = month;
    currentYear  = year;

    document.getElementById('resultTitle').innerText =
        cls + ' - ' + sec + ' | ' + monthNames[month] + ' ' + year;

    document.getElementById('resultHeader').style.display = 'block';
    document.getElementById('tableWrap').style.display    = 'block';
    document.getElementById('tableBody').innerHTML        = '<div class="loading">⏳ Loading...</div>';
    document.getElementById('saveWrap').style.display     = 'none';

    fetch(BASE_URL + 'fetchclass.php?class_name=' + encodeURIComponent(cls) +
          '&sec=' + encodeURIComponent(sec) +
          '&month=' + month + '&year=' + year + '&students=1')
    .then(r => r.json())
    .then(data => {
        currentClassId = data.class_id;
        let students   = data.students;

        if (students.length === 0) {
            document.getElementById('tableBody').innerHTML = '<div class="loading">No students found.</div>';
            return;
        }

        let paid   = students.filter(s => s.status === 'paid').length;
        let unpaid = students.length - paid;
        document.getElementById('paidCount').innerText   = 'Paid: '   + paid;
        document.getElementById('unpaidCount').innerText = 'Unpaid: ' + unpaid;

        let html = `<table><thead><tr>
            <th>#</th><th>DAS</th><th>Student Name</th>
            <th>Father Name</th><th>Contact</th><th>Total Fee</th><th>Fee Status</th>
        </tr></thead><tbody>`;

        students.forEach((s, i) => {
            let pc = s.status === 'paid'   ? 'checked' : '';
            let uc = s.status === 'unpaid' ? 'checked' : '';
            html += `<tr>
                <td>${i+1}</td>
                <td>${s.DAS}</td>
                <td>${s.student_name}</td>
                <td>${s.father_name}</td>
                <td>${s.contact_number}</td>
                <td>Rs. ${s.T_Fee}</td>
                <td>
                    <div class="status-toggle">
                        <input type="radio" class="paid-radio"
                               name="status[${s.id}]" id="paid_${s.id}"
                               value="paid" ${pc} onchange="updateBadges()">
                        <label for="paid_${s.id}" class="paid-label">✅ Paid</label>
                        <input type="radio" class="unpaid-radio"
                               name="status[${s.id}]" id="unpaid_${s.id}"
                               value="unpaid" ${uc} onchange="updateBadges()">
                        <label for="unpaid_${s.id}" class="unpaid-label">❌ Unpaid</label>
                    </div>
                </td>
            </tr>`;
        });

        html += '</tbody></table>';
        document.getElementById('tableBody').innerHTML    = html;
        document.getElementById('saveWrap').style.display = 'flex';
    })
    .catch(() => {
        document.getElementById('tableBody').innerHTML = '<div class="loading">❌ Something went wrong!</div>';
    });
}

function updateBadges() {
    let paid   = document.querySelectorAll('input.paid-radio:checked').length;
    let unpaid = document.querySelectorAll('input.unpaid-radio:checked').length;
    document.getElementById('paidCount').innerText   = 'Paid: '   + paid;
    document.getElementById('unpaidCount').innerText = 'Unpaid: ' + unpaid;
}

function saveStatus() {
    let radios   = document.querySelectorAll('input[type="radio"]:checked');
    let statuses = {};
    let btn      = document.getElementById('saveBtn');

    radios.forEach(r => {
        let match = r.name.match(/status\[(\d+)\]/);
        if (match) statuses[match[1]] = r.value;
    });

    if (Object.keys(statuses).length === 0) {
        showMsg('No data to save!', false);
        return;
    }

    let body = `month=${currentMonth}&year=${currentYear}&class_id=${currentClassId}`;
    for (let id in statuses) {
        body += `&status[${id}]=${statuses[id]}`;
    }

    btn.disabled  = true;
    btn.innerText = '⏳ Saving...';

    fetch(BASE_URL + 'updatefeestatus.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(r => r.text())
    .then(() => {
        showMsg('✅ Fee status saved successfully!', true);
        btn.disabled  = false;
        btn.innerText = '💾 Save Fee Status';
    })
    .catch(() => {
        showMsg('❌ Something went wrong!', false);
        btn.disabled  = false;
        btn.innerText = '💾 Save Fee Status';
    });
}

function showMsg(text, success) {
    let msg       = document.getElementById('saveMsg');
    msg.style.display = 'block';
    msg.className     = success ? 'msg-success' : 'msg-error';
    msg.innerText     = text;
    setTimeout(() => { msg.style.display = 'none'; }, 3000);
}