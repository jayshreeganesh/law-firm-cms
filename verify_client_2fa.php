<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['pending_2fa_client'])) {
    header("Location: client_login.php");
    exit;
}

$error = '';
$client_id = $_SESSION['pending_2fa_client'];

// Fetch the code just so we can display it for testing
$stmt = $pdo->prepare("SELECT two_factor_code FROM clients WHERE id = ?");
$stmt->execute([$client_id]);
$demo_code = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ? AND two_factor_code = ?");
    $stmt->execute([$client_id, $code]);
    $client = $stmt->fetch();
    
    if ($client) {
        // Clear code
        $pdo->prepare("UPDATE clients SET two_factor_code = NULL WHERE id = ?")->execute([$client_id]);
        
        // Log in
        unset($_SESSION['pending_2fa_client']);
        $_SESSION['client_id'] = $client['id'];
        $_SESSION['client_name'] = $client['name'];
        
        header("Location: client_dashboard.php");
        exit;
    } else {
        $error = "Invalid verification code.";
    }
}
$page_title = 'Security Verification';
require_once 'includes/header.php';
?>
<section class="section bg-light" style="min-height: 70vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 450px;">
        <div class="card" style="text-align: center;">
            <i class="fas fa-mobile-alt" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h2 style="margin-top: 0; color: var(--primary-color);">Security Verification</h2>
            <p style="color: #64748b; margin-bottom: 1.5rem;">Please enter the 6-digit security code sent to your email to verify your identity.</p>
            
            <?php if ($error): ?>
                <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <div style="background-color: #eff6ff; color: #1e3a8a; padding: 0.5rem; border-radius: 4px; margin-bottom: 1.5rem; font-size: 0.85rem; border: 1px dashed #93c5fd;">
                <strong>Demo Mode:</strong> Your code is <?= htmlspecialchars($demo_code) ?>
            </div>
            
            <form method="POST" action="">
                <div style="margin-bottom: 1.5rem;">
                    <input type="text" name="code" required placeholder="000000" style="width: 100%; padding: 1rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 1.5rem; text-align: center; letter-spacing: 5px;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; border: none; background-color: var(--primary-color);">Verify Identity</button>
            </form>
            <div style="margin-top: 1.5rem;">
                <a href="client_login.php" style="color: #64748b; text-decoration: none; font-size: 0.9rem;">Cancel Login</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
