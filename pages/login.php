<?php
// ========================= pages/login.php =========================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/database.php';

// ...existing code...

// Initialize variables
$error_message = '';
$success_message = '';
$debug_info = [];

// Check if user is already logged in as student
if (isset($_SESSION['student_id'])) {
    header('Location: ./enrollment_system/student/dashboard.php');
    exit;
}

// Check if user is logged in as admin and redirect appropriately
if (isset($_SESSION['admin_id'])) {
    if (isset($_POST['clear_admin'])) {
        session_destroy();
    // ...existing code...
        $success_message = 'Admin session cleared. You can now login as a student.';
    }
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['login']) || !empty($_POST['login_id']))) {
    $login_id = trim($_POST['login_id'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login_id) || empty($password)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        try {
            $db = Database::getInstance();

            // Check if login_id is email or student_id
            $query = "SELECT * FROM students WHERE email = ? OR student_id = ? LIMIT 1";
            $student = $db->fetch($query, [$login_id, $login_id]);

            if ($student) {
                // Try password_verify (bcrypt/argon2)
                if (password_verify($password, $student['password'])) {
                    session_destroy();
                // ...existing code...
                    $_SESSION['student_id'] = $student['id'];
                    $_SESSION['student_name'] = $student['name'];
                    $_SESSION['student_email'] = $student['email'];
                    $_SESSION['student_course'] = $student['course'];
                    header('Location: /enrollment_system_m1/enrollment_system/student/dashboard.php');
                    exit;

                // Try MD5
                } elseif (md5($password) === $student['password']) {
                    session_destroy();
                // ...existing code...
                    $_SESSION['student_id'] = $student['id'];
                    $_SESSION['student_name'] = $student['name'];
                    $_SESSION['student_email'] = $student['email'];
                    $_SESSION['student_course'] = $student['course'];
                    header('Location: /enrollment_system_m1/enrollment_system/student/dashboard.php');
                    exit;

                // Try plain text (legacy)
                } elseif ($password === $student['password']) {
                    session_destroy();
                // ...existing code...
                    $_SESSION['student_id'] = $student['id'];
                    $_SESSION['student_name'] = $student['name'];
                    $_SESSION['student_email'] = $student['email'];
                    $_SESSION['student_course'] = $student['course'];
                    header('Location: /enrollment_system_m1/enrollment_system/student/dashboard.php');
                    exit;

                } else {
                    $error_message = 'Invalid student ID/email or password.';
                }
            } else {
                $error_message = 'Invalid student ID/email or password.';
            }
        } catch (Exception $e) {
            $error_message = 'An error occurred during login. Please try again.';
            error_log('Login error: ' . $e->getMessage());
        }
    }
}
?>

<!-- ========== HTML Part Below ========== -->

<div class="container">
    <div class="form-container">
        <div class="form-header">
            <h2 class="form-title">Student Login Disabled</h2>
            <p class="form-subtitle">Please use the main login page: <a href="/enrollment_system_m1/enrollment_system/login.php">Student Login</a></p>
        </div>
        <div class="alert alert-warning">
            This login form is disabled. Please use the main login page for student access.
        </div>
    </div>
</div>

<!-- ========== STYLES ========== -->
<style>
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    .password-input-wrapper {
        position: relative;
    }
    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 0.25rem;
    }
    .password-toggle:hover {
        color: #333;
    }
</style>

<!-- ========== SCRIPT ========== -->
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('password-toggle-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'fas fa-eye';
        }
    }
</script>
