<?php
require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

requireAdmin();

$db = Database::getInstance();
$admin = getCurrentAdmin();

// Get statistics
$stats = [
    'total_students' => $db->fetch("SELECT COUNT(*) as count FROM students")['count'],
    'pending_students' => $db->fetch("SELECT COUNT(*) as count FROM students WHERE status = 'pending'")['count'],
    'approved_students' => $db->fetch("SELECT COUNT(*) as count FROM students WHERE status = 'approved'")['count'],
    'total_enrollments' => $db->fetch("SELECT COUNT(*) as count FROM enrollments")['count'],
    'total_subjects' => $db->fetch("SELECT COUNT(*) as count FROM subjects")['count'],
    'total_revenue' => $db->fetch("SELECT COALESCE(SUM(total_amount), 0) as total FROM billing WHERE payment_status = 'paid'")['total']
];

// Get pending applications
$pendingStudents = $db->fetchAll("SELECT * FROM students WHERE status = 'pending' ORDER BY created_at DESC LIMIT 10");

// Get recent activities
$recentActivities = $db->fetchAll("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/admin_nav.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($admin['username']); ?> (<?php echo ucfirst($admin['role']); ?>)</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_students']; ?></h3>
                    <p>Total Students</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-yellow">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['pending_students']; ?></h3>
                    <p>Pending Applications</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-green">
                    <i class="fas fa-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['approved_students']; ?></h3>
                    <p>Approved Students</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-purple">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_enrollments']; ?></h3>
                    <p>Total Enrollments</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-orange">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_subjects']; ?></h3>
                    <p>Total Subjects</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-teal">
                    <i class="fas fa-money-bill"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo formatCurrency($stats['total_revenue']); ?></h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>

        <div class="admin-content">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-user-clock"></i> Pending Applications</h2>
                    <a href="students.php" class="btn btn-primary btn-sm">Manage All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($pendingStudents)): ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <p>No pending applications</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Course</th>
                                        <th>Email</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingStudents as $student): ?>
                                    <tr>
                                        <td class="font-bold"><?php echo htmlspecialchars($student['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['course']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo formatDate($student['created_at']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-success btn-sm" onclick="approveStudent(<?php echo $student['id']; ?>)">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="rejectStudent(<?php echo $student['id']; ?>)">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-history"></i> Recent Activities</h2>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <?php foreach ($recentActivities as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-circle"></i>
                            </div>
                            <div class="activity-content">
                                <p><?php echo htmlspecialchars($activity['message']); ?></p>
                                <small><?php echo formatDate($activity['created_at'], 'M d, Y g:i A'); ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
    function approveStudent(id) {
        if (confirm('Are you sure you want to approve this student?')) {
            updateStudentStatus(id, 'approve');
        }
    }
    function rejectStudent(id) {
        if (confirm('Are you sure you want to reject this student?')) {
            updateStudentStatus(id, 'reject');
        }
    }
    function updateStudentStatus(id, action) {
        fetch('approve_reject_student.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + encodeURIComponent(id) + '&action=' + encodeURIComponent(action)
        })
        .then(response => response.text())
        .then(result => {
            if (result.trim() === 'success') {
                location.reload();
            } else {
                alert('Failed to update student status.');
            }
        });
    }
    </script>
</body>
</html>