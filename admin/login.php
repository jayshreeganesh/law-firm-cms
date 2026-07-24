<?php
require_once 'includes/admin_header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $code = sprintf("%06d", mt_rand(1, 999999));
        $pdo->prepare("UPDATE users SET two_factor_code = ? WHERE id = ?")->execute([$code, $user['id']]);
        
        // Simulate sending email
        require_once '../includes/db.php';
        $from_email = get_setting($pdo, 'smtp_from_email') ?: 'no-reply@lawfirm.local';
        @mail($user['email'] ?? 'admin@local', "Your 2FA Code", "Your login code is: $code", "From: $from_email");
        
        $_SESSION['pending_2fa_admin'] = $user['id'];
        header("Location: verify_2fa.php");
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>

<div class="login-page">
    <div class="login-card">
        <h2>Admin Login</h2>
        
        <?php if ($error): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; text-align: center;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="margin-bottom: 1.5rem;">
                <label for="username" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Username</label>
                <input type="text" id="username" name="username" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            <div style="margin-bottom: 2rem;">
                <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            <button type="submit" style="width: 100%; padding: 0.75rem 1rem; background-color: var(--admin-accent); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Login</button>
        </form>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
