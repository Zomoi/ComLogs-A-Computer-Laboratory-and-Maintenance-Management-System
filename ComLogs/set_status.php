<?php
include 'config.php';

$id = $_GET['id'] ?? null;
$action = $_GET['status'] ?? '';

if (!$id) {
    header("Location: index.php");
    exit();
}

if ($action === 'under_maintenance') {
    // Technician starts maintenance → lock status
    $pdo->prepare("UPDATE computers SET status = 'under_maintenance' WHERE id = ?")->execute([$id]);
} 
elseif ($action === 'online') {
    // Technician marks as repaired → now subject to ping
    // But don't assume it's online—set to 'offline' and let ping decide
    $pdo->prepare("UPDATE computers SET status = 'offline' WHERE id = ?")->execute([$id]);
}

header("Location: index.php");
exit();
?>