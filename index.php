<?php
require_once 'config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = Database::getInstance();
$page = $_GET['page'] ?? 'home';
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['register'])) {
            $name = validateRequired($_POST['name'], 'Name');
            $student_id = validateRequired($_POST['student_id'], 'Student ID');
            $email = validateRequired($_POST['email'], 'Email');
            $course = validateRequired($_POST['course'], 'Course');
            $password = validateRequired($_POST['password'], 'Password');

            $name = validateLength($name, 2, 100, 'Name');
            $password = validateLength($password, 6, 255, 'Password');

            if (!validateEmail($email)) {
                throw new Exception('Invalid email format');
            }

            if (!validateStudentId($student_id)) {
                throw new Exception('Invalid student ID format');
            }

            // Check if student ID or email exists
            $existing = $db->fetch("SELECT id FROM students WHERE student_id = ? OR email = ?", [$student_id, $email]);
            if ($existing) {
                throw new Exception('Student ID or Email already exists');
            }

            // Insert new student
            $hashedPassword = hashPassword($password);
            $sql = "INSERT INTO students (name, student_id, email, course, password, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
            $db->execute($sql, [$name, $student_id, $email, $course, $hashedPassword]);

            logActivity("New student registration: $student_id", null, 'registration');
            flashMessage('success', 'Registration successful! Please wait for admin approval.');
            redirect('index.php?page=login');

        } elseif (isset($_POST['login'])) {
            $login_id = validateRequired($_POST['login_id'], 'Login ID');
            $password = validateRequired($_POST['password'], 'Password');

            $user = $db->fetch("SELECT * FROM students WHERE (student_id = ? OR email = ?) AND status = 'approved'", [$login_id, $login_id]);

            if (!$user || !verifyPassword($password, $user['password'])) {
                throw new Exception('Invalid credentials or account not approved');
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['course'] = $user['course'];

            logActivity("User login: " . $user['student_id'], $user['id'], 'login');
            redirect('/enrollment_system/student/dashboard.php');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get flash messages
$success = getFlashMessage('success');
$error = getFlashMessage('error') ?: $error;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="main-content">
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php
        switch ($page) {
            case 'home':
                include 'pages/home.php';
                break;
            case 'register':
                include 'pages/register.php';
                break;
            case 'login':
                include 'login.php';
                break;
            case 'about':
                include 'pages/about.php';
                break;
            default:
                include 'pages/home.php';
        }
        ?>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>
?>