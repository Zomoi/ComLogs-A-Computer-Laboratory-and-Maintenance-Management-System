<?php
// Redirect to login if not authenticated
/*if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}*/
include 'config.php';

$message = '';
$computer = null;

// Get computer by ID
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM computers WHERE id = ?");
    $stmt->execute([$id]);
    $computer = $stmt->fetch();

    if (!$computer) {
        die("Computer not found.");
    }
}

// Handle form submission
if ($_POST) {
    try {
        $id = (int)$_POST['id'];
        $pc_name = trim($_POST['pc_name']);
        $ip = trim($_POST['ip_address']);
        $mac = trim($_POST['mac_address']);
        $location = trim($_POST['location']);
        $status = $_POST['status'];

        if (empty($pc_name) || empty($ip) || empty($mac) || empty($location)) {
            throw new Exception("All fields are required.");
        }

        $stmt = $pdo->prepare("
            UPDATE computers 
            SET pc_name = ?, ip_address = ?, mac_address = ?, location = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([$pc_name, $ip, $mac, $location, $status, $id]);

        $message = "Computer updated successfully!";
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
    <title>Edit Computer - ComLogs</title>
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
        <h2>✎ Edit Computer</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($computer): ?>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $computer['id'] ?>">

            <div class="form-group">
                <label for="pc_name">PC Name *</label>
                <input type="text" id="pc_name" name="pc_name" value="<?= htmlspecialchars($computer['pc_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="ip_address">IP Address *</label>
                <input type="text" id="ip_address" name="ip_address" value="<?= htmlspecialchars($computer['ip_address']) ?>" required>
            </div>

            <div class="form-group">
                <label for="mac_address">MAC Address *</label>
                <input type="text" id="mac_address" name="mac_address" value="<?= htmlspecialchars($computer['mac_address']) ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Location *</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($computer['location']) ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="online" <?= $computer['status'] === 'online' ? 'selected' : '' ?>>Online</option>
                    <option value="offline" <?= $computer['status'] === 'offline' ? 'selected' : '' ?>>Offline</option>
                    <option value="under_maintenance" <?= $computer['status'] === 'under_maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                </select>
            </div>

            <button type="submit">Update Computer</button>
            <button type="button" onclick="history.back()">Cancel</button>
        </form>
        <?php else: ?>
            <p>Computer not found.</p>
            <button onclick="location.href='index.php'">Go Back</button>
        <?php endif; ?>
    </div>
</body>
</html>