<?php
// ========================= includes/admin_nav.php =========================
?>
<nav class="admin-nav">
    <div class="container">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <a href="../index.php" style="color: var(--white); text-decoration: none;"><?php echo APP_NAME; ?> - Admin</a>
        </div>
        <ul class="nav">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="students.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Students
                </a>
            </li>
            <li class="nav-item">
                <a href="enrollment.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'enrollment.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> Enrollments
                </a>
            </li>
            <li class="nav-item">
                <a href="subjects.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'subjects.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Subjects
                </a>
            </li>
            <li class="nav-item">
                <a href="payment.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'payment.php' ? 'active' : ''; ?>">
                    <i class="fas fa-money-bill"></i> Payments
                </a>
            </li>
            <li class="nav-item">
                <a href="reports.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a href="../logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>