<?php
session_start();
session_destroy(); // Ends the session completely
header("Location: login.php"); // Redirect to login page
exit();
?>