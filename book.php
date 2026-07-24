<?php 
require_once 'includes/db.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $date = trim($_POST['appointment_date'] ?? '');
    $time = trim($_POST['appointment_time'] ?? '');
    
    if ($name && $email && $date && $time) {
        $stmt = $pdo->prepare("INSERT INTO appointments (name, email, phone, appointment_date, appointment_time) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $phone, $date, $time])) {
            $success = true;
        } else {
            $error = 'Failed to book appointment. Please try again later.';
        }
    } else {
        $error = 'Please fill out all required fields.';
    }
}

$page_title = 'Book a Consultation';
require_once 'includes/header.php'; 
?>

<section class="section" style="background-color: var(--primary-color); padding: 80px 0 40px; color: white;">
    <div class="container text-center">
        <h1 style="color: white; margin-bottom: 1rem;">Book a Consultation</h1>
        <p style="color: #cbd5e1; max-width: 600px; margin: 0 auto;">Select a date and time to speak with one of our experienced attorneys. We will review your request and confirm your appointment shortly.</p>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            <?php if ($success): ?>
                <div class="card" style="text-align: center; padding: 3rem 2rem;">
                    <i class="fas fa-check-circle" style="font-size: 4rem; color: #10b981; margin-bottom: 1.5rem;"></i>
                    <h2 style="margin-bottom: 1rem;">Booking Requested</h2>
                    <p style="color: var(--text-light); margin-bottom: 2rem;">Thank you, your appointment request has been submitted. Our office will contact you shortly to confirm the details.</p>
                    <a href="index.php" class="btn btn-primary">Return Home</a>
                </div>
            <?php else: ?>
                <div class="card">
                    <?php if ($error): ?>
                        <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="book.php" method="POST">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Full Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Phone Number</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Preferred Date *</label>
                                <input type="date" name="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Preferred Time *</label>
                                <select name="appointment_time" class="form-control" required>
                                    <option value="">Select a time...</option>
                                    <option value="09:00:00">09:00 AM</option>
                                    <option value="10:00:00">10:00 AM</option>
                                    <option value="11:00:00">11:00 AM</option>
                                    <option value="13:00:00">01:00 PM</option>
                                    <option value="14:00:00">02:00 PM</option>
                                    <option value="15:00:00">03:00 PM</option>
                                    <option value="16:00:00">04:00 PM</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Request Appointment</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
