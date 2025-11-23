<?php
// Redirect to login if not authenticated
//if (!isset($_SESSION['user_id'])) {
//    header("Location: login.php");
//    exit();
//}
include 'config.php';

// Fetch computers and users for dropdowns
$computers = $pdo->query("SELECT id, pc_name FROM computers ORDER BY pc_name")->fetchAll();
$technicians = $pdo->query("SELECT id, name FROM users WHERE role = 'technician' OR role = 'admin'")->fetchAll();

$message = '';

if ($_POST) {
    try {
        $computer_id = (int)$_POST['computer_id'];
        $issue = trim($_POST['issue_description']);
        $technician_id = !empty($_POST['assigned_technician']) ? (int)$_POST['assigned_technician'] : null;
        $status = $_POST['status'];

        if (empty($computer_id) || empty($issue)) {
            throw new Exception("Computer and Issue are required.");
        }

        $stmt = $pdo->prepare("
            INSERT INTO maintenance_logs (computer_id, issue_description, reported_by, assigned_technician, status)
            VALUES (?, ?, 1, ?, ?)
        ");
        // Note: `reported_by = 1` is temporary (will be real user ID after login)
        $stmt->execute([$computer_id, $issue, $technician_id, $status]);

        $message = "Maintenance log added successfully!";
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
    <title>Add Maintenance Log - ComLogs</title>
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
        <h2>+ Add Maintenance Log</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="computer_id">Computer *</label>
                <select id="computer_id" name="computer_id" required>
                    <option value="">-- Select a Computer --</option>
                    <?php foreach ($computers as $pc): ?>
                        <option value="<?= $pc['id'] ?>"><?= htmlspecialchars($pc['pc_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="assigned_technician">Assigned Technician</label>
                <select id="assigned_technician" name="assigned_technician">
                    <option value="">Unassigned</option>
                    <?php foreach ($technicians as $tech): ?>
                        <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="issue_description">Issue Description *</label>
                <textarea id="issue_description" name="issue_description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>

            <button type="submit">Save Log</button>
            <button type="button" onclick="history.back()">Cancel</button>
        </form>
    </div>
</body>
</html>