<?php
// student/logout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Store a logout message
$_SESSION['logout_message'] = 'You have been successfully logged out.';

// Destroy all session data
session_destroy();

// Start a new session for the message
session_start();
$_SESSION['logout_message'] = 'You have been successfully logged out.';

// Redirect to login page
header('Location: ../index.php');
exit();
?>