<?php
require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

requireAdmin();

// Initialize database first
$db = Database::getInstance();

function getBillingPayments($billing_id, $db) {
    $stmt = $db->getConnection()->prepare("SELECT amount, paid_at FROM billing_payments WHERE billing_id = ? ORDER BY paid_at ASC");
    $stmt->execute([$billing_id]);
    return $stmt->fetchAll();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['billing_id'], $_POST['payment_status'])) {
    $billing_id = intval($_POST['billing_id']);
    $payment_status = $_POST['payment_status'];
    $db->execute("UPDATE billing SET payment_status = ? WHERE id = ?", [$payment_status, $billing_id]);
    $success = "Payment status updated.";
}

// Handle installment payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['installment_billing_id'], $_POST['installment_amount'])) {
    $installment_billing_id = intval($_POST['installment_billing_id']);
    $installment_amount = floatval($_POST['installment_amount']);

    // Insert the new payment record
    $db->execute("
        INSERT INTO billing_payments (billing_id, amount, paid_at)
        VALUES (?, ?, NOW())
    ", [$installment_billing_id, $installment_amount]);

    $success = "Installment payment recorded.";
}

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
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
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
                                    <th>Date Paid</th>
                                    <th>Action</th>
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
                                    <td><?php echo formatDate($payment['created_at']); ?></td>
                                    <td>
                                        <?php
                                        $paymentsList = getBillingPayments($payment['id'], $db);
                                        $paid = 0;
                                        foreach ($paymentsList as $p) { $paid += $p['amount']; }
                                        $remaining = $payment['total_amount'] - $paid;
                                        ?>
                                        <div style="margin-bottom:8px;">
                                            <strong>Paid:</strong> ₱<?php echo number_format($paid,2); ?> <br>
                                            <strong>Remaining:</strong> ₱<?php echo number_format($remaining,2); ?>
                                        </div>
                                        <form method="POST" style="margin-bottom:8px; display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                            <input type="hidden" name="installment_billing_id" value="<?php echo $payment['id']; ?>">
                                            <input type="number" name="installment_amount" min="1" max="<?php echo $remaining; ?>" step="0.01" placeholder="Amount" required style="width:100px; padding:4px 8px; border-radius:4px; border:1px solid #444;">
                                            <button type="submit" class="btn btn-sm" style="padding:4px 14px; font-size:0.95rem;">Record Payment</button>
                                        </form>
                                        <div style="margin-top:8px; text-align:left;">
                                            <strong>Payments:</strong>
                                            <ul style="margin:0; padding-left:18px;">
                                                <?php foreach ($paymentsList as $p): ?>
                                                    <li>₱<?php echo number_format($p['amount'],2); ?> <span style="color:#aaa; font-size:0.95em;">on <?php echo formatDate($p['paid_at'],'M d, Y'); ?></span></li>
                                                <?php endforeach; ?>
                                            </ul>
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
    </div>
</body>
</html>