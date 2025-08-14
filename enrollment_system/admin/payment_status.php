<?php
// Admin Billing Status Management
include_once '../config.php';
include_once '../includes/database.php';
include_once '../includes/header.php';

// Only allow admins
if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$db = Database::getInstance();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['billing_id'], $_POST['payment_status'])) {
    $billing_id = intval($_POST['billing_id']);
    $payment_status = $_POST['payment_status'];
    $db->execute("UPDATE billing SET payment_status = ? WHERE id = ?", [$payment_status, $billing_id]);
    $success = "Payment status updated.";
}

// Helper: Get payments for a billing record
function getBillingPayments($billing_id, $db) {
    $stmt = $db->getConnection()->prepare("SELECT amount, paid_at FROM billing_payments WHERE billing_id = ? ORDER BY paid_at ASC");
    $stmt->execute([$billing_id]);
    return $stmt->fetchAll();
}

// Handle installment payment submission (admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['installment_billing_id'], $_POST['installment_amount'])) {
    $billing_id = intval($_POST['installment_billing_id']);
    $amount = floatval($_POST['installment_amount']);
    // Get billing record
    $billing = $db->fetch("SELECT * FROM billing WHERE id = ?", [$billing_id]);
    if ($billing && $amount > 0) {
        $payments = getBillingPayments($billing_id, $db);
        $paid = 0;
        foreach ($payments as $p) { $paid += $p['amount']; }
        $remaining = $billing['total_amount'] - $paid;
        if ($amount <= $remaining) {
            $stmt = $db->getConnection()->prepare("INSERT INTO billing_payments (billing_id, amount) VALUES (?, ?)");
            $stmt->execute([$billing_id, $amount]);
            // If fully paid, update billing status; else set to pending
            $payments = getBillingPayments($billing_id, $db);
            $paid_total = 0;
            foreach ($payments as $p) { $paid_total += $p['amount']; }
            if ($paid_total >= $billing['total_amount']) {
                $db->getConnection()->prepare("UPDATE billing SET payment_status = 'paid' WHERE id = ?")->execute([$billing_id]);
            } else {
                $db->getConnection()->prepare("UPDATE billing SET payment_status = 'pending' WHERE id = ?")->execute([$billing_id]);
            }
            $success = "Installment payment recorded.";
        }
    }
}

$billings = $db->fetchAll("SELECT b.*, s.name as student_name FROM billing b JOIN students s ON b.student_id = s.id ORDER BY b.created_at DESC");
?>
<div class="container">
    <h2>Manage Billing Status</h2>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <table class="table">
        <thead>
            <tr>
                <th>Billing ID</th>
                <th>Student</th>
                <th>Semester</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Reference #</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($billings as $billing): ?>
            <tr>
                <td><?php echo $billing['id']; ?></td>
                <td><?php echo htmlspecialchars($billing['student_name']); ?></td>
                <td><?php echo htmlspecialchars($billing['semester']); ?></td>
                <td><?php echo formatCurrency($billing['total_amount']); ?></td>
                <td>
                    <?php
                    $status = strtolower($billing['payment_status']);
                    if ($status === 'paid') {
                        echo '<span class="status-badge status-paid">Paid</span>';
                    } elseif ($status === 'pending') {
                        echo '<span class="status-badge status-pending">Pending</span>';
                    } elseif ($status === 'cancelled') {
                        echo '<span class="status-badge status-cancelled">Cancelled</span>';
                    } else {
                        echo '<span class="status-badge">' . ucfirst($status) . '</span>';
                    }
                    ?>
                </td>
                <td>-</td>
                <td><?php echo formatDate($billing['created_at']); ?></td>
                <td>
                    <!-- Installment payment form -->
                    <?php
                    $payments = getBillingPayments($billing['id'], $db);
                    $paid = 0;
                    foreach ($payments as $p) { $paid += $p['amount']; }
                    $remaining = $billing['total_amount'] - $paid;
                    ?>
                    <div style="margin-bottom:8px;">
                        <strong>Paid:</strong> ₱<?php echo number_format($paid,2); ?> <br>
                        <strong>Remaining:</strong> ₱<?php echo number_format($remaining,2); ?>
                    </div>
                    <form method="POST" style="margin-bottom:8px; display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                        <input type="hidden" name="installment_billing_id" value="<?php echo $billing['id']; ?>">
                        <input type="number" name="installment_amount" min="1" max="<?php echo $remaining; ?>" step="0.01" placeholder="Amount" required style="width:100px; padding:4px 8px; border-radius:4px; border:1px solid #ccc;">
                        <button type="submit" class="btn btn-sm" style="padding:4px 14px; font-size:0.95rem;">Record Payment</button>
                    </form>
                    <div style="margin-top:8px; text-align:left;">
                        <strong>Payments:</strong>
                        <ul style="margin:0; padding-left:18px;">
                            <?php foreach ($payments as $p): ?>
                                <li>₱<?php echo number_format($p['amount'],2); ?> <span style="color:#888; font-size:0.95em;">on <?php echo formatDate($p['paid_at'],'M d, Y'); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include_once '../includes/footer.php'; ?>
