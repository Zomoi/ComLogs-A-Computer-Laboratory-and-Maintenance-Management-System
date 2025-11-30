<?php
include 'config.php';

$computers = $pdo->query("SELECT id, ip_address, status FROM computers")->fetchAll();

foreach ($computers as $pc) {
    // 🔒 CRITICAL: Skip ping if manually set to "Under Maintenance"
    if ($pc['status'] === 'under_maintenance') {
        continue;
    }

    $ip = $pc['ip_address'];
    $id = $pc['id'];

    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        continue;
    }

    $isWin = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    $ping = $isWin 
        ? "ping -n 1 -w 1000 " . escapeshellarg($ip)
        : "ping -c 1 -W 1 " . escapeshellarg($ip);

    $output = shell_exec($ping . ' 2>&1');
    $online = false;

    if ($output !== null) {
        if ($isWin) {
            $online = strpos($output, 'TTL=') !== false;
        } else {
            $online = strpos($output, 'bytes from') !== false || strpos($output, '1 received') !== false;
        }
    }

    // Only update if NOT under maintenance
    $newStatus = $online ? 'online' : 'offline';
    $pdo->prepare("UPDATE computers SET status = ? WHERE id = ?")->execute([$newStatus, $id]);
}

header("Location: index.php");
exit();
?>