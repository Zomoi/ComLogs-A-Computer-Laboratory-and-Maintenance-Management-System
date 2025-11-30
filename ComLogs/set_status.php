<?php
include 'config.php';
$id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? 'offline';

if ($id && in_array($status, ['online', 'offline', 'under_maintenance'])) {
    $pdo->prepare("UPDATE computers SET status = ? WHERE id = ?")->execute([$status, $id]);
}
header("Location: index.php");
exit();
?>