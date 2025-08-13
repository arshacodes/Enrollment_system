<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
$user = getCurrentUser();

$db = Database::getInstance();

// Get student statistics
$enrolledCount = (int) $db->fetch(
    "SELECT COUNT(*) as count FROM enrollments WHERE student_id = ?",
    [$user['id']]
)['count'];

$totalUnits = (int) $db->fetch(
    "SELECT COALESCE(SUM(s.units), 0) as total 
     FROM enrollments e 
     JOIN subjects s ON e.subject_id = s.id 
     WHERE e.student_id = ?",
    [$user['id']]
)['total'];

$recentEnrollments = $db->fetchAll(
    "SELECT s.subject_code, s.title, s.units, e.created_at, e.status
     FROM enrollments e 
     JOIN subjects s ON e.subject_id = s.id 
     WHERE e.student_id = ? 
     ORDER BY e.created_at DESC 
     LIMIT 5",
    [$user['id']]
);

$billing = $db->fetch(
    "SELECT * FROM billing WHERE student_id = ? ORDER BY created_at DESC LIMIT 1",
    [$user['id']]
);
?>

<!-- Your existing HTML for dashboard stays unchanged -->
