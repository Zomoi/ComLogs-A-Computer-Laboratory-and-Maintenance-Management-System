<?php
require 'config.php';
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Admin';
$_SESSION['user_role'] = 'admin';
header("Location: index.php");
exit();
?>