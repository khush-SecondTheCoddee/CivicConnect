<?php
require_once 'config.php';
session_start();

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $users = readFromJson(USERS_JSON);

    // Check if email already exists
    $exists = false;
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $exists = true;
            break;
        }
    }

    if ($exists) {
        $error = "Email already registered!";
    } else {
        $newUser = [
            "user_id" => time(),
            "name"    => $name,
            "email"   => $email,
            "phone"   => $phone,
            "password"=> $pass,
            "joined"  => date("Y-m-d")
        ];

        // Save using the helper function in config.php
        saveToJson(USERS_JSON, $newUser);
        
        $_SESSION['msg'] = "Registration successful! Please login.";
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register | CivicConnect</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { background: white; padding: 30px; border-radius: 10px; shadow: 0 4px 6px rgba(0,0,0,0.1); width: 350px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn { width: 100%; padding: 10px; background: #003366; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Create Account</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Mobile Number" required>
            <input type="password" name="password" placeholder="Create Password" required>
            <button type="submit" class="btn">Register</button>
        </form>
        <p style="text-align:center">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>