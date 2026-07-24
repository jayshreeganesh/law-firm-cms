<?php
require_once 'includes/admin_header.php';

if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'superadmin') {
    die("Unauthorized Access. Superadmin only.");
}

$stmt = $pdo->query("SELECT a.*, u.username, u.role FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 500");
$logs = $stmt->fetchAll();
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin-top: 0; color: var(--admin-primary); margin-bottom: 0;"><i class="fas fa-list-alt"></i> System Audit Trail</h3>
        <a href="backup.php" class="btn btn-primary" style="background-color: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-weight: 600;"><i class="fas fa-database"></i> Download Database Backup</a>
    </div>
    
    <div class="table-responsive">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="background-color: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                    <th style="padding: 0.75rem; text-align: left;">Timestamp</th>
                    <th style="padding: 0.75rem; text-align: left;">User</th>
                    <th style="padding: 0.75rem; text-align: left;">Action</th>
                    <th style="padding: 0.75rem; text-align: left;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($logs) > 0): foreach ($logs as $log): ?>
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 0.75rem; color: #64748b;"><?= date('M j, Y H:i:s', strtotime($log['created_at'])) ?></td>
                    <td style="padding: 0.75rem;">
                        <strong><?= htmlspecialchars($log['username'] ?? 'System / Deleted') ?></strong><br>
                        <span style="font-size: 0.8rem; color: #94a3b8;"><?= htmlspecialchars($log['role'] ?? '') ?></span>
                    </td>
                    <td style="padding: 0.75rem; font-weight: 500;"><?= htmlspecialchars($log['action']) ?></td>
                    <td style="padding: 0.75rem; font-family: monospace; color: #475569;"><?= htmlspecialchars($log['ip_address']) ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem; color: #64748b;">No audit logs found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
