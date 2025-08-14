<?php
include_once dirname(__DIR__) . '/config.php';
include_once dirname(__DIR__) . '/includes/database.php';
include_once dirname(__DIR__) . '/includes/header.php';


if (!isset($_SESSION['student_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$db = Database::getInstance();


// Get all billing transactions per subject

// Get semester-level billing records for the student
$query = "SELECT id, semester, tuition_fee, lab_fee, misc_fee, total_amount, payment_status, reference_number, created_at
FROM billing
WHERE student_id = ?
ORDER BY created_at DESC";
$stmt = $db->getConnection()->prepare($query);
$stmt->execute([$student_id]);

$rows = $stmt->fetchAll();

// Helper: Get payments for a billing record
function getBillingPayments($billing_id, $db) {
    $stmt = $db->getConnection()->prepare("SELECT amount, reference_number, paid_at FROM billing_payments WHERE billing_id = ? ORDER BY paid_at ASC");
    $stmt->execute([$billing_id]);
    return $stmt->fetchAll();
}

// Calculate total unpaid amount (consider installments)
$total_unpaid = 0;
foreach ($rows as $row) {
    $payments = getBillingPayments($row['id'], $db);
    $paid = 0;
    foreach ($payments as $p) {
        $paid += $p['amount'];
    }
    $remaining = $row['total_amount'] - $paid;
    if ($remaining > 0) {
        $total_unpaid += $remaining;
    }
}

// Payment submission removed for student side

// ...existing code...
function statusBadge($status) {
    if (empty($status)) return '<span class="status-badge status-unpaid">No status</span>';
    $status = strtolower($status);
    if ($status === 'paid') return '<span class="status-badge status-paid">Paid</span>';
    if ($status === 'pending') return '<span class="status-badge status-pending">Pending</span>';
    return '<span class="status-badge status-enrolled">' . ucfirst($status) . '</span>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing & Payment Status - <?php echo defined('APP_NAME') ? APP_NAME : 'NCST'; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2><i class="fas fa-file-invoice-dollar"></i> Billing & Payment Status</h2>
            <p>View your billing details and payment status for each semester.</p>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Important Information
            </div>
            <div class="card-body">
                <span>If you have unpaid bills, please settle them at the cashier or online. For questions, contact the finance office.</span>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-credit-card"></i> Billing Summary
            </div>
            <div class="card-body" style="text-align:right;">
                <span style="font-size: 1.1rem; color: #888;">Total Unpaid Amount</span><br>
                <span style="font-size: 2rem; color: #dc3545; font-weight: bold;"><?php echo formatCurrency($total_unpaid); ?></span>
                <!-- Payment functionality not available -->
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i> Billing Records
            </div>
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Semester</th>
                                <th>Tuition</th>
                                <th>Lab</th>
                                <th>Misc</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($rows) === 0): ?>
                                <tr><td colspan="8" class="empty-state">No billing records found.</td></tr>
                            <?php else: foreach ($rows as $row): ?>
                            <tr>
                                <td class="font-bold" style="text-align:center;"><?php echo htmlspecialchars($row['id']); ?></td>
                                <td style="text-align:center;"><?php echo htmlspecialchars($row['semester']); ?></td>
                                <td style="text-align:right;"><?php echo formatCurrency($row['tuition_fee']); ?></td>
                                <td style="text-align:right;"><?php echo formatCurrency($row['lab_fee']); ?></td>
                                <td style="text-align:right;"><?php echo formatCurrency($row['misc_fee']); ?></td>
                                <td class="font-bold" style="text-align:right; color: #7289da;"><?php echo formatCurrency($row['total_amount']); ?></td>
                                <td style="text-align:center;"><?php echo formatDate($row['created_at']); ?></td>
                                <td style="text-align:center;">
                                    <?php
                                    $payments = getBillingPayments($row['id'], $db);
                                    $paid = 0;
                                    foreach ($payments as $p) { $paid += $p['amount']; }
                                    $remaining = $row['total_amount'] - $paid;
                                    if ($remaining <= 0): ?>
                                        <span style="color: #43b581; font-weight: 500;">Paid</span>
                                    <?php else: ?>
                                        <span style="color: #faa61a; font-weight: 500;">Unpaid (₱<?php echo number_format($remaining,2); ?>)</span>
                                    <?php endif; ?>
                                    <div style="margin-top:8px; text-align:left;">
                                        <strong>Payments:</strong>
                                        <ul style="margin:0; padding-left:18px;">
                                            <?php foreach ($payments as $p): ?>
                                                <li>₱<?php echo number_format($p['amount'],2); ?> <span style="color:#aaa; font-size:0.95em;">on <?php echo formatDate($p['paid_at'],'M d, Y'); ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
