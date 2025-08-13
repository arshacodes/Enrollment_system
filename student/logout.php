<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log the logout activity before destroying session
if (isset($_SESSION['student_id'])) {
    logActivity($_SESSION['student_id'], 'student_logout', 'Student logged out');
}

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page
header('Location: ../index.php');
exit;