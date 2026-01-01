<?php

session_start();

session_unset();

session_destroy();


header("Location: ../../FRONTEND/login.html");
exit();
?>


