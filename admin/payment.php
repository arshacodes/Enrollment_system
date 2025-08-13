<?php
require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

requireAdmin();

$db = Database::getInstance();

// Fetch all payments/billing records with student info
$payments = $db->fetchAll("
    SELECT b.*, s.name AS student_name, s.student_id AS student_code
    FROM billing b
    LEFT JOIN students s ON b.student_id = s.id
    ORDER BY b.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/admin_nav.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Manage Payments</h1>
        </div>
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-money-bill"></i> All Payments</h2>
            </div>
            <div class="card-body">
                <?php if (empty($payments)): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>No payment records found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($payment['student_name'] ?? 'N/A'); ?>
                                        <br>
                                        <small><?php echo htmlspecialchars($payment['student_code'] ?? ''); ?></small>
                                    </td>
                                    <td><?php echo formatCurrency($payment['total_amount']); ?></td>
                                    <td>
                                        <?php if ($payment['payment_status'] === 'paid'): ?>
                                            <span class="badge badge-success">Paid</span>
                                        <?php elseif ($payment['payment_status'] === 'pending'): ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?php echo htmlspecialchars(ucfirst($payment['payment_status'])); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo formatDate($payment['created_at']); ?></td>
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