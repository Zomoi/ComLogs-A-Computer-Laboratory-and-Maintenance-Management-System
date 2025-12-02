<?php
session_start();
include 'config.php';

// Only Admin can access this
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied.");
}

// Fetch user to edit
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$user_id = (int)$_GET['id'];

// Prevent editing the main admin (optional safety) 
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'technician'");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("Technician not found or access denied.");
}

$message = '';

if ($_POST) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (empty($name) || empty($email)) {
        $message = "Name and email are required.";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE users
                SET name = ?, email = ?
                WHERE id = ? AND role = 'technician'
            ");
            $stmt->execute([$name, $email, $user_id]);
            $message = "Technician updated successfully!";
        } catch (Exception $e) {
            $message = "Error: Email may already be in use.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Technician - ComLogs</title>
    <link rel="stylesheet" href="style.css?v=1">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 40px;
        }
        .form-container {
            max-width: 500px;
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
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 1.1em;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #2980b9;
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
        <h2>✎ Edit Technician</h2>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            

            <button type="submit">Update Technician</button>
            <button type="button" onclick="history.back()">Cancel</button>
        </form>
    </div>
</body>
</html>