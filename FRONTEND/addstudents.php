<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="../BACKEND/admin/adminsidebar.css">
    <link rel="stylesheet" href="addstudents.css">
</head>
<body>

<div class="dashboard-layout">

    <?php include __DIR__ . '/../BACKEND/admin/adminsidebar.php'; ?>

    <main class="main-content">
        <div class="card form-wrapper">
            <h2>👨‍🎓 Add new student</h2>

            <form id="addStudentForm" method="post">

                <div class="form-field">
                    <label for="DAS">DAS</label>
                    <input type="text" id="DAS" name="DAS" placeholder="Enter the DAS" required>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="Studentname">Student name</label>
                        <input type="text" id="Studentname" name="Studentname" placeholder="Enter student name" required>
                    </div>
                    <div class="form-field">
                        <label for="Fathername">Father name</label>
                        <input type="text" id="Fathername" name="Fathername" placeholder="Enter father name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="Contactnumber">Contact number</label>
                        <input type="text" id="Contactnumber" name="Contactnumber" placeholder="Enter contact number" required>
                    </div>
                    <div class="form-field">
                        <label for="T_fee">Tuition fee</label>
                        <input type="text" id="T_fee" name="T_fee" placeholder="Enter fee amount" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label for="Class">Class</label>
                        <input type="text" id="Class" name="Class" placeholder="Enter class" required>
                    </div>
                    <div class="form-field">
                        <label for="Section">Section</label>
                        <input type="text" id="Section" name="Section" placeholder="Enter section" required>
                    </div>
                </div>

                <div id="responseMsg"></div>

                <input type="submit" value="Save student">

            </form>
        </div>
    </main>

</div>

<script>
    document.getElementById('addStudentForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const msgBox = document.getElementById('responseMsg');

        msgBox.textContent = '';
        msgBox.className = '';

        fetch('../BACKEND/addstudent.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            msgBox.textContent = data.message;
            msgBox.className = data.success ? 'msg-success' : 'msg-error';
            if (data.success) form.reset();
        })
        .catch(() => {
            msgBox.textContent = 'Something went wrong. Please try again.';
            msgBox.className = 'msg-error';
        });
    });
</script>

</body>
</html>