<?php
include 'config.php';

$computers = $pdo->query("SELECT id, pc_name FROM computers ORDER BY pc_name")->fetchAll();
$peripherals = $pdo->query("SELECT id, device_name FROM peripherals ORDER BY device_name")->fetchAll();
$users = $pdo->query("SELECT id, name FROM users WHERE role IN ('admin','technician')")->fetchAll();

$message = '';

// Fetch schedule to edit 
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$schedule_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM maintenance_schedules WHERE id = ?");
$stmt->execute([$schedule_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    die("Schedule not found.");
}

if ($_POST) {
    try {
        $device_type = $_POST['device_type'];
        $device_ids = $_POST['device_ids'] ?? [];
        $maintenance_type = trim($_POST['maintenance_type']);
        $scheduled_date = $_POST['scheduled_date'];
        $assigned_to = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;

        if (empty($device_ids) || empty($maintenance_type) || empty($scheduled_date)) {
            throw new Exception("Device(s), Type, and Date are required.");
        }

        // Ensure all IDs are integers (security)
        $device_ids = array_map('intval', array_filter($device_ids));
        if (empty($device_ids)) {
            throw new Exception("Invalid device selection.");
        }

        $device_ids_str = implode(',', $device_ids);

        $stmt = $pdo->prepare("
            UPDATE maintenance_schedules
            SET device_type = ?, device_ids = ?, maintenance_type = ?, scheduled_date = ?, assigned_to = ?
            WHERE id = ?
        ");
        $stmt->execute([$device_type, $device_ids_str, $maintenance_type, $scheduled_date, $assigned_to, $schedule_id]);

        $message = "Schedule updated successfully!";
        header("Refresh: 2; url=index.php");
        exit();
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Maintenance Schedule - ComLogs</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 40px;
        }
        .form-container {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        h2 {
            color: #333;
            margin-bottom: 24px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #444;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            box-sizing: border-box;
        }
        button {
            background: #eaeaea;
            border: none;
            padding: 12px 24px;
            font-size: 1.1em;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #ddd;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .alert.success { background: #d8f8e1; color: #28a745; }
        .alert.error { background: #f2dede; color: #da1616; }
        small {
            display: block;
            color: #666;
            margin-top: 4px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>✎ Edit Maintenance Schedule</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="scheduleForm">
            <div class="form-group">
                <label for="device_type">Device Type *</label>
                <select id="device_type" name="device_type" required>
                    <option value="computer" <?= $schedule['device_type'] === 'computer' ? 'selected' : '' ?>>Computer</option>
                    <option value="peripheral" <?= $schedule['device_type'] === 'peripheral' ? 'selected' : '' ?>>Peripheral</option>
                </select>
                <small>Choose whether to schedule maintenance for computers or other devices.</small>
            </div>

            <div class="form-group">
                <label for="device_ids">Select Devices *</label>
                <select id="device_ids" name="device_ids[]" multiple size="5" required>
                    <!-- Options will be loaded by JavaScript -->
                </select>
                <small>Hold Ctrl (or Cmd) to select multiple devices.</small>
            </div>

            <div class="form-group">
                <label for="maintenance_type">Maintenance Type *</label>
                <input type="text" id="maintenance_type" name="maintenance_type" value="<?= htmlspecialchars($schedule['maintenance_type']) ?>" placeholder="e.g., Hardware Check, System Update" required>
            </div>

            <div class="form-group">
                <label for="scheduled_date">Scheduled Date *</label>
                <input type="date" id="scheduled_date" name="scheduled_date" value="<?= htmlspecialchars($schedule['scheduled_date']) ?>" required>
            </div>

            <div class="form-group">
                <label for="assigned_to">Assigned To</label>
                <select id="assigned_to" name="assigned_to">
                    <option value="">Unassigned</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $schedule['assigned_to'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">Update Schedule</button>
            <button type="button" onclick="history.back()">Cancel</button>
        </form>
    </div>

<script>
        const computers = <?= json_encode($computers) ?>;
        const peripherals = <?= json_encode($peripherals) ?>;
        const selectedDeviceIds = "<?= $schedule['device_ids'] ?>".split(',').map(id => parseInt(id.trim()));

        const deviceTypeSelect = document.getElementById('device_type');
        const deviceIdsSelect = document.getElementById('device_ids');

        function loadDevices() {
            deviceIdsSelect.innerHTML = '';
            const type = deviceTypeSelect.value;
            const list = type === 'computer' ? computers : peripherals;
            const idField = 'id';
            const nameField = type === 'computer' ? 'pc_name' : 'device_name';

            list.forEach(item => {
                const option = document.createElement('option');
                option.value = item[idField];
                option.textContent = item[nameField];
                if (selectedDeviceIds.includes(item[idField])) {
                    option.selected = true;
                }
                deviceIdsSelect.appendChild(option);
            });
        }

        deviceTypeSelect.addEventListener('change', loadDevices);
        // Load initial devices based on current type
        loadDevices();
    </script>
</body>
</html>
