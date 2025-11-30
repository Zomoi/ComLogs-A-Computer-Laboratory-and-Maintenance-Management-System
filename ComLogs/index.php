<?php
// Redirect to login if not authenticated
//if (!isset($_SESSION['user_id'])) {
//    header("Location: login.php");
//    exit();
// }
// Load configuration and database connection
include 'config.php';

// Optional: Load and insert sample data (safe to remove later)
if (file_exists('sample_data.php')) {
    include 'sample_data.php';
    insertSampleData($pdo);
}

// Fetch Dashboard data
$stats = $pdo->query("
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'online' THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN status = 'under_maintenance' THEN 1 ELSE 0 END) AS maintenance,
        SUM(CASE WHEN status = 'offline' THEN 1 ELSE 0 END) AS offline
    FROM computers
")->fetch();

$computers = $pdo->query("SELECT * FROM computers ORDER BY pc_name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComLogs: Computer Laboratory Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <img src="images/logo.png" class="logo">
        <nav>
            <ul>
                <li class="active" data-target="dashboard">Dashboard</li>
                <li data-target="device-function">Devices</li>
                <li data-target="computers">Computers</li>
                <li data-target="maintenance-logs">Maintenance Logs</li>
                <li data-target="maintenance-schedule">Maintenance Schedule</li>
                <li data-target="user-management">User Management</li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
      <!-- Dashboard (DYNAMIC) -->
      <section data-page="dashboard" class="page active">
        <h1>Dashboard</h1>
        <div class="stats-grid">
          <div class="card"><div>Total Computers<br><span><?= htmlspecialchars($stats['total']) ?></span></div></div>
          <div class="card"><div>Under Maintenance<br><span><?= htmlspecialchars($stats['maintenance']) ?></span></div></div>
          <div class="card"><div>Active<br><span><?= htmlspecialchars($stats['active']) ?></span></div></div>
          <div class="card"><div>Offline<br><span><?= htmlspecialchars($stats['offline']) ?></span></div></div>
        </div>
        <form method="POST" action="check_status.php" style="margin: 20px 0;">
          <button type="submit" style="
            background: linear-gradient(to right, #4fc3f7, #29b6f6);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(2, 136, 209, 0.2);
            font-size: 1em;
          ">
            🔄 Check PC Status (Ping All)
          </button>
        </form>
        <table>
          <thead>
            <tr><th>PC Name</th><th>IP</th><th>MAC</th><th>Location</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php foreach ($computers as $pc): ?>
            <tr>
              <td><?= htmlspecialchars($pc['pc_name']) ?></td>
              <td><?= htmlspecialchars($pc['ip_address']) ?></td>
              <td><?= htmlspecialchars($pc['mac_address']) ?></td>
              <td><?= htmlspecialchars($pc['location']) ?></td>
              <td>
                <?php
                $statusMap = [
                    'online' => ['class' => 'online', 'text' => 'Online'],
                    'offline' => ['class' => 'offline', 'text' => 'Offline'],
                    'under_maintenance' => ['class' => 'under-maintenance', 'text' => 'Under Maintenance']
                ];
                $status = $statusMap[$pc['status']] ?? ['class' => 'offline', 'text' => 'Unknown'];
                ?>
                <span class="<?= $status['class'] ?>"><?= $status['text'] ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>

      <!-- Device Function Page (DYNAMIC) -->
<section data-page="device-function" class="page">
  <h1>Device Function</h1>
  <p>Manage printers, projectors, routers, and other peripherals.</p>
  <button class="add-peripheral" onclick="location.href='add_peripheral.php'">+ Add Peripheral</button>
  <table>
    <thead>
      <tr><th>Device Name</th><th>Type</th><th>Location</th><th>Status</th><th>Dependent On</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php
      // Fetch peripherals
      $peripherals = $pdo->query("SELECT * FROM peripherals ORDER BY device_name")->fetchAll();
      foreach ($peripherals as $p):
        $statusClass = match($p['status']) {
            'operational' => 'online',
            'offline', 'faulty' => 'offline',
            default => 'offline'
        };
        $statusText = ucfirst($p['status']);
      ?>
      <tr>
        <td><?= htmlspecialchars($p['device_name']) ?></td>
        <td><?= htmlspecialchars($p['type']) ?></td>
        <td><?= htmlspecialchars($p['location']) ?></td>
        <td><span class="<?= $statusClass ?>"><?= $statusText ?></span></td>
        <td><?= htmlspecialchars($p['dependent_on'] ?: 'N/A') ?></td>
        <td>
          <button onclick="location.href='edit_peripheral.php?id=<?= $p['id'] ?>'">Edit</button>
          <button onclick="if(confirm('Delete this peripheral?')) location.href='delete_peripheral.php?id=<?= $p['id'] ?>'">Delete</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

      <!-- Computers Page (DYNAMIC) -->
  <section data-page="computers" class="page">
        <h1>Computers</h1>
        <p>Manage all computer records in the laboratory.</p>
        <button class="add-computer" onclick="location.href='add_computers.php'">+ Add New Computer</button>
    <table>
          <thead>
            <tr><th>PC Name</th><th>IP</th><th>MAC</th><th>Location</th><th>Status</th><th>Actions</th></tr>
          </thead>
      <tbody>
        <?php
          // Fetch all computers (if not already fetched at the top)
          $allComputers = $pdo->query("SELECT * FROM computers ORDER BY pc_name")->fetchAll();
            foreach ($allComputers as $pc):
          $statusMap = [
            'online' => ['class' => 'online', 'text' => 'Online'],
            'offline' => ['class' => 'offline', 'text' => 'Offline'],
            'under_maintenance' => ['class' => 'under-maintenance', 'text' => 'Under Maintenance']
          ];
          $status = $statusMap[$pc['status']] ?? ['class' => 'offline', 'text' => 'Unknown'];
        ?>
          <tr>
            <td><?= htmlspecialchars($pc['pc_name']) ?></td>
            <td><?= htmlspecialchars($pc['ip_address']) ?></td>
            <td><?= htmlspecialchars($pc['mac_address']) ?></td>
            <td><?= htmlspecialchars($pc['location']) ?></td>
            <td><span class="<?= $status['class'] ?>"><?= $status['text'] ?></span></td>
            <td>
              <button onclick="location.href='edit_computer.php?id=<?= $pc['id'] ?>'">Edit</button>
              <button onclick="if(confirm('Delete this computer?')) location.href='delete_computer.php?id=<?= $pc['id'] ?>'">Delete</button>
            </td>
          </tr>
          <?php endforeach; ?>
      </tbody>
    </table>
  </section>
      <!-- Maintenance Logs (DYNAMIC) -->
<section data-page="maintenance-logs" class="page">
  <h1>Maintenance Logs</h1>
  <p>View history of repairs and issues reported.</p>
  <button onclick="location.href='add_log.php'">+ Add New Log</button>
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>PC Name</th>
        <th>Issue</th>
        <th>Technician</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Join logs with computers and users (technicians)
      $logs = $pdo->query("
        SELECT 
          ml.*, 
          c.pc_name, 
          u.name AS technician_name
        FROM maintenance_logs ml
        LEFT JOIN computers c ON ml.computer_id = c.id
        LEFT JOIN users u ON ml.assigned_technician = u.id
        ORDER BY ml.created_at DESC
      ")->fetchAll();

      foreach ($logs as $log):
        $statusText = ucfirst(str_replace('_', ' ', $log['status']));
      ?>
      <tr>
        <td><?= date('Y-m-d', strtotime($log['created_at'])) ?></td>
        <td><?= htmlspecialchars($log['pc_name'] ?? '—') ?></td>
        <td><?= htmlspecialchars($log['issue_description']) ?></td>
        <td><?= htmlspecialchars($log['technician_name'] ?? 'Unassigned') ?></td>
        <td><?= $statusText ?></td>
        <td>
          <button onclick="location.href='edit_log.php?id=<?= $log['id'] ?>'">Edit</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

      <!-- Maintenance Schedule (DYNAMIC) -->
<section data-page="maintenance-schedule" class="page">
  <h1>Maintenance Schedule</h1>
  <p>Upcoming preventive maintenance tasks.</p>
  <button onclick="location.href='add_schedule.php'">+ Add Maintenance Schedule</button>
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Device Type</th>
        <th>Devices</th>
        <th>Type</th>
        <th>Assigned To</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Fetch schedules with assigned user names
      $schedules = $pdo->query("
        SELECT 
          ms.*,
          u.name AS assigned_name
        FROM maintenance_schedules ms
        LEFT JOIN users u ON ms.assigned_to = u.id
        ORDER BY ms.scheduled_date ASC
      ")->fetchAll();

      foreach ($schedules as $s):
        // Decode device IDs to names
        $deviceNames = '—';
        if ($s['device_type'] === 'computer') {
            $ids = explode(',', $s['device_ids']);
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $pdo->prepare("SELECT pc_name FROM computers WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $names = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $deviceNames = implode(', ', $names ?: ['(Deleted PCs)']);
        } elseif ($s['device_type'] === 'peripheral') {
            $ids = explode(',', $s['device_ids']);
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $pdo->prepare("SELECT device_name FROM peripherals WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $names = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $deviceNames = implode(', ', $names ?: ['(Deleted Devices)']);
        }
      ?>
      <tr>
        <td><?= htmlspecialchars($s['scheduled_date']) ?></td>
        <td><?= ucfirst($s['device_type']) ?></td>
        <td><?= htmlspecialchars($deviceNames) ?></td>
        <td><?= htmlspecialchars($s['maintenance_type']) ?></td>
        <td><?= htmlspecialchars($s['assigned_name'] ?? 'Unassigned') ?></td>
        <td>
          <button onclick="location.href='edit_schedule.php?id=<?= $s['id'] ?>'">Edit</button>
          <button onclick="if(confirm('Delete this schedule?')) location.href='delete_schedule.php?id=<?= $s['id'] ?>'">Delete</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

      <!-- User Management -->
      <!-- User Management (DYNAMIC + ADMIN ONLY) -->
<!-- User Management (DYNAMIC + ADMIN ONLY) -->
<section data-page="user-management" class="page">
  <h1>User Management</h1>
  <p>Manage system users and roles.</p>

  <!-- Logout Button (Visible to ALL logged-in users) -->
  <div style="margin-bottom: 20px; padding: 12px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #3498db;">
    <strong>Current User:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?>
    (<em><?= ucfirst(htmlspecialchars($_SESSION['user_role'])) ?></em>)
    <br>
    <button onclick="location.href='logout.php'" 
            style="margin-top: 8px; padding: 6px 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer;">
      🔒 Logout
    </button>
  </div>

  <!-- Admin-only: Add User Button -->
  <?php if ($_SESSION['user_role'] === 'admin'): ?>
    <button onclick="location.href='add_user.php'">+ Add Technician</button>
  <?php endif; ?>

  <!-- Users Table -->
  <table>
    <thead>
      <tr><th>Name</th><th>Role</th><th>Email</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php
      $users = $pdo->query("SELECT * FROM users ORDER BY role, name")->fetchAll();
      foreach ($users as $u):
        $statusClass = $u['status'] === 'active' ? 'online' : 'offline';
        $statusText = ucfirst($u['status']);
      ?>
      <tr>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= ucfirst($u['role']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><span class="<?= $statusClass ?>"><?= $statusText ?></span></td>
        <td>
          <?php if ($_SESSION['user_role'] === 'admin' && $u['role'] !== 'admin'): ?>
            <button onclick="location.href='edit_user.php?id=<?= $u['id'] ?>'">Edit</button>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
    </main>
</div>
<script src="script.js"></script>
</body>
</html>
