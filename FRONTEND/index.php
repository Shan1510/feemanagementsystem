<?php
session_start();

// Agar already logged in hai toh dashboard pe bhejo
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['type'] === 'admin') {
        header("Location: ../BACKEND/admin/admindashboard.php");
    } else {
        header("Location: ../BACKEND/user/userdashboard.php");
    }
    exit();
}

// Cache band karo
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="login.css" rel="stylesheet">
</head>
<body>
    <div class="outerbox">
        <div class="inerbox">
            <h1>Login</h1>
            <form action="/BACKEND/login.php" method="post" autocomplete="off">
                <label for="Email">Email</label>
                <input type="text" id="Email" name="Email" 
                       value="" placeholder="Enter your email" autocomplete="off">
                       
                <br><br>
                <label for="Password">Password</label>
                <input type="password" id="password" name="password" 
                       placeholder="Enter your password" autocomplete="new-password" value="">
                <br><br>
                <input type="submit" value="Login">
            </form>
            <a href="signup.html">signup</a>
        </div>
    </div>
    <script>
    // Page load hote hi fields clear karo
    window.onload = function() {
        document.getElementById('Email').value = '';
        document.getElementById('password').value = '';
    }
</script>

Clear data
</body>
</html>








<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="login.css" rel="stylesheet">
</head>
<body>
    <div class="outerbox">
    <div class="inerbox">
        <h1>Login</h1>
<form action="../BACKEND/login.php" method="post" name="loginform">
    <label for="Email">Email</label>
    <input type="text" id="Email" name="Email" placeholder="Enter your email">
    <br><br>
    <label for="Password">Password</label>
    <input type="password" id="password" name="password" placeholder="Enter your password">
    <br>
    <br>
    <input type="submit" value="Login">


</form>

<a href="signup.html">signup</a>
    </div>
    </div>
</body>
</html> -->