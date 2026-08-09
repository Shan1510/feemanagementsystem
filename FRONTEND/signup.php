<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['type'] === 'admin') {
        header("Location: ../BACKEND/admin/admindashboard.php");
    } else {
        header("Location: ../BACKEND/user/userdashboard.php");
    }
    exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="login.css" rel="stylesheet">
</head>
<body>
    <div class="outerbox">
        <div class="inerbox">
            <h1>Sign Up</h1>
            <form action="../BACKEND/signup.php" method="post" autocomplete="off">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="off">
                <br><br>
                <label for="Email">Email</label>
                <input type="text" id="Email" name="Email" placeholder="Enter your email" required autocomplete="off">
                <br><br>
                <label for="Phonenumber">Phone Number</label>
                <input type="text" id="Phonenumber" name="Phonenumber" placeholder="Enter your phone number" required autocomplete="off">
                <br><br>
                <label for="Password">Password</label>
                <input type="password" id="Password" name="Password" placeholder="Enter your password" required>
                <br><br>
                <label for="ConfirmPassword">Confirm Password</label>
                <input type="password" id="ConfirmPassword" name="ConfirmPassword" placeholder="Confirm your password" required>
                <br><br>
                <input type="submit" value="Sign Up">
            </form>
            <a href="index.php">Login</a>
        </div>
    </div>
</body>
</html>