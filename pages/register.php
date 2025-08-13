<?php
// ========================= pages/register.php =========================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_message = '';
$success_message = '';

// Check if user is already logged in
if (isset($_SESSION['student_id'])) {
    header('Location: ../student/dashboard.php');
    exit;
}

// Process registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $student_id = trim($_POST['student_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = $_POST['course'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($student_id) || empty($name) || empty($email) || empty($course) || empty($password) || empty($confirm_password)) {
        $error_message = 'Please fill in all required fields.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[a-zA-Z0-9\-]{5,20}$/', $student_id)) {
        $error_message = 'Student ID must be 5-20 characters and contain only letters, numbers, and hyphens.';
    } else {
        try {
            $db = Database::getInstance();
            
            // Check if student_id already exists
            $existingStudent = $db->fetch("SELECT id FROM students WHERE student_id = ?", [$student_id]);
            if ($existingStudent) {
                $error_message = "Student ID already exists. Please use a different ID.";
            } else {
                // Check if email already exists
                $existingEmail = $db->fetch("SELECT id FROM students WHERE email = ?", [$email]);
                if ($existingEmail) {
                    $error_message = "Email address already registered. Please use a different email.";
                } else {
                    // Generate student ID number if using auto-generation
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert new student
                    $db->execute(
                        "INSERT INTO students (student_id, name, email, course, password, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())",
                        [$student_id, $name, $email, $course, $hashedPassword]
                    );

                    // Log the registration activity
                    $newStudentId = $db->getLastInsertId();
                    logActivity($newStudentId, 'student_registration', 'New student registered: ' . $name);

                    $success_message = 'Registration successful! Your application is now pending approval. You will be able to log in once approved by an administrator.';
                    
                    // Clear form data on success
                    $_POST = [];
                }
            }
            
        } catch (Exception $e) {
            $error_message = 'An error occurred during registration. Please try again.';
            error_log('Registration error: ' . $e->getMessage());
        }
    }
}
?>

<div class="container">
    <div class="form-container">
        <div class="form-header">
            <h2 class="form-title">Student Registration</h2>
            <p class="form-subtitle">Create your account to start enrollment</p>
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

        <form method="POST" id="registration-form">
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-input" 
                       placeholder="Enter your full name" 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                       required>
            </div>

            <div class="form-group">
                <label class="form-label">Student ID *</label>
                <input type="text" name="student_id" class="form-input" 
                       placeholder="e.g., 2024-0001" 
                       value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>" 
                       pattern="[a-zA-Z0-9\-]{5,20}" 
                       required>
                <small>Format: Letters, numbers, and hyphens only (5-20 characters)</small>
            </div>

            <div class="form-group">
                <label class="form-label">Course *</label>
                <select name="course" class="form-select" required>
                    <option value="">Select your course</option>
                    <option value="BSIT" <?php echo (isset($_POST['course']) && $_POST['course'] === 'BSIT') ? 'selected' : ''; ?>>BS Information Technology</option>
                    <option value="BSCS" <?php echo (isset($_POST['course']) && $_POST['course'] === 'BSCS') ? 'selected' : ''; ?>>BS Computer Science</option>
                    <option value="BSBA" <?php echo (isset($_POST['course']) && $_POST['course'] === 'BSBA') ? 'selected' : ''; ?>>BS Business Administration</option>
                    <option value="BSED" <?php echo (isset($_POST['course']) && $_POST['course'] === 'BSED') ? 'selected' : ''; ?>>BS Education</option>
                    <option value="BSN" <?php echo (isset($_POST['course']) && $_POST['course'] === 'BSN') ? 'selected' : ''; ?>>BS Nursing</option>
                    <option value="BSEE" <?php echo (isset($_POST['course']) && $_POST['course'] === 'BSEE') ? 'selected' : ''; ?>>BS Electrical Engineering</option>
                    <option value="BSCE" <?php echo (isset($_POST['course']) && $_POST['course'] === 'BSCE') ? 'selected' : ''; ?>>BS Civil Engineering</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-input" 
                       placeholder="your.email@example.com" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                       required>
            </div>

            <div class="form-group">
                <label class="form-label">Password *</label>
                <div class="password-input-wrapper">
                    <input type="password" name="password" class="form-input" id="password"
                           placeholder="Enter your password" minlength="6" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password', 'password-toggle-icon')">
                        <i class="fas fa-eye" id="password-toggle-icon"></i>
                    </button>
                </div>
                <small>Minimum 6 characters</small>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password *</label>
                <div class="password-input-wrapper">
                    <input type="password" name="confirm_password" class="form-input" id="confirm_password"
                           placeholder="Confirm your password" minlength="6" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', 'confirm-password-toggle-icon')">
                        <i class="fas fa-eye" id="confirm-password-toggle-icon"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="flex items-center gap-2">
                    <input type="checkbox" required>
                    I agree to the <a href="#" onclick="alert('Terms and conditions would be displayed here.')">Terms and Conditions</a>
                </label>
            </div>

            <button type="submit" name="register" class="btn btn-primary btn-block">
                <i class="fas fa-user-plus"></i> Register Account
            </button>
            
            <div class="text-center mt-3">
                <p>Already have an account? <a href="?page=login">Login here</a></p>
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

    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        background: white;
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

    small {
        color: #666;
        font-size: 0.875rem;
        display: block;
        margin-top: 0.25rem;
    }
</style>

<script>
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'fas fa-eye';
        }
    }

    // Form validation
    document.getElementById('registration-form').addEventListener('submit', function(e) {
        const password = document.querySelector('input[name="password"]').value;
        const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
        const studentId = document.querySelector('input[name="student_id"]').value;
        
        // Validate password match
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return;
        }
        
        // Validate password length
        if (password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long!');
            return;
        }
        
        // Validate student ID format
        if (!/^[a-zA-Z0-9\-]{5,20}$/.test(studentId)) {
            e.preventDefault();
            alert('Student ID must be 5-20 characters and contain only letters, numbers, and hyphens!');
            return;
        }
    });

    // Real-time password match validation
    document.querySelector('input[name="confirm_password"]').addEventListener('input', function() {
        const password = document.querySelector('input[name="password"]').value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
</script>