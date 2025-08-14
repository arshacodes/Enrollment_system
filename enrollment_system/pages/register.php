<?php
// ========================= pages/register.php =========================
?>
<div class="container">
    <div class="form-container">
        <div class="form-header">
            <h2 class="form-title">Student Registration</h2>
            <p class="form-subtitle">Create your account to start enrollment</p>
        </div>

        <form method="POST" id="registration-form">
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-input" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label class="form-label">Student ID *</label>
                <input type="text" name="student_id" class="form-input" placeholder="e.g., 2024-0001" required>
                <small>Format: Letters, numbers, and hyphens only (5-20 characters)</small>
            </div>

            <div class="form-group">
                <label class="form-label">Course *</label>
                <select name="course" class="form-select" required>
                    <option value="">Select your course</option>
                    <option value="BSIT">BS Information Technology</option>
                    <option value="BSCS">BS Computer Science</option>
                    <option value="BSBA">BS Business Administration</option>
                    <option value="BSED">BS Education</option>
                    <option value="BSN">BS Nursing</option>
                    <option value="BSEE">BS Electrical Engineering</option>
                    <option value="BSCE">BS Civil Engineering</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-input" placeholder="your.email@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                <small>Minimum 6 characters</small>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password *</label>
                <input type="password" name="confirm_password" class="form-input" placeholder="Confirm your password" required>
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

<?php
// No output before this line!

require_once __DIR__ . '/../config.php';
// ...other includes...

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $password = $_POST['password'];

    // Check if student_id already exists
    $existing = $db->fetch("SELECT * FROM students WHERE student_id = ?", [$student_id]);
    if ($existing) {
        $error = "Student ID already exists. Please use a different ID.";
    } else {
        // Insert new student
        $db->execute(
            "INSERT INTO students (student_id, name, email, course, password, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())",
            [$student_id, $name, $email, $course, password_hash($password, PASSWORD_DEFAULT)]
        );
        header('Location: ../login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Removed dark mode <style> block -->
</head>
<!-- ... -->
</html>