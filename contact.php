<?php 
require_once 'includes/db.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $phone, $subject, $message])) {
            $success = true;
        } else {
            $error = 'Failed to send message. Please try again.';
        }
    } else {
        $error = 'Please fill out all required fields.';
    }
}
require_once 'includes/header.php'; 
?>

<section class="section" style="background-color: var(--primary-color); padding: 80px 0 40px; color: white;">
    <div class="container text-center">
        <h1 style="color: white; margin-bottom: 0;">Contact Us</h1>
    </div>
</section>

<section class="section bg-white">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem;">
            <div>
                <h2>Get In Touch</h2>
                <p style="margin-bottom: 2rem;">We offer free initial consultations. Please fill out the form, and our legal team will contact you shortly.</p>
                
                <?php if ($success): ?>
                    <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                        Thank you for your message! We will get back to you soon.
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="message" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Message *</label>
                        <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
                </form>
            </div>
            
            <div>
                <h2>Office Location</h2>
                <div style="background-color: #f8fafc; padding: 2rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">Justice Partners</h3>
                    <p style="margin-bottom: 0.5rem;"><i class="fas fa-map-marker-alt" style="color: var(--secondary-color); width: 20px;"></i> <?= htmlspecialchars(get_setting($pdo, 'site_address')) ?></p>
                    <p style="margin-bottom: 0.5rem;"><i class="fas fa-phone" style="color: var(--secondary-color); width: 20px;"></i> <?= htmlspecialchars(get_setting($pdo, 'site_phone')) ?></p>
                    <p><i class="fas fa-envelope" style="color: var(--secondary-color); width: 20px;"></i> <?= htmlspecialchars(get_setting($pdo, 'site_email')) ?></p>
                </div>
                
                <!-- Map placeholder -->
                <div style="width: 100%; height: 300px; background-color: #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: #64748b;">[Google Map Integration Here]</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
