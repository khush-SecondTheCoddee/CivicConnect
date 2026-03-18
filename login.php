<?php
require_once 'config.php';
session_start();

$error = "";

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $users = readFromJson(USERS_JSON);
    $authenticated = false;

    foreach ($users as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            // Set Session Variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_phone'] = $user['phone'];
            
            $authenticated = true;
            break;
        }
    }

    if ($authenticated) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | CivicConnect</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { color: #003366; margin-bottom: 20px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #003366; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn:hover { background: #002244; }
        .error { color: #d93025; background: #fde7e9; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; text-align: center; }
        .footer-links { margin-top: 20px; text-align: center; font-size: 14px; }
        a { color: #003366; text-decoration: none; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Login to Portal</h2>
    
    <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn">Login</button>
    </form>

    <div class="footer-links">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <p><a href="index.php">← Back to Home</a></p>
    </div>
</div>

</body>
</html>