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
                <h4 style="color: white; margin-bottom: 1.5rem; font-size: 1.25rem;">Subscribe to Newsletter</h4>
                <p style="color: #cbd5e1; margin-bottom: 1rem; font-size: 0.95rem;">Get the latest legal insights and firm updates delivered to your inbox.</p>
                <form action="subscribe.php" method="POST" style="display: flex; margin-bottom: 0.5rem;">
                    <input type="email" name="email" placeholder="Email Address" required style="flex: 1; padding: 0.75rem; border: none; border-radius: 4px 0 0 4px; font-family: inherit;">
                    <button type="submit" class="btn btn-primary" style="border-radius: 0 4px 4px 0; padding: 0.75rem 1rem;">Subscribe</button>
                </form>
                <?php if (isset($_GET['newsletter_msg'])): ?>
                    <div style="color: #4ade80; font-size: 0.85rem;"><?= htmlspecialchars($_GET['newsletter_msg']) ?></div>
                <?php endif; ?>
            </div>
            <div class="footer-widget">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="practice-areas.php">Practice Areas</a></li>
                    <li><a href="attorneys.php">Our Attorneys</a></li>
                    <li><a href="blog.php">News</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="client_login.php" style="color: var(--secondary-color); font-weight: bold;"><i class="fas fa-lock"></i> Client Portal</a></li>
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
