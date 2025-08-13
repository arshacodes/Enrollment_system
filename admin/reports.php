<?php
require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

requireAdmin();

$db = Database::getInstance();

// Example: Get summary statistics for reports
$totalStudents = $db->fetch("SELECT COUNT(*) as count FROM students")['count'];
$totalEnrollments = $db->fetch("SELECT COUNT(*) as count FROM enrollments")['count'];
$totalSubjects = $db->fetch("SELECT COUNT(*) as count FROM subjects")['count'];
$totalRevenue = $db->fetch("SELECT COALESCE(SUM(total_amount), 0) as total FROM billing WHERE payment_status = 'paid'")['total'];

// Example: Get monthly enrollment counts
$monthlyEnrollments = $db->fetchAll("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
    FROM enrollments
    GROUP BY month
    ORDER BY month DESC
    LIMIT 12
");

// Example: Get top 5 subjects by enrollment
$topSubjects = $db->fetchAll("
    SELECT sub.title AS subject_name, COUNT(e.id) as enroll_count
    FROM enrollments e
    LEFT JOIN subjects sub ON e.subject_id = sub.id
    GROUP BY e.subject_id
    ORDER BY enroll_count DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/admin_nav.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Reports & Analytics</h1>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-blue"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3><?php echo $totalStudents; ?></h3>
                    <p>Total Students</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-purple"><i class="fas fa-book"></i></div>
                <div class="stat-info">
                    <h3><?php echo $totalEnrollments; ?></h3>
                    <p>Total Enrollments</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-orange"><i class="fas fa-graduation-cap"></i></div>
                <div class="stat-info">
                    <h3><?php echo $totalSubjects; ?></h3>
                    <p>Total Subjects</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-teal"><i class="fas fa-money-bill"></i></div>
                <div class="stat-info">
                    <h3><?php echo formatCurrency($totalRevenue); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top:2rem;">
            <div class="card-header">
                <h2><i class="fas fa-chart-line"></i> Monthly Enrollments (Last 12 Months)</h2>
            </div>
            <div class="card-body">
                <?php if (empty($monthlyEnrollments)): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>No enrollment data found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Enrollments</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthlyEnrollments as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['month']); ?></td>
                                    <td><?php echo htmlspecialchars($row['count']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card" style="margin-top:2rem;">
            <div class="card-header">
                <h2><i class="fas fa-star"></i> Top 5 Subjects by Enrollment</h2>
            </div>
            <div class="card-body">
                <?php if (empty($topSubjects)): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>No subject data found.</p>
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
                                    <th>Enrollments</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topSubjects as $subject): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['title']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['description'] ?? ''); ?></td>
                                    <td><?php echo formatDate($subject['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['enroll_count']); ?></td>
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
