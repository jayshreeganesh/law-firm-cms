<?php
require_once 'includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$success = '';

if ($action === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
    if ($stmt->execute([(int)$_GET['id']])) {
        $success = "Subscriber deleted successfully.";
        $action = 'list';
    }
}

if ($action === 'export') {
    $stmt = $pdo->query("SELECT email, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC");
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Clear output buffer and send headers
    ob_end_clean();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Date Subscribed']);
    
    foreach ($subscribers as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h3 style="margin: 0; color: var(--admin-primary);">Newsletter Subscribers</h3>
    <a href="newsletter.php?action=export" class="btn btn-primary" style="padding: 0.5rem 1rem; background-color: var(--admin-primary); color: white; text-decoration: none; border-radius: 4px; font-weight: 500;"><i class="fas fa-file-export"></i> Export CSV</a>
</div>

<?php if ($success): ?>
    <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Email Address</th>
                    <th>Date Subscribed</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC");
                $subs = $stmt->fetchAll();
                if (count($subs) > 0): foreach ($subs as $sub):
                ?>
                <tr>
                    <td style="font-weight: 500;"><?= htmlspecialchars($sub['email']) ?></td>
                    <td><?= date('M j, Y g:i A', strtotime($sub['subscribed_at'])) ?></td>
                    <td>
                        <a href="newsletter.php?action=delete&id=<?= $sub['id'] ?>" class="btn-sm btn-danger" style="text-decoration: none; border-radius: 4px; display: inline-block;" onclick="return confirm('Delete this subscriber?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 2rem;">No subscribers found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
