<?php

require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    include_once '../config.php';
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
            <div class="form-container" style="max-width: 400px; margin: 4rem auto; box-shadow: var(--shadow-lg); background: var(--white);">
                <div class="form-header" style="margin-bottom: 2rem;">
                    <h2 class="form-title" style="color: var(--primary-dark); margin-bottom: 0.5rem;">Admin Login</h2>
                    <p class="form-subtitle" style="color: var(--gray-700);">Access the admin dashboard</p>
                </div>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error" style="margin-bottom:1rem; color: var(--error); background: #fee2e2; border: 1px solid #fca5a5; padding: 0.75rem; border-radius: var(--radius-md); text-align:center; font-weight:500;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <form method="post" class="login-form" style="margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="username" class="form-label" style="color: var(--gray-800); font-weight:600;">Username *</label>
                        <input type="text" name="username" id="username" class="form-input" style="background: var(--gray-50); color: var(--gray-900); border: 2px solid var(--gray-200);" placeholder="Enter your username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="password" class="form-label" style="color: var(--gray-800); font-weight:600;">Password *</label>
                        <input type="password" name="password" id="password" class="form-input" style="background: var(--gray-50); color: var(--gray-900); border: 2px solid var(--gray-200);" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" style="margin-top:1rem; background: var(--primary-dark); color: var(--white); font-size:1.1rem;">Login</button>
                </form>
            </div>
    </div>
</body>
</html>