<?php
require_once 'includes/admin_header.php';

// Get counts for dashboard
$total_practice_areas = $pdo->query("SELECT COUNT(*) FROM practice_areas")->fetchColumn();
$total_attorneys = $pdo->query("SELECT COUNT(*) FROM attorneys")->fetchColumn();
$total_messages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$total_page_views = $pdo->query("SELECT SUM(views) FROM page_views")->fetchColumn() ?: 0;
$total_appointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='pending'")->fetchColumn();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2 style="margin: 0; color: var(--admin-primary);">Dashboard Overview</h2>
    <div style="color: var(--text-light); font-weight: 500;">
        <i class="far fa-calendar-alt"></i> <?= date('F j, Y') ?>
    </div>
</div>

<div class="grid-4" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-balance-scale"></i></div>
        <div class="stat-value"><?= number_format($total_practice_areas) ?></div>
        <div class="stat-label">Practice Areas</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #dbeafe; color: #3b82f6;"><i class="fas fa-user-tie"></i></div>
        <div class="stat-value"><?= number_format($total_attorneys) ?></div>
        <div class="stat-label">Attorneys</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #fce7f3; color: #db2777;"><i class="fas fa-newspaper"></i></div>
        <div class="stat-value"><?= number_format($total_posts) ?></div>
        <div class="stat-label">Blog Posts</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #fef3c7; color: #d97706;"><i class="fas fa-envelope"></i></div>
        <div class="stat-value"><?= number_format($total_messages) ?></div>
        <div class="stat-label">Messages</div>
    </div>
</div>

<div class="grid-3" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #d1fae5; color: #059669;"><i class="fas fa-chart-line"></i></div>
        <div class="stat-value"><?= number_format($total_page_views) ?></div>
        <div class="stat-label">Total Page Views</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #e0e7ff; color: #4f46e5;"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-value"><?= number_format($total_appointments) ?></div>
        <div class="stat-label">Pending Appointments</div>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <h3 style="margin-top: 0; color: var(--admin-primary);"><i class="fas fa-chart-area"></i> Revenue & Activity Analytics</h3>
    <canvas id="revenueChart" width="100%" height="30"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Total Revenue ($)',
            data: [12000, 19000, 15000, 22000, 28000, 32000],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'New Clients',
            data: [4, 7, 5, 10, 12, 15],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: {
            y: { type: 'linear', display: true, position: 'left' },
            y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } }
        }
    }
});
</script>

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
