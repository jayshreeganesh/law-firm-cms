<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-widget">
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: white; font-weight: 800;">
                    Justice<span style="color: var(--secondary-color);">.</span>
                </h4>
                <p style="margin-top: 1rem;">Providing world-class legal representation for individuals and corporations worldwide. Your justice is our priority.</p>
            </div>
            <div class="footer-widget">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="practice-areas.php">Practice Areas</a></li>
                    <li><a href="attorneys.php">Our Attorneys</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-widget">
                <h4>Contact Info</h4>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt" style="color: var(--secondary-color); margin-right: 10px;"></i> <?= htmlspecialchars(get_setting($pdo, 'site_address')) ?></li>
                    <li><i class="fas fa-phone" style="color: var(--secondary-color); margin-right: 10px;"></i> <?= htmlspecialchars(get_setting($pdo, 'site_phone')) ?></li>
                    <li><i class="fas fa-envelope" style="color: var(--secondary-color); margin-right: 10px;"></i> <?= htmlspecialchars(get_setting($pdo, 'site_email')) ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(get_setting($pdo, 'site_name')) ?>. All Rights Reserved. Designed by Antigravity.</p>
        </div>
    </div>
</footer>
</body>
</html>
