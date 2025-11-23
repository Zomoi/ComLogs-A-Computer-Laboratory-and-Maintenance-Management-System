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
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Log</title>
    <style>
        body { font-family: Arial, sans-serif; 
            padding: 40px; 
            background: #f7f7f7; 
        }
        .form { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        select, textarea { width: 100%; padding: 8px; margin: 6px 0; }
        button { padding: 10px 20px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="form">
        <h2>Edit Maintenance Log</h2>
        <form method="POST">
            <label>Computer</label>
            <select name="computer_id" required>
                <?php foreach ($computers as $pc): ?>
                    <option value="<?= $pc['id'] ?>" <?= $pc['id'] == $log['computer_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pc['pc_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Technician</label>
            <select name="assigned_technician">
                <option value="">Unassigned</option>
                <?php foreach ($technicians as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $t['id'] == $log['assigned_technician'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Issue</label>
            <textarea name="issue_description" required><?= htmlspecialchars($log['issue_description']) ?></textarea>

            <label>Status</label>
            <select name="status">
                <option value="pending" <?= $log['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="in_progress" <?= $log['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="resolved" <?= $log['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
            </select>

            <button type="submit">Update Log</button>
            <button type="button" onclick="history.back()">Cancel</button>
        </form>
    </div>
</body>
</html>