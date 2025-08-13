functions
<?php
require_once __DIR__ . '/database.php';

// Security functions
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validateStudentId($student_id) {
    return preg_match('/^[A-Z0-9-]{5,20}$/', $student_id);
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Session functions
function isLoggedIn() {
    return isset($_SESSION['student_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

/**
 * Check if student is logged in, redirect to login if not
 */
function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['student_id'])) {
        header('Location: ../index.php?page=login');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect('../admin/login.php');
    }
}

function getCurrentUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['student_id'])) {
        return null;
    }
    // You can add database fetch logic here if needed
}

function getCurrentAdmin() {
    if (isAdmin()) {
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'] ?? '',
            'role' => $_SESSION['admin_role'] ?? ''
        ];
    }
    return null;
}

// Utility functions
function redirect($url) {
    header("Location: $url");
    exit();
}

function flashMessage($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function getFlashMessage($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

function formatCurrency($amount) {
    return '₱' . number_format($amount, 2);
}

// File upload functions
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', __DIR__ . '/../uploads/');
}

function uploadFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'], $maxSize = 5000000) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error');
    }

    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowedTypes)) {
        throw new Exception('Invalid file type');
    }

    if ($fileSize > $maxSize) {
        throw new Exception('File size too large');
    }

    $newFileName = uniqid() . '.' . $fileExt;
    $uploadPath = UPLOAD_PATH . $newFileName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        return $newFileName;
    } else {
        throw new Exception('Failed to upload file');
    }
}

// Validation functions
function validateRequired($value, $fieldName) {
    if (empty(trim($value))) {
        throw new Exception("$fieldName is required");
    }
    return trim($value);
}

function validateLength($value, $min, $max, $fieldName) {
    $length = strlen($value);
    if ($length < $min || $length > $max) {
        throw new Exception("$fieldName must be between $min and $max characters");
    }
    return $value;
}

function validateNumeric($value, $fieldName) {
    if (!is_numeric($value)) {
        throw new Exception("$fieldName must be a number");
    }
    return $value;
}

// Logging function
function logActivity($message, $userId = null, $type = 'info') {
    $db = Database::getInstance();
    $sql = "INSERT INTO activity_logs (user_id, message, type, created_at) VALUES (?, ?, ?, NOW())";
    $db->execute($sql, [$userId, $message, $type]);
}
?>
