
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection (direct, no config)
$pdo = new PDO("mysql:host=localhost;dbname=comlogs_db;charset=utf8", "root", "");

// Initialize error message variable
$error = '';

if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Query to get user by email
    $stmt = $pdo->prepare("SELECT id, name, role, password FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify password (assuming password hashing is used)
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ComLogs Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #caf0f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-image: url('Images/login_bg.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
        .logo{
            display:block; 
            margin:0 auto 0px; 
            width:240px;
        }
        .login {
            font-family: 'Aharoni', Verdana, sans-serif;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 2px;
            color: #00B4D8
        }
        .box {
            background: rgba(236, 247, 253, 1);
            padding: 20px;
            border-radius: 30px;
            box-shadow: 0px 12px 12px rgba(239, 232, 232, 0.19);
            width: 350px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-sizing: border-box;
        }
        button {
            font-size: 1rem;
            text-align: center;
            width: 30%;
            padding: 15px;
            background: #00B4D8;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            margin: 5px auto 0;      
            display: block;  
        }
        button:hover {
            background: linear-gradient(to left, #0077b6, #03054e);
            transform: scale(1.03);
            box-shadow: 0 4px 10px rgba(2, 136, 209, 0.3);
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
        <img src="images/logo.png" class="logo">
        <h2 class="login">LOG IN</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p style="text-align:center; margin-top:10px; color:#777; font-size:0.8em;">
        Enter your credentials. 
        </p>
    </div>
</body>
</html>
<!--comment-->
