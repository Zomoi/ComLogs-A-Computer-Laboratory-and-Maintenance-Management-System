<?php
// Redirect to login if not authenticated
//if (!isset($_SESSION['user_id'])) {
//    header("Location: login.php");
//    exit();
//}
include 'config.php';

$message = '';

if ($_POST) {
    try {
        $device_name = trim($_POST['device_name']);
        $type = trim($_POST['type']);
        $location = trim($_POST['location']);
        $status = $_POST['status'];
        $dependent_on = trim($_POST['dependent_on']) ?: null;

        if (empty($device_name) || empty($type)) {
            throw new Exception("Device Name and Type are required.");
        }

        $stmt = $pdo->prepare("
            INSERT INTO peripherals (device_name, type, location, status, dependent_on)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$device_name, $type, $location, $status, $dependent_on]);

        $message = "Peripheral added successfully!";
        header("Refresh: 2; url=index.php");
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
    <title>Add Peripheral - ComLogs</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 40px;
        }
        .form-container {
            max-width: 600px;
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
    </style>
</head>
<body>
    <div class="form-container">
        <h2>+ Add New Peripheral</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="device_name">Device Name *</label>
                <input type="text" id="device_name" name="device_name" required>
            </div>

            <div class="form-group">
                <label for="type">Type *</label>
                <input type="text" id="type" name="type" placeholder="e.g., Printer, Projector" required>
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="e.g., Lab 1">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="operational">Operational</option>
                    <option value="offline">Offline</option>
                    <option value="faulty">Faulty</option>
                </select>
            </div>

            <div class="form-group">
                <label for="dependent_on">Dependent On (Optional)</label>
                <input type="text" id="dependent_on" name="dependent_on" placeholder="e.g., PC-01">
                <small style="color:#666;">Leave blank if independent</small>
            </div>

            <button type="submit">Save Peripheral</button>
            <button type="button" onclick="history.back()">Cancel</button>
        </form>
    </div>
</body>
</html>