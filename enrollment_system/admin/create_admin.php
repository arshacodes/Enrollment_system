<?php
require_once '../config.php';
require_once '../includes/database.php';

$db = Database::getInstance();
$db->execute(
    "INSERT INTO admins (username, password, role) VALUES (?, ?, ?)",
    ['admin', password_hash('newpassword123', PASSWORD_DEFAULT), 'super']
);
echo "Default admin created.";