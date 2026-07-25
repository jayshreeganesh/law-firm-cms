<?php
require_once 'includes/admin_header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: messages.php");
    exit;
}

// Handle mark as read/unread
if (isset($_GET['toggle_read'])) {
    $id = (int)$_GET['toggle_read'];
    
    // get current status
    $stmt = $pdo->prepare("SELECT is_read FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    $msg = $stmt->fetch();
    
    if ($msg) {
        $new_status = $msg['is_read'] ? 0 : 1;
        $update = $pdo->prepare("UPDATE messages SET is_read = ? WHERE id = ?");
        $update->execute([$new_status, $id]);
    }
    
    header("Location: messages.php");
    exit;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$total = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$totalPages = ceil($total / $limit);

$stmt = $pdo->prepare(db_limit_offset_sql($db_driver, "SELECT * FROM messages ORDER BY created_at DESC", $limit, $offset));
$stmt->execute();
$messages = $stmt->fetchAll();
?>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($messages) > 0): foreach ($messages as $msg): ?>
                <tr style="<?= $msg['is_read'] ? '' : 'background-color: #f8fafc;' ?>">
                    <td><?= date('M j, Y g:i A', strtotime($msg['created_at'])) ?></td>
                    <td><?= htmlspecialchars($msg['name']) ?></td>
                    <td><a href="mailto:<?= htmlspecialchars($msg['email']) ?>"><?= htmlspecialchars($msg['email']) ?></a></td>
                    <td><?= htmlspecialchars($msg['subject']) ?></td>
                    <td>
                        <?php if ($msg['is_read']): ?>
                            <span style="background-color: #d1fae5; color: #065f46; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Read</span>
                        <?php else: ?>
                            <span style="background-color: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Unread</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button onclick="alert(`Message:\n<?= addslashes(htmlspecialchars($msg['message'])) ?>`)" class="btn-sm" style="background-color: var(--admin-accent); color: white; border: none; cursor: pointer; border-radius: 4px;"><i class="fas fa-eye"></i></button>
                        <a href="messages.php?toggle_read=<?= $msg['id'] ?>" class="btn-sm" style="background-color: #64748b; color: white; text-decoration: none; border-radius: 4px; display: inline-block; text-align: center;"><i class="fas <?= $msg['is_read'] ? 'fa-envelope' : 'fa-envelope-open' ?>"></i></a>
                        <a href="messages.php?delete=<?= $msg['id'] ?>" class="btn-sm btn-danger" style="text-decoration: none; border-radius: 4px; display: inline-block; text-align: center;" onclick="return confirm('Are you sure you want to delete this message?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">No messages found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($totalPages > 1): ?>
<div style="display: flex; justify-content: center; margin-bottom: 2rem; gap: 0.5rem;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="messages.php?page=<?= $i ?>" style="padding: 0.5rem 1rem; border-radius: 4px; background-color: <?= $i === $page ? 'var(--admin-accent)' : 'white' ?>; color: <?= $i === $page ? 'white' : 'var(--admin-text)' ?>; font-weight: 500; text-decoration: none; border: 1px solid #cbd5e1;"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
