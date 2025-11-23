<?php
// sample_data.php — ONLY for development/testing

function insertSampleData($pdo) {
    $hasUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($hasUsers == 0) {
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)")
            ->execute(['Admin User', 'admin@batstate-u.edu.ph', $adminPass, 'admin']);

        $pdo->exec("INSERT INTO computers (pc_name, ip_address, mac_address, location, status) VALUES 
            ('PC-01', '192.168.1.10', '00:1A:2B:3C:4D:5E', 'Lab 1', 'online'),
            ('PC-02', '192.168.1.11', '00:1A:2B:3C:4D:5F', 'Lab 2', 'offline'),
            ('PC-03', '192.168.1.12', '00:1A:2B:3C:4D:60', 'Lab 1', 'under_maintenance')
        ");

        $pdo->exec("INSERT INTO peripherals (device_name, type, location, status, dependent_on) VALUES 
            ('Printer-Lab1', 'Printer', 'Lab 1', 'operational', 'PC-01'),
            ('Projector-A', 'Projector', 'Lecture Room', 'offline', NULL)
        ");
    }
}
?>