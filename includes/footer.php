<?php
// ========================= includes/footer.php =========================
?>
<footer style="background: var(--gray-800); color: var(--white); padding: 2rem 0; margin-top: 3rem;">
    <div class="container">
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
            <div>
                <h3 style="color: var(--primary-light); margin-bottom: 1rem;">
                    <i class="fas fa-graduation-cap"></i> <?php echo APP_NAME; ?>
                </h3>
                <p style="line-height: 1.6; color: var(--gray-300);">
                    Streamlining academic enrollment with modern technology and user-friendly interfaces.
                </p>
            </div>
            <div>
                <h4 style="margin-bottom: 1rem;">Quick Links</h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.5rem;"><a href="index.php" style="color: var(--gray-300);">Home</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="index.php?page=register" style="color: var(--gray-300);">Register</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="index.php?page=login" style="color: var(--gray-300);">Student Login</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="admin/login.php" style="color: var(--gray-300);">Admin Login</a></li>
                </ul>
            </div>
            <div>
                <h4 style="margin-bottom: 1rem;">Contact Info</h4>
                <p style="color: var(--gray-300); margin-bottom: 0.5rem;">
                    <i class="fas fa-map-marker-alt"></i> Amafel Building, Aguinaldo Highway, Dasmariñas City, Cavite
                </p>
                <p style="color: var(--gray-300); margin-bottom: 0.5rem;">
                    <i class="fas fa-phone"></i> (555) 123-4567
                </p>
                <p style="color: var(--gray-300); margin-bottom: 0.5rem;">
                    <i class="fas fa-envelope"></i> ncst.edu.ph
                </p>
            </div>
            <div>
                <h4 style="margin-bottom: 1rem;">Office Hours</h4>
                <p style="color: var(--gray-300); margin-bottom: 0.5rem;">
                    <strong>Monday - Friday:</strong><br>8:00 AM - 5:00 PM
                </p>
                <p style="color: var(--gray-300); margin-bottom: 0.5rem;">
                    <strong>Saturday:</strong><br>9:00 AM - 12:00 PM
                </p>
                <p style="color: var(--gray-300);">
                    <strong>Sunday:</strong> Closed
                </p>
            </div>
        </div>
        <hr style="border: none; height: 1px; background: var(--gray-600); margin: 2rem 0;">
        <div class="text-center">
            <p style="color: var(--gray-400); margin: 0;">
                &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
            </p>
        </div>
    </div>
</footer>