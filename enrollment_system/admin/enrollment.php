<?php
require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

requireAdmin();

$db = Database::getInstance();

// Fetch all enrollments with student and subject info
$enrollments = $db->fetchAll("
    SELECT e.*, s.name AS student_name, s.student_id AS student_code, sub.title AS subject_name, sub.subject_code AS subject_code
    FROM enrollments e
    LEFT JOIN students s ON e.student_id = s.id
    LEFT JOIN subjects sub ON e.subject_id = sub.id
    ORDER BY e.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Enrollments - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/admin_nav.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Enrollments</h1>
        </div>
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-book"></i> All Enrollments</h2>
            </div>
            <div class="card-body">
                <?php if (empty($enrollments)): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>No enrollments found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Enrollment ID</th>
                                    <th>Student</th>
                                    <th>Subject</th>
                                    <th>Date Enrolled</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrollments as $enrollment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($enrollment['id']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($enrollment['student_name'] ?? 'N/A'); ?>
                                        <br>
                                        <small><?php echo htmlspecialchars($enrollment['student_code'] ?? ''); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($enrollment['subject_name'] ?? 'N/A'); ?>
                                        <br>
                                        <small><?php echo htmlspecialchars($enrollment['subject_code'] ?? ''); ?></small>
                                    </td>
                                    <td><?php echo formatDate($enrollment['created_at']); ?></td>
                                    <td>
                                        <?php if ($enrollment['status'] === 'active'): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php elseif ($enrollment['status'] === 'completed'): ?>
                                            <span class="badge badge-primary">Completed</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?php echo htmlspecialchars(ucfirst($enrollment['status'])); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>