<?php
// SECRET KEY — sirf tum jaante ho
define('RECOVERY_KEY', 'rooshan');

include __DIR__ . '../../Master/conection.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $key      = $_POST['secret_key']   ?? '';
    $email    = trim($_POST['email']   ?? '');
    $password = $_POST['new_password'] ?? '';

    // Secret key check karo
    if ($key !== RECOVERY_KEY) {
        $message = '❌ Invalid secret key!';
    } elseif (!$email || !$password) {
        $message = '❌ All fields required!';
    } else {
        // Password update karo
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt   = $conn->prepare("UPDATE signup SET Password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed, $email);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $success = true;
            $message = '✅ Password updated! You can now login.';
        } else {
            $message = '❌ Email not found in database!';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recovery</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:#f1f5f9; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .box { background:white; padding:35px; border-radius:14px; width:400px; box-shadow:0 10px 30px rgba(0,0,0,0.1); }
        h2 { font-size:20px; color:#0f172a; margin-bottom:5px; }
        p  { font-size:13px; color:#64748b; margin-bottom:25px; }
        label { font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:5px; }
        input { width:100%; padding:10px 12px; border-radius:8px; border:2px solid #e2e8f0; font-size:14px; outline:none; margin-bottom:15px; }
        input:focus { border-color:#6366f1; }
        button { width:100%; padding:12px; background:#1e293b; color:white; border:none; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; }
        .msg { padding:12px 15px; border-radius:8px; font-size:14px; font-weight:600; margin-bottom:15px; }
        .msg-success { background:#d1fae5; color:#065f46; }
        .msg-error   { background:#fee2e2; color:#991b1b; }
        .login-link  { display:block; text-align:center; margin-top:15px; color:#6366f1; font-size:14px; text-decoration:none; }
    </style>
</head>
<body>
<div class="box">
    <h2>🔑 Emergency Recovery</h2>
    <p>Reset admin password</p>

    <?php if ($message): ?>
        <div class="msg <?= $success ? 'msg-success' : 'msg-error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST">
        <label>Secret Key</label>
        <input type="password" name="secret_key" placeholder="Enter secret key" required>

        <label>Admin Email</label>
        <input type="email" name="email" placeholder="admin@school.com" required>

        <label>New Password</label>
        <input type="password" name="new_password" placeholder="New password" required>

        <button type="submit">Reset Password</button>
    
    </form>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>../FRONTEND/index.php" class="login-link">← Back to Login</a>
</div>
</body>
</html>
```