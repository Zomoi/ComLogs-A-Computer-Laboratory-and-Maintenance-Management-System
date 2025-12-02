<?php
// Redirect to login if not authenticated
/*if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}*/
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];
$log = $pdo->prepare("SELECT * FROM maintenance_logs WHERE id = ?");
$log->execute([$id]);
$log = $log->fetch();

if (!$log) {
    die("Log not found.");
}

$computers = $pdo->query("SELECT id, pc_name FROM computers")->fetchAll();
$technicians = $pdo->query("SELECT id, name FROM users WHERE role IN ('admin','technician')")->fetchAll();

if ($_POST) {
    $computer_id = (int)$_POST['computer_id'];
    $issue = trim($_POST['issue_description']);
    $tech_id = !empty($_POST['assigned_technician']) ? (int)$_POST['assigned_technician'] : null;
    $status = $_POST['status'];

    $pdo->prepare("
        UPDATE maintenance_logs 
        SET computer_id = ?, issue_description = ?, assigned_technician = ?, status = ?
        WHERE id = ?
    ")->execute([$computer_id, $issue, $tech_id, $status, $id]);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Maintenance Log - ComLogs</title>
    <link rel="stylesheet" href="style.css?v=1">
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
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 1.1em;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
            margin-right: 10px;
        }
        button:hover {
            background: #2980b9;
        }
        button[type="button"] {
            background: #eaeaea;
            color: #333;
        }
        button[type="button"]:hover {
            background: #ddd;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .alert.error { background: #f2dede; color: #c0392b; }
        .alert.success { background: #d8f8e1; color: #27ae60; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>✎ Edit Maintenance Log</h2>

        <form method="POST">
            <div class="form-group">
                <label>Computer *</label>
                <select name="computer_id" required>
                    <?php foreach ($computers as $pc): ?>
                        <option value="<?= $pc['id'] ?>" <?= $pc['id'] == $log['computer_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pc['pc_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Technician</label>
                <select name="assigned_technician">
                    <option value="">Unassigned</option>
                    <?php foreach ($technicians as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= $t['id'] == $log['assigned_technician'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Issue Description *</label>
                <textarea name="issue_description" required><?= htmlspecialchars($log['issue_description']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="pending" <?= $log['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="in_progress" <?= $log['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="resolved" <?= $log['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>
            </div>

            <button type="submit">Update Log</button>
            <button type="button" onclick="history.back()">Cancel</button>
        </form>
    </div>
</body>
</html>
