<?php
// ========================= includes/student_nav.php =========================
?>
<nav class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                <a href="../index.php" style="color: var(--white); text-decoration: none;"><?php echo APP_NAME; ?></a>
            </div>
            <ul class="nav">
                <li class="nav-item">
                    <a href="/enrollment_system/Enrollment_system/student/dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/enrollment_system/Enrollment_system/student/enrollment.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'enrollment.php' ? 'active' : ''; ?>">
                        <i class="fas fa-book"></i> Enrollment
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/enrollment_system/Enrollment_system/student/payment_status.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'payment_status.php' ? 'active' : ''; ?>">
                        <i class="fas fa-credit-card"></i> Billing
                    </a>
                </li>
                <!-- <li class="nav-item">
                    
                        
                    </a>
                </li> -->
                <li class="nav-item">
                    <a href="/enrollment_system/Enrollment_system/student/logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>