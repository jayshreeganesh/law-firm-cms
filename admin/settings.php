<?php
require_once 'includes/admin_header.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => $_POST['site_name'] ?? '',
        'site_email' => $_POST['site_email'] ?? '',
        'site_phone' => $_POST['site_phone'] ?? '',
        'site_address' => $_POST['site_address'] ?? ''
    ];
    
    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    $success = "Settings updated successfully.";
}
?>

<div class="card" style="max-width: 600px;">
    <h3 style="margin-top: 0; color: var(--admin-primary);">Site Settings</h3>
    
    <?php if ($success): ?>
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Site Name</label>
            <input type="text" name="site_name" value="<?= htmlspecialchars(get_setting($pdo, 'site_name')) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Contact Email</label>
            <input type="email" name="site_email" value="<?= htmlspecialchars(get_setting($pdo, 'site_email')) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Contact Phone</label>
            <input type="text" name="site_phone" value="<?= htmlspecialchars(get_setting($pdo, 'site_phone')) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Office Address</label>
            <textarea name="site_address" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit; resize: vertical;"><?= htmlspecialchars(get_setting($pdo, 'site_address')) ?></textarea>
        </div>
        
        <button type="submit" style="padding: 0.75rem 1.5rem; background-color: var(--admin-accent); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Save Settings</button>
    </form>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
