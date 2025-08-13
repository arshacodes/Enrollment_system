<?php
// ========================= includes/bootstrap.php =========================
// Central configuration file to fix all path issues

// Prevent direct access
if (!defined('NCST_SYSTEM')) {
    define('NCST_SYSTEM', true);
}

// Start output buffering early to prevent header issues
if (!ob_get_level()) {
    ob_start();
}

// Define absolute paths from root directory
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PAGES_PATH', ROOT_PATH . '/pages');
define('STUDENT_PATH', ROOT_PATH . '/student');
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Define web paths (URLs)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host;

// Determine base directory from document root
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
if ($script_dir !== '/') {
    $base_url .= $script_dir;
}

define('BASE_URL', rtrim($base_url, '/'));
define('ASSETS_URL', BASE_URL . '/assets');

// Include configuration
require_once(realpath(ROOT_PATH . '/config.php'));

// Enhanced session management
function initializeSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Secure session configuration
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        
    // ...existing code...
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

// Initialize session
initializeSession();

// Authentication helper functions
function isLoggedIn() {
    return isset($_SESSION['student_id']) && !empty($_SESSION['student_id']);
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireLogin($redirect_to = null) {
    if (!isLoggedIn()) {
        if (!$redirect_to) {
            // Determine the best login path based on current location
            $current_dir = dirname($_SERVER['SCRIPT_NAME']);
            
            if (strpos($current_dir, '/student') !== false) {
                $redirect_to = BASE_URL . '/login.php';
            } elseif (strpos($current_dir, '/admin') !== false) {
                $redirect_to = BASE_URL . '/admin/login.php';
            } else {
                $redirect_to = BASE_URL . '/login.php';
            }
        }
        
        // Clear any output buffer before redirect
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Location: ' . $redirect_to);
        exit;
    }
}

function requireAdminLogin($redirect_to = null) {
    if (!isAdminLoggedIn()) {
        $redirect_to = $redirect_to ?: BASE_URL . '/admin/login.php';
        
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Location: ' . $redirect_to);
        exit;
    }
}

function logout($redirect_to = null) {
    // Destroy session
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    
    // Redirect
    $redirect_to = $redirect_to ?: BASE_URL . '/login.php';
    
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Location: ' . $redirect_to);
    exit;
}

// Database connection helper
function getDatabaseConnection() {
    try {
        require_once INCLUDES_PATH . '/database.php';
        return Database::getInstance();
    } catch (Exception $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        return null;
    }
}

// Path resolution helpers
function getAssetUrl($path) {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

function getPageUrl($path) {
    return BASE_URL . '/' . ltrim($path, '/');
}

// Redirect helper with proper header handling
function safeRedirect($url, $permanent = false) {
    // Clean output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set appropriate headers
    $status_code = $permanent ? 301 : 302;
    http_response_code($status_code);
    
    // Prevent caching of redirect
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Perform redirect
    header('Location: ' . $url);
    exit;
}

// Error handling
function handleError($message, $redirect_to = null) {
    error_log('Application Error: ' . $message);
    
    if ($redirect_to) {
        $_SESSION['error_message'] = $message;
        safeRedirect($redirect_to);
    } else {
        // Display error inline
        return '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ' . 
               htmlspecialchars($message) . '</div>';
    }
}

// Success message helper
function setSuccessMessage($message) {
    $_SESSION['success_message'] = $message;
}

function setErrorMessage($message) {
    $_SESSION['error_message'] = $message;
}

function getFlashMessage($type = 'error') {
    $key = $type . '_message';
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return null;
}

// Utility functions
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return '₱' . number_format((float)$amount, 2);
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'M d, Y') {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return 'N/A';
        }
        return date($format, strtotime($date));
    }
}

if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map('sanitizeInput', $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

// Debug helper (remove in production)
define('DEBUG_MODE', false); // Set to true to enable debug logging

function debugLog($message, $data = null) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        error_log('DEBUG: ' . $message . ($data ? ' - ' . print_r($data, true) : ''));
    }
}

// Auto-include common files if they exist
$common_includes = [
    INCLUDES_PATH . '/functions.php',
    INCLUDES_PATH . '/validation.php'
];

foreach ($common_includes as $file) {
    if (file_exists($file)) {
        require_once $file;
    }
}

?>