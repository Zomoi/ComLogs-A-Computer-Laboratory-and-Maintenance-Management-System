<?php
// login.php — TEMPORARY: Bypass password check for testing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection (direct, no config)
$pdo = new PDO("mysql:host=localhost;dbname=comlogs_db;charset=utf8", "root", "");

$error = '';

if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    
    if ($email === 'admin@batstate-u.edu.ph') {
        // ✅ FOR TESTING: Skip password check
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Admin User';
        $_SESSION['user_role'] = 'admin';
        header("Location: index.php");
        exit();
    } else {
        $error = "Only admin@batstate-u.edu.ph is allowed for testing.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ComLogs Login (Testing Mode)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 350px;
        }
        .box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .error {
            color: #e74c3c;
            background: #fdf2f2;
            padding: 8px;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>ComLogs Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" value="admin@batstate-u.edu.ph" required>
            <input type="password" name="password" placeholder="Any password works" required>
            <button type="submit">Login</button>
        </form>
        <p style="text-align:center; margin-top:15px; color:#777; font-size:0.9em;">
        <strong>Enter your credentials. </strong>  
        </p>
    </div>
</body>
</html>