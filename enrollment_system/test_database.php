<?php
// Create this file as: test_database.php in your root directory
include_once '../config.php';

echo "<h2>Database Connection Test</h2>";

// Test 1: Check if files exist
echo "<h3>1. File Check:</h3>";
echo "config.php exists: " . (file_exists('config.php') ? 'YES' : 'NO') . "<br>";
echo "includes/database.php exists: " . (file_exists('includes/database.php') ? 'YES' : 'NO') . "<br>";
echo "connection.php exists: " . (file_exists('connection.php') ? 'YES' : 'NO') . "<br>";
echo "student/dashboard.php exists: " . (file_exists('student/dashboard.php') ? 'YES' : 'NO') . "<br>";

// Test 2: Try database connections
echo "<h3>2. Database Connection Test:</h3>";
try {
    // Try Database class first
    if (file_exists('includes/database.php')) {
        require_once 'includes/database.php';
        if (class_exists('Database')) {
            $db = Database::getInstance();
            echo "Database class connection: SUCCESS<br>";
            
            // Test query
            $result = $db->fetchAll("SHOW TABLES");
            echo "Tables found: " . count($result) . "<br>";
            foreach ($result as $table) {
                echo "- " . implode(', ', $table) . "<br>";
            }
        }
    }
    
    // Try PDO connection
    if (file_exists('connection.php')) {
        require_once 'connection.php';
        if (isset($pdo)) {
            echo "PDO connection: SUCCESS<br>";
        }
    }
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: Check students table
echo "<h3>3. Students Table Test:</h3>";
try {
    if (isset($db)) {
        $students = $db->fetchAll("SELECT id, student_id, email, name, password FROM students LIMIT 5");
    } elseif (isset($pdo)) {
        $stmt = $pdo->query("SELECT id, student_id, email, name, password FROM students LIMIT 5");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    if (!empty($students)) {
        echo "Students found: " . count($students) . "<br>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Student ID</th><th>Email</th><th>Name</th><th>Password Type</th></tr>";
        
        foreach ($students as $student) {
            $password_type = (strlen($student['password']) === 60 && substr($student['password'], 0, 4) === '$2y$') ? 'Hashed' : 'Plain Text';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($student['id']) . "</td>";
            echo "<td>" . htmlspecialchars($student['student_id']) . "</td>";
            echo "<td>" . htmlspecialchars($student['email']) . "</td>";
            echo "<td>" . htmlspecialchars($student['name']) . "</td>";
            echo "<td>" . $password_type . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No students found in database<br>";
    }
    
} catch (Exception $e) {
    echo "Error checking students table: " . $e->getMessage() . "<br>";
}

// Test 4: Session test
echo "<h3>4. Session Test:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Session data: <br>";
print_r($_SESSION);

// Test 5: Test password hashing
echo "<h3>5. Password Hash Test:</h3>";
$test_password = "123456";
$hash = password_hash($test_password, PASSWORD_DEFAULT);
echo "Test password: " . $test_password . "<br>";
echo "Generated hash: " . $hash . "<br>";
echo "Verification: " . (password_verify($test_password, $hash) ? 'SUCCESS' : 'FAILED') . "<br>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h2, h3 { color: #333; }
    table { margin: 10px 0; }
    th { background: #f0f0f0; padding: 8px; }
    td { padding: 8px; }
</style>