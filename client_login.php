<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['client_id'])) {
    header("Location: client_dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        $client = $stmt->fetch();
        
        if ($client && password_verify($password, $client['password'])) {
            $code = sprintf("%06d", mt_rand(1, 999999));
            $pdo->prepare("UPDATE clients SET two_factor_code = ? WHERE id = ?")->execute([$code, $client['id']]);
            
            // Simulate sending email
            $from_email = get_setting($pdo, 'smtp_from_email') ?: 'no-reply@lawfirm.local';
            @mail($client['email'], "Your Secure Portal Code", "Your login code is: $code", "From: $from_email");
            
            $_SESSION['pending_2fa_client'] = $client['id'];
            header("Location: verify_client_2fa.php");
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Please enter email and password.";
    }
}

$page_title = 'Client Portal Login';
require_once 'includes/header.php';
?>

<section class="section" style="background-color: #f8fafc; min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div class="container" style="max-width: 450px;">
        <div class="card" style="text-align: center; padding: 2.5rem 2rem;">
            <i class="fas fa-user-lock" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1.5rem;"></i>
            <h2 style="margin-bottom: 0.5rem; color: var(--primary-color);">Client Portal</h2>
            <p style="color: var(--text-light); margin-bottom: 2rem;">Sign in to access your secure case documents.</p>
            
            <?php if ($error): ?>
                <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; text-align: left;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="client_login.php" style="text-align: left;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Secure Login</button>
            </form>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
