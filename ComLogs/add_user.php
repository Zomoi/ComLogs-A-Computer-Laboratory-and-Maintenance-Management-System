<?php
session_start();
include 'config.php';

// Only admin can access
if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied.");
}

$message = '';

if ($_POST) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = 'technician'; // Only technicians can be added

    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } else {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, 'active')")
                ->execute([$name, $email, $hashed, $role]);
            $message = "Technician account created successfully!";
        } catch (Exception $e) {
            $message = "Error: Email may already exist.";
        }
    }
}
?>

<!-- Same styling as other forms -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add User - ComLogs</title>
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
        h2 { color: #333; margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
        button { background: #3498db; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .alert { padding: 12px; margin: 12px 0; border-radius: 6px; }
        .alert.error { background: #f2dede; color: #c0392b; }
        .alert.success { background: #d8f8e1; color: #27ae60; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>+ Add Technician Account</h2>
        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required minlength="6">
                <small>Password must be at least 6 characters.</small>
            </div>
            <button type="submit">Create Account</button>
            <button type="button" onclick="history.back()">Cancel</button>
        </form>
    </div>
</body>
</html>