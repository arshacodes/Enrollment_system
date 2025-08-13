<?php
require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    include_once '../config.php';
}

// Redirect to login if not logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();
$studentId = $_SESSION['student_id'];
$student = $db->fetch("SELECT * FROM students WHERE id = ?", [$studentId]);

if (!$student) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Get statistics
$stats = [
    'enrolled_subjects' => $db->fetch("SELECT COUNT(*) as count FROM enrollments WHERE student_id = ?", [$studentId])['count'],
    'total_units' => $db->fetch("SELECT COALESCE(SUM(s.units), 0) as total FROM enrollments e JOIN subjects s ON e.subject_id = s.id WHERE e.student_id = ?", [$studentId])['total'],
    'total_bill' => $db->fetch("SELECT COALESCE(SUM(total_amount), 0) as total FROM billing WHERE student_id = ?", [$studentId])['total'],
    'payment_status' => $db->fetch("SELECT payment_status FROM billing WHERE student_id = ? ORDER BY created_at DESC LIMIT 1", [$studentId])['payment_status'] ?? 'pending'
];

// Get recent enrollments
$recentEnrollments = $db->fetchAll("
    SELECT s.subject_code, s.title, s.units, e.created_at, e.status
    FROM enrollments e
    JOIN subjects s ON e.subject_id = s.id
    WHERE e.student_id = ?
    ORDER BY e.created_at DESC
    LIMIT 5
", [$studentId]);

// Helper functions
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return '₱' . number_format($amount, 2);
    }
}
if (!function_exists('formatDate')) {
    function formatDate($date, $format = 'M d, Y') {
        return date($format, strtotime($date));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; }
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .dashboard-header { text-align: center; margin-bottom: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .dashboard-content { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card-header { background: #007bff; color: white; padding: 15px; border-radius: 8px 8px 0 0; }
        .card-body { padding: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .btn { background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .quick-actions { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .action-buttons { display: flex; flex-direction: column; gap: 10px; }
        .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-enrolled { background: #d1ecf1; color: #0c5460; }
        .empty-state { text-align: center; padding: 40px; color: #666; }
        .font-bold { font-weight: bold; }
        @media (max-width: 768px) {
            .dashboard-content { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Simple Navigation -->
    <nav style="background: white; padding: 15px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px;">
            <div style="font-size: 20px; font-weight: bold; color: #333;">NCST Enrollment System</div>
            <div>
                <a href="dashboard.php" style="margin-right: 20px; text-decoration: none; color: #333;">Dashboard</a>
                <a href="enrollment.php" style="margin-right: 20px; text-decoration: none; color: #333;">Enrollment</a>
                
                <a href="../logout.php" style="text-decoration: none; color: #dc3545;">Logout</a>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($student['name']); ?>!</h1>
            <p>Student ID: <?php echo htmlspecialchars($student['student_id']); ?> | Course: <?php echo htmlspecialchars($student['course'] ?? 'Not specified'); ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="background: #007bff; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $stats['enrolled_subjects']; ?></h3>
                        <p style="margin: 0; color: #666;">Enrolled Subjects</p>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="background: #28a745; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 2rem;"><?php echo $stats['total_units']; ?></h3>
                        <p style="margin: 0; color: #666;">Total Units</p>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="background: #17a2b8; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.5rem;"><?php echo formatCurrency($stats['total_bill']); ?></h3>
                        <p style="margin: 0; color: #666;">Total Bill</p>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="background: #ffc107; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0;">
                            <span class="status-badge status-<?php echo $stats['payment_status']; ?>">
                                <?php echo ucfirst($stats['payment_status']); ?>
                            </span>
                        </h3>
                        <p style="margin: 0; color: #666;">Payment Status</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="margin: 0;"><i class="fas fa-history"></i> Recent Enrollments</h2>
                    <a href="enrollment.php" class="btn">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentEnrollments)): ?>
                        <div class="empty-state">
                            <i class="fas fa-book-open" style="font-size: 3rem; margin-bottom: 20px; color: #ccc;"></i>
                            <p>No enrollments yet. <a href="enrollment.php">Start enrolling</a> in subjects!</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Title</th>
                                        <th>Units</th>
                                        <th>Status</th>
                                        <th>Date Enrolled</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentEnrollments as $enrollment): ?>
                                    <tr>
                                        <td class="font-bold"><?php echo htmlspecialchars($enrollment['subject_code']); ?></td>
                                        <td><?php echo htmlspecialchars($enrollment['title']); ?></td>
                                        <td><?php echo $enrollment['units']; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $enrollment['status']; ?>">
                                                <?php echo ucfirst($enrollment['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($enrollment['created_at']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="enrollment.php" class="btn">
                        <i class="fas fa-plus"></i> Enroll Subjects
                    </a>
                    <a href="payment_status.php" class="btn" style="background: #6c757d;">
                        <i class="fas fa-receipt"></i> View Billing
                    </a>
                    
                        
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Simple dashboard interactions
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.15)';
                    this.style.transition = 'all 0.3s ease';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                });
            });
        });
    </script>
</body> 
</html>