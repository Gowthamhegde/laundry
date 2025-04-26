<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simple brute force protection
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
    
    if ($_SESSION['login_attempts'] < 5) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if ($username === 'admin' && $password === '1234') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['login_attempts'] = 0;
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['login_attempts']++;
            $error = "Invalid login credentials. Attempts remaining: " . (5 - $_SESSION['login_attempts']);
        }
    } else {
        $error = "Too many failed attempts. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Laundry Tech</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .login-box {
            background: white;
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .login-box h2 {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .login-box input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .login-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        
        .login-box button {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }
        
        .login-box button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .login-box button:active {
            transform: translateY(0);
        }
        
        .error-message {
            color: var(--error-color);
            margin-bottom: 1rem;
            font-size: 14px;
        }
        
        .footer {
            margin-top: 2rem;
            text-align: center;
            color: var(--dark-color);
            font-size: 14px;
            opacity: 0.8;
        }
        
        .logo {
            margin-bottom: 1.5rem;
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 480px) {
            .login-box {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">Laundry Tech</div>
            <h2>Admin Portal</h2>
            <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
            <form method="POST">
                <input name="username" type="text" placeholder="Username" required autofocus>
                <input name="password" type="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2023 Laundry Tech. All rights reserved.</p>
    </div>
</body>
</html>