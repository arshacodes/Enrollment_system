<?php
// ========================= pages/login.php =========================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize variables
$error_message = '';
$success_message = '';

// Check if user is already logged in
if (isset($_SESSION['student_id'])) {
    header('Location: ../student/dashboard.php');
    exit;
}

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $login_id = trim($_POST['login_id'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($login_id) || empty($password)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        try {
            $db = Database::getInstance();
            
            // Check if login_id is email or student_id
            $query = "SELECT * FROM students WHERE email = ? OR student_id = ? LIMIT 1";
            $student = $db->fetch($query, [$login_id, $login_id]);

            if ($student && password_verify($password, $student['password'])) {
                // Login successful - set session variables
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['student_name'] = $student['name'];
                $_SESSION['student_email'] = $student['email'];
                $_SESSION['student_course'] = $student['course'];
                $_SESSION['student_status'] = $student['status'];

                // Log activity
                logActivity($student['id'], 'student_login', 'Student logged in');

                // Redirect to student dashboard
                header('Location: ../student/dashboard.php');
                exit;
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

<div class="container">
    <div class="form-container">
        <div class="form-header">
            <h2 class="form-title">Student Login</h2>
            <p class="form-subtitle">Access your enrollment dashboard</p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="login-form">
            <div class="form-group">
                <label class="form-label">Student ID or Email *</label>
                <input type="text" name="login_id" class="form-input" 
                       placeholder="Enter your Student ID or Email" 
                       value="<?php echo isset($_POST['login_id']) ? htmlspecialchars($_POST['login_id']) : ''; ?>" 
                       required>
            </div>

            <div class="form-group">
                <label class="form-label">Password *</label>
                <div class="password-input-wrapper">
                    <input type="password" name="password" class="form-input" id="password" 
                           placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="password-toggle-icon"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember_me" 
                           <?php echo isset($_POST['remember_me']) ? 'checked' : ''; ?>> 
                    Remember me baby
                </label>
            </div>

            <button type="submit" name="login" class="btn btn-primary btn-block" id="login-btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
            
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="?page=register">Register here</a></p>
                <p><a href="#" onclick="alert('Please contact the registrar office for password reset.')">Forgot Password?</a></p>
            </div>
        </form>
    </div>
</div>

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

    .flex {
        display: flex;
    }

    .items-center {
        align-items: center;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .mt-3 {
        margin-top: 1rem;
    }
</style>

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

    // Form validation
    document.getElementById('login-form').addEventListener('submit', function(e) {
        const loginId = document.querySelector('input[name="login_id"]').value.trim();
        const password = document.querySelector('input[name="password"]').value;
        
        if (!loginId || !password) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
</script>