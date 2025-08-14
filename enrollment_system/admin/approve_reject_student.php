<?php
require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id && in_array($action, ['approve', 'reject'])) {
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $db = Database::getInstance();
        $db->execute("UPDATE students SET status = ? WHERE id = ?", [$status, $id]);
        echo 'success';
        exit;
    }
}
echo 'error';
exit;