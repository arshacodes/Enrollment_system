
<?php
include_once '../config.php';
include_once '../includes/database.php';
include_once '../includes/header.php';

if (!isset($_SESSION['student_id'])) {
    header('Location: ../login.php');
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

// Calculate total unpaid amount
$total_unpaid = 0;
foreach ($rows as $row) {
    if (strtolower($row['payment_status']) !== 'paid') {
        $total_unpaid += $row['total_amount'];
    }
}

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
    <!-- ...existing code... -->
    <div class="container">
        <div class="dashboard-header">
            <h2><i class="fas fa-file-invoice-dollar"></i> Billing & Payment Status</h2>
            <p>View your billing details and payment status for each semester.</p>
        </div>
        <div class="table-container">
            <div class="container" style="max-width: 900px; margin: 2rem auto;">
                <div class="dashboard-header" style="margin-bottom: 2rem; text-align: center;">
                    <h2 style="font-size:2rem; color: var(--primary); margin-bottom: 0.5rem;"><i class="fas fa-file-invoice-dollar"></i> Billing & Payment Status</h2>
                    <p style="color: var(--gray-600); font-size:1rem;">View your billing details and payment status for each semester.</p>
                </div>
                <div class="table-container" style="box-shadow: var(--shadow-lg);">
                    <div class="table-responsive">
                        <table class="table" style="background: var(--white);">
                            <thead>
                                <tr style="background: var(--primary); color: var(--white);">
                                    <th style="width: 8%;">ID</th>
                                    <th style="width: 15%;">Semester</th>
                                    <th style="width: 13%;">Tuition</th>
                                    <th style="width: 13%;">Lab</th>
                                    <th style="width: 13%;">Misc</th>
                                    <th style="width: 13%;">Total</th>
                                    <th style="width: 12%;">Status</th>
                                    <th style="width: 13%;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($rows) === 0): ?>
                                    <tr><td colspan="8" class="empty-state" style="text-align:center; color: var(--gray-500);">No billing records found.</td></tr>
                                <?php else: foreach ($rows as $row): ?>
                                <tr style="border-bottom: 1px solid var(--gray-200);">
                                    <td class="font-bold" style="text-align:center;"><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td style="text-align:center;"><?php echo htmlspecialchars($row['semester']); ?></td>
                                    <td style="text-align:right;"><?php echo formatCurrency($row['tuition_fee']); ?></td>
                                    <td style="text-align:right;"><?php echo formatCurrency($row['lab_fee']); ?></td>
                                    <td style="text-align:right;"><?php echo formatCurrency($row['misc_fee']); ?></td>
                                    <td class="font-bold" style="text-align:right; color: var(--primary-dark);"><?php echo formatCurrency($row['total_amount']); ?></td>
                                    <td style="text-align:center;"><?php echo statusBadge($row['payment_status']); ?></td>
                                    <td style="text-align:center;"><?php echo formatDate($row['created_at']); ?></td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="billing-summary" style="margin-top:2rem; text-align:right;">
                        <h3 style="font-size:1.2rem; color:var(--error);">Total Unpaid Amount: <span class="font-bold" style="color:var(--primary-dark);"><?php echo formatCurrency($total_unpaid); ?></span></h3>
                    </div>
                </div>
