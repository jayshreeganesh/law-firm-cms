<?php
require_once 'includes/admin_header.php';

// Get counts for dashboard
$msg_stmt = $pdo->query("SELECT COUNT(*) FROM messages");
$total_messages = $msg_stmt->fetchColumn();

$unread_stmt = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0");
$unread_messages = $unread_stmt->fetchColumn();

$att_stmt = $pdo->query("SELECT COUNT(*) FROM attorneys");
$total_attorneys = $att_stmt->fetchColumn();

$prac_stmt = $pdo->query("SELECT COUNT(*) FROM practice_areas");
$total_practice_areas = $prac_stmt->fetchColumn();
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="border-left: 4px solid var(--admin-accent);">
        <h3 style="color: #64748b; font-size: 1rem; margin-bottom: 0.5rem;">Total Messages</h3>
        <p style="font-size: 2rem; font-weight: bold; margin: 0; color: var(--admin-primary);"><?= $total_messages ?></p>
    </div>
    
    <div class="card" style="border-left: 4px solid var(--admin-danger);">
        <h3 style="color: #64748b; font-size: 1rem; margin-bottom: 0.5rem;">Unread Messages</h3>
        <p style="font-size: 2rem; font-weight: bold; margin: 0; color: var(--admin-primary);"><?= $unread_messages ?></p>
    </div>

    <div class="card" style="border-left: 4px solid var(--admin-success);">
        <h3 style="color: #64748b; font-size: 1rem; margin-bottom: 0.5rem;">Attorneys</h3>
        <p style="font-size: 2rem; font-weight: bold; margin: 0; color: var(--admin-primary);"><?= $total_attorneys ?></p>
    </div>

    <div class="card" style="border-left: 4px solid var(--admin-primary);">
        <h3 style="color: #64748b; font-size: 1rem; margin-bottom: 0.5rem;">Practice Areas</h3>
        <p style="font-size: 2rem; font-weight: bold; margin: 0; color: var(--admin-primary);"><?= $total_practice_areas ?></p>
    </div>
</div>

<div class="card">
    <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: var(--admin-primary);">Recent Messages</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");
                $recent_messages = $recent->fetchAll();
                
                if (count($recent_messages) > 0):
                    foreach ($recent_messages as $msg):
                ?>
                <tr>
                    <td><?= date('M j, Y', strtotime($msg['created_at'])) ?></td>
                    <td><?= htmlspecialchars($msg['name']) ?></td>
                    <td><?= htmlspecialchars($msg['subject']) ?></td>
                    <td>
                        <?php if ($msg['is_read']): ?>
                            <span style="background-color: #d1fae5; color: #065f46; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Read</span>
                        <?php else: ?>
                            <span style="background-color: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Unread</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php 
                    endforeach;
                else: 
                ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No recent messages.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top: 1rem; text-align: right;">
        <a href="messages.php" style="color: var(--admin-accent); font-weight: 500; text-decoration: none;">View All Messages &rarr;</a>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
