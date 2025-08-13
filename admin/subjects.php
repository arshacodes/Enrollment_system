<?php
require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

requireAdmin();

$db = Database::getInstance();

// Fetch all subjects
$subjects = $db->fetchAll("SELECT * FROM subjects ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/admin_nav.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Subjects</h1>
        </div>
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-graduation-cap"></i> All Subjects</h2>
            </div>
            <div class="card-body">
                <?php if (empty($subjects)): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>No subjects found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subjects as $subject): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['title']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['description'] ?? ''); ?></td>
                                    <td><?php echo formatDate($subject['created_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>