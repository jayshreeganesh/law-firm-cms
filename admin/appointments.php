<?php
require_once 'includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$success = '';

if (isset($_GET['status']) && isset($_GET['id'])) {
    $status = $_GET['status'];
    if (in_array($status, ['pending', 'confirmed', 'cancelled'])) {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$status, (int)$_GET['id']]);
        $success = "Appointment marked as " . ucfirst($status) . ".";
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    if ($stmt->execute([(int)$_GET['id']])) {
        $success = "Appointment deleted successfully.";
        $action = 'list';
    }
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h3 style="margin: 0; color: var(--admin-primary);">Appointment Requests</h3>
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
                    <th>Requested For</th>
                    <th>Client Info</th>
                    <th>Status</th>
                    <th style="width: 250px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM appointments ORDER BY appointment_date ASC, appointment_time ASC");
                $appointments = $stmt->fetchAll();
                if (count($appointments) > 0): foreach ($appointments as $appt):
                ?>
                <tr>
                    <td>
                        <strong><?= date('M j, Y', strtotime($appt['appointment_date'])) ?></strong><br>
                        <span style="color: #64748b; font-size: 0.9rem;"><?= date('g:i A', strtotime($appt['appointment_time'])) ?></span>
                        <?php if (!empty($appt['meeting_link'])): ?>
                            <div style="margin-top: 0.5rem;">
                                <a href="<?= htmlspecialchars($appt['meeting_link']) ?>" target="_blank" style="background-color: #2563eb; color: white; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; text-decoration: none;"><i class="fas fa-video"></i> Zoom Link</a>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="font-weight: 500;"><?= htmlspecialchars($appt['name']) ?></span><br>
                        <span style="color: #64748b; font-size: 0.85rem;"><?= htmlspecialchars($appt['email']) ?> | <?= htmlspecialchars($appt['phone']) ?></span>
                    </td>
                    <td>
                        <?php if ($appt['status'] === 'pending'): ?>
                            <span style="background-color: #fef3c7; color: #d97706; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Pending</span>
                        <?php elseif ($appt['status'] === 'confirmed'): ?>
                            <span style="background-color: #d1fae5; color: #065f46; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Confirmed</span>
                        <?php else: ?>
                            <span style="background-color: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Cancelled</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($appt['status'] !== 'confirmed'): ?>
                            <a href="appointments.php?status=confirmed&id=<?= $appt['id'] ?>" class="btn-sm" style="background-color: #10b981; color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;">Confirm</a>
                        <?php endif; ?>
                        <?php if ($appt['status'] !== 'cancelled'): ?>
                            <a href="appointments.php?status=cancelled&id=<?= $appt['id'] ?>" class="btn-sm" style="background-color: #f59e0b; color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;">Cancel</a>
                        <?php endif; ?>
                        <a href="appointments.php?action=delete&id=<?= $appt['id'] ?>" class="btn-sm btn-danger" style="text-decoration: none; border-radius: 4px; display: inline-block;" onclick="return confirm('Delete this appointment record?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem;">No appointments found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
