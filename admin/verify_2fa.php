<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['pending_2fa_admin'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$user_id = $_SESSION['pending_2fa_admin'];

// Fetch the code just so we can display it for testing (normally you wouldn't show this)
$stmt = $pdo->prepare("SELECT two_factor_code FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$demo_code = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND two_factor_code = ?");
    $stmt->execute([$user_id, $code]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Clear code
        $pdo->prepare("UPDATE users SET two_factor_code = NULL WHERE id = ?")->execute([$user_id]);
        
        // Log in
        unset($_SESSION['pending_2fa_admin']);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_role'] = $user['role'] ?? 'superadmin';
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid 2FA code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication | Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0;">
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center;">
        <i class="fas fa-shield-alt" style="font-size: 3rem; color: #3b82f6; margin-bottom: 1rem;"></i>
        <h2 style="margin-top: 0; color: #1e293b;">Security Check</h2>
        <p style="color: #64748b; margin-bottom: 1.5rem; font-size: 0.95rem;">Enter the 6-digit code sent to your email.</p>
        
        <?php if ($error): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; font-size: 0.9rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <div style="background-color: #eff6ff; color: #1e3a8a; padding: 0.5rem; border-radius: 4px; margin-bottom: 1rem; font-size: 0.85rem; border: 1px dashed #93c5fd;">
            <strong>Demo Mode:</strong> Your code is <?= htmlspecialchars($demo_code) ?>
        </div>
        
        <form method="POST" action="">
            <div style="margin-bottom: 1.5rem;">
                <input type="text" name="code" required placeholder="000000" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 1.5rem; text-align: center; letter-spacing: 5px;">
            </div>
            <button type="submit" style="width: 100%; padding: 0.75rem; background-color: #3b82f6; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1rem;">Verify & Login</button>
        </form>
        <div style="margin-top: 1.5rem;">
            <a href="login.php" style="color: #64748b; text-decoration: none; font-size: 0.9rem;">Cancel</a>
        </div>
    </div>
</body>
</html>
