<?php
// Dev helper: generates a bcrypt hash for a password you pass in.
// Usage: hash.php?password=YourPasswordHere
$pwtxt = $_GET['password'] ?? '';
if ($pwtxt !== '') {
    echo password_hash($pwtxt, PASSWORD_BCRYPT);
} else {
    echo "Usage: hash.php?password=YourPasswordHere";
}
?>