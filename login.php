<?php
require_once 'connection.php';
session_start();

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
    <div class="login-container">
        <h2>Student Login</h2>
        <p>Access your enrollment dashboard</p>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" class="login-form">
            <div class="form-group">
                <label for="login">Student ID or Email *</label>
                <input type="text" name="login" id="login" placeholder="Enter your Student ID or Email" value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
    </div>
</body>
</html>