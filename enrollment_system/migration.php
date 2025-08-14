<?php
require_once __DIR__ . '/includes/database.php';

try {
    $db = Database::getInstance();

    // Fetch all students
    $students = $db->fetchAll("SELECT id, password FROM students");

    foreach ($students as $student) {
        $storedPass = $student['password'];

        // If it doesn't look like a hash (starts with $2y$), then hash it
        if (strpos($storedPass, '$2y$') !== 0) {
            $hashed = password_hash($storedPass, PASSWORD_DEFAULT);

            $db->query("UPDATE students SET password = ? WHERE id = ?", [$hashed, $student['id']]);

            echo "Updated password for student ID: {$student['id']}<br>";
        }
    }

    echo "<strong>Migration completed successfully!</strong>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
