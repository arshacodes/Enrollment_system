<?php
require_once 'student/connection.php';
include_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    // Allow login with student_id or email
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ? OR email = ?");
    $stmt->execute([$login, $login]);
    $student = $stmt->fetch();

    if ($student && password_verify($password, $student['password'])) {
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['name'];
        header('Location: ../enrollment_system/student/dashboard.php');
        exit;
    } else {
        $error = 'Invalid Student ID/Email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="form-container" style="max-width: 400px; margin: 4rem auto; box-shadow: var(--shadow-lg); background: var(--white);">
        <div class="form-header" style="margin-bottom: 2rem;">
            <h2 class="form-title" style="color: var(--primary-dark); margin-bottom: 0.5rem;">Student Login</h2>
            <p class="form-subtitle" style="color: var(--gray-700);">Access your enrollment dashboard</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error" style="margin-bottom:1rem; color: var(--error); background: #fee2e2; border: 1px solid #fca5a5; padding: 0.75rem; border-radius: var(--radius-md); text-align:center; font-weight:500;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form method="post" class="login-form" style="margin-bottom: 1.5rem;">
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="login" class="form-label" style="color: var(--gray-800); font-weight:600;">Student ID or Email *</label>
                <input type="text" name="login" id="login" class="form-input" style="background: var(--gray-50); color: var(--gray-900); border: 2px solid var(--gray-200);" placeholder="Enter your Student ID or Email" value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" required>
            </div>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="password" class="form-label" style="color: var(--gray-800); font-weight:600;">Password *</label>
                <input type="password" name="password" id="password" class="form-input" style="background: var(--gray-50); color: var(--gray-900); border: 2px solid var(--gray-200);" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:1rem; background: var(--primary-dark); color: var(--white); font-size:1.1rem;">Login</button>
        </form>
        <div style="margin-top:2rem; text-align:center;">
            <p style="margin-bottom:0.5rem; color: var(--gray-700);">Don't have an account? <a href="register.php" style="color: var(--primary); text-decoration:underline;">Register here</a>.</p>
            <p><a href="forgot_password.php" style="color: var(--primary); text-decoration:underline;">Forgot Password?</a></p>
        </div>
    </div>
</body>
</html>