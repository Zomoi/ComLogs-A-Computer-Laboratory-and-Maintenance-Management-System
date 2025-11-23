<?php
// Redirect to login if not authenticated
/*if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}*/
include 'config.php';
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo->prepare("DELETE FROM peripherals WHERE id = ?")->execute([$id]);
}
header("Location: index.php");
exit();
?>