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

// Fetch all billing records
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
                <td><?php echo htmlspecialchars($billing['payment_status']); ?></td>
                <td><?php echo htmlspecialchars($billing['reference_number']); ?></td>
                <td><?php echo formatDate($billing['created_at']); ?></td>
                <td>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="billing_id" value="<?php echo $billing['id']; ?>">
                        <select name="payment_status">
                            <option value="pending" <?php if ($billing['payment_status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="paid" <?php if ($billing['payment_status'] == 'paid') echo 'selected'; ?>>Paid</option>
                            <option value="cancelled" <?php if ($billing['payment_status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include_once '../includes/footer.php'; ?>
