<?php
include 'config.php';

// Get all computers
$computers = $pdo->query("SELECT id, ip_address FROM computers")->fetchAll();

foreach ($computers as $pc) {
    $ip = $pc['ip_address'];
    $id = $pc['id'];

    // Validate IP
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        continue;
    }

    // Build ping command (Windows vs Linux)
    $isWin = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    $ping = $isWin 
        ? "ping -n 1 -w 1000 " . escapeshellarg($ip)
        : "ping -c 1 -W 1 " . escapeshellarg($ip);

    // Execute ping
    $output = shell_exec($ping . ' 2>&1');
    $online = false;

    if ($output !== null) {
        if ($isWin) {
            $online = strpos($output, 'TTL=') !== false;
        } else {
            $online = strpos($output, 'bytes from') !== false || strpos($output, '1 received') !== false;
        }
    }

    // Update status
    $status = $online ? 'online' : 'offline';
    $pdo->prepare("UPDATE computers SET status = ? WHERE id = ?")->execute([$status, $id]);
}

// Go back to index
header("Location: index.php");
exit();
?>