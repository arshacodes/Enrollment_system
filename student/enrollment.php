<?php
require_once '../config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

requireLogin();

$db = Database::getInstance();
$user = getCurrentUser();
$error = '';
$success = '';

$stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$_SESSION['student_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// Handle enrollment/drop actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['enroll_subject'])) {
            $subject_id = (int)$_POST['subject_id'];

            // echo $user;
            // echo $subject_id;
            
            $db->beginTransaction();
            
            // Check if subject exists and has slots
            $subject = $db->fetch("SELECT * FROM subjects WHERE id = ? AND available_slots > 0", [$subject_id]);
            if (!$subject) {
                throw new Exception("Subject not available or no slots remaining.");
            }
            
            // Check if already enrolled
            $existing = $db->fetch("SELECT id, status FROM enrollments WHERE student_id = ? AND subject_id = ?", [$user['id'], $subject_id]);
            if ($existing && $existing['status'] === 'enrolled') {
                throw new Exception("You are already enrolled in this subject.");
            }
            
            // Check for schedule conflicts
            $conflicts = $db->fetchAll("
                SELECT s.subject_code, s.schedule 
                FROM enrollments e 
                JOIN subjects s ON e.subject_id = s.id 
                WHERE e.student_id = ? AND s.schedule = ? AND e.status = 'enrolled'
            ", [$user['id'], $subject['schedule']]);
            if (!empty($conflicts)) {
                throw new Exception("Schedule conflict with: " . $conflicts[0]['subject_code']);
            }
            
            // Enroll student
            if ($existing && $existing['status'] === 'dropped') {
                // Update dropped enrollment to active
                $db->execute("UPDATE enrollments SET status = 'enrolled', created_at = NOW() WHERE id = ?", [$existing['id']]);
            } else {
                // Enroll student
                $db->execute("INSERT INTO enrollments (student_id, subject_id, status, created_at) VALUES (?, ?, 'enrolled', NOW())", [$user['id'], $subject_id]);
            }
            
            // Update available slots
            $db->execute("UPDATE subjects SET available_slots = available_slots - 1 WHERE id = ?", [$subject_id]);
            
            $db->commit();
            logActivity("Enrolled in subject: " . $subject['subject_code'], $user['id'], 'enrollment');
            $success = "Successfully enrolled in " . $subject['subject_code'] . "!";

            // --- BILLING LOGIC (default values) ---
            $semester = $subject['semester'] ?? 'N/A';
            $tuition_fee = 10000; // Default tuition fee per enrollment
            $lab_fee = 2000;      // Default lab fee per enrollment
            $misc_fee = 1000;     // Default misc fee per enrollment
            $total_amount = $tuition_fee + $lab_fee + $misc_fee;

            // Check if billing record exists for this student and semester
            $billing = $db->fetch("SELECT id FROM billing WHERE student_id = ? AND semester = ?", [$user['id'], $semester]);
            if ($billing) {
                // Update billing (add fees for each new enrollment)
                $db->execute("UPDATE billing SET tuition_fee = tuition_fee + ?, lab_fee = lab_fee + ?, misc_fee = misc_fee + ?, total_amount = total_amount + ?, payment_status = 'pending', created_at = NOW() WHERE id = ?", [$tuition_fee, $lab_fee, $misc_fee, $total_amount, $billing['id']]);
            } else {
                // Insert new billing
                $db->execute("INSERT INTO billing (student_id, semester, tuition_fee, lab_fee, misc_fee, total_amount, payment_status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())", [$user['id'], $semester, $tuition_fee, $lab_fee, $misc_fee, $total_amount]);
            }
            
        } elseif (isset($_POST['drop_subject'])) {
            $enrollment_id = (int)$_POST['enrollment_id'];
            
            $db->beginTransaction();
            
            // Get enrollment details
            $enrollment = $db->fetch("
                SELECT e.*, s.subject_code, s.id as subject_id 
                FROM enrollments e 
                JOIN subjects s ON e.subject_id = s.id 
                WHERE e.id = ? AND e.student_id = ?
            ", [$enrollment_id, $user['id']]);
            
            if (!$enrollment) {
                throw new Exception("Enrollment not found.");
            }
            
            // Update enrollment status to dropped
            $db->execute("UPDATE enrollments SET status = 'dropped' WHERE id = ?", [$enrollment_id]);
            
            // Restore available slot
            $db->execute("UPDATE subjects SET available_slots = available_slots + 1 WHERE id = ?", [$enrollment['subject_id']]);
            
            $db->commit();
            logActivity("Dropped subject: " . $enrollment['subject_code'], $user['id'], 'enrollment');
            $success = "Successfully dropped " . $enrollment['subject_code'] . "!";
        }
    } catch (Exception $e) {
        // Only rollback if a transaction is active
        if ($db->getConnection()->inTransaction()) {
            $db->rollback();
        }
        $error = $e->getMessage();
    }
}

// Get filters
$department = $_GET['department'] ?? '';
$year_level = $_GET['year_level'] ?? '';

// Get available subjects
$subjects_query = "SELECT * FROM subjects WHERE available_slots > 0 AND is_active = 1";
$params = [];

if ($department) {
    $subjects_query .= " AND department = ?";
    $params[] = $department;
}

if ($year_level) {
    $subjects_query .= " AND year_level = ?";
    $params[] = $year_level;
}

$subjects_query .= " ORDER BY department, year_level, subject_code";
$subjects = $db->fetchAll($subjects_query, $params);

// Get current enrollments
$current_enrollments = $db->fetchAll("
    SELECT e.*, s.subject_code, s.title, s.units, s.instructor, s.schedule, s.department 
    FROM enrollments e 
    JOIN subjects s ON e.subject_id = s.id 
    WHERE e.student_id = ? AND e.status = 'enrolled'
    ORDER BY s.subject_code
", [$user['id']]);

// Get departments for filter
$departments = $db->fetchAll("SELECT DISTINCT department FROM subjects ORDER BY department");

// Calculate total units
$total_units = array_sum(array_column($current_enrollments, 'units'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/student_nav.php'; ?>

    <div class="dashboard-container">
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Current Enrollments -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-clipboard-list"></i> Current Enrollments (<?php echo count($current_enrollments); ?> subjects, <?php echo $total_units; ?> units)</h2>
            </div>
            <div class="card-body">
                <?php if (empty($current_enrollments)): ?>
                    <div class="empty-state">
                        <i class="fas fa-book-open"></i>
                        <p>No current enrollments. Start enrolling in subjects below!</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Title</th>
                                    <th>Instructor</th>
                                    <th>Schedule</th>
                                    <th>Units</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($current_enrollments as $enrollment): ?>
                                <tr>
                                    <td class="font-bold"><?php echo htmlspecialchars($enrollment['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($enrollment['title']); ?></td>
                                    <td><?php echo htmlspecialchars($enrollment['instructor']); ?></td>
                                    <td><?php echo htmlspecialchars($enrollment['schedule']); ?></td>
                                    <td><?php echo $enrollment['units']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $enrollment['status']; ?>">
                                            <?php echo ucfirst($enrollment['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to drop this subject?')">
                                            <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['id']; ?>">
                                            <button type="submit" name="drop_subject" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Drop
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-container">
            <h3><i class="fas fa-filter"></i> Filter Available Subjects</h3>
            <form method="GET">
                <div class="filters-grid">
                    <div>
                        <label class="form-label">Department</label>
                        <select name="department" class="form-select">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['department']; ?>" <?php echo ($department == $dept['department']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept['department']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Year Level</label>
                        <select name="year_level" class="form-select">
                            <option value="">All Years</option>
                            <option value="1" <?php echo ($year_level == '1') ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2" <?php echo ($year_level == '2') ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3" <?php echo ($year_level == '3') ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4" <?php echo ($year_level == '4') ? 'selected' : ''; ?>>4th Year</option>
                        </select>
                    </div>
                </div>
                <div style="margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="enrollment.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Available Subjects -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-book"></i> Available Subjects (<?php echo count($subjects); ?>)</h2>
            </div>
            <div class="card-body">
                <?php if (empty($subjects)): ?>
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <p>No subjects found matching your criteria.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Title</th>
                                    <th>Department</th>
                                    <th>Year</th>
                                    <th>Units</th>
                                    <th>Instructor</th>
                                    <th>Schedule</th>
                                    <th>Available Slots</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subjects as $subject): ?>
                                <tr>
                                    <td class="font-bold"><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['title']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['department']); ?></td>
                                    <td><?php echo $subject['year_level']; ?></td>
                                    <td><?php echo $subject['units']; ?></td>
                                    <td><?php echo htmlspecialchars($subject['instructor']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['schedule']); ?></td>
                                    <td><?php echo $subject['available_slots'] . '/' . $subject['max_slots']; ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="subject_id" value="<?php echo $subject['id']; ?>">
                                            <button type="submit" name="enroll_subject" class="btn btn-success btn-sm" onclick="return confirm('Enroll in <?php echo htmlspecialchars($subject['subject_code']); ?>?')">
                                                <i class="fas fa-plus"></i> Enroll
                                            </button>
                                        </form>
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

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>