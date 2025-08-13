<?php

require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $db = Database::getInstance();
    $admin = $db->fetch("SELECT * FROM admins WHERE username = ?", [$username]);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role'] = $admin['role'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
} else {
    // Insert default admin account if not exists
    $db = Database::getInstance();
    $adminExists = $db->fetch("SELECT * FROM admins WHERE username = ?", ['admin']);

    if (!$adminExists) {
        $db->execute("INSERT INTO admins (username, password, role) VALUES (?, ?, ?)", ['admin', password_hash('newpassword123', PASSWORD_DEFAULT), 'super']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        body {
            min-height: 100vh;
            background: #f5f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-center-wrapper {
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem 2rem 2rem;
            width: 100%;
            max-width: 370px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-card h2 {
            margin-bottom: 1.5rem;
            color: #333;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .login-form {
            width: 100%;
        }
        .form-group {
            margin-bottom: 1.2rem;
            width: 100%;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            color: #444;
            font-size: 1rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            background: #f9f9f9;
            transition: border 0.2s;
        }
        .form-group input:focus {
            border-color: #007bff;
            outline: none;
            background: #fff;
        }
        .btn-block {
            width: 100%;
            padding: 0.8rem 0;
            font-size: 1.1rem;
            border-radius: 6px;
            margin-top: 0.5rem;
        }
        .alert {
            width: 100%;
            margin-bottom: 1rem;
            padding: 0.8rem 1rem;
            border-radius: 5px;
            font-size: 0.98rem;
        }
        .alert-danger {
            background: #ffeaea;
            color: #c0392b;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 500px) {
            .login-card {
                padding: 1.5rem 0.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-center-wrapper">
        <div class="login-card">
            <h2>Admin Login</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="login.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</body>
</html>