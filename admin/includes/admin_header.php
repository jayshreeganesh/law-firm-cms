<?php
require_once '../includes/db.php';

// Check if user is logged in (unless on login page)
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php' && !isset($_SESSION['admin_logged_in'])) {
    $base_url = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: $base_url/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?= htmlspecialchars(get_setting($pdo, 'site_name')) ?></title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php if ($current_page !== 'login.php'): ?>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="index.php" class="sidebar-brand">
            Justice CMS
        </a>
        <ul class="sidebar-nav">
            <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="practice_areas.php" class="<?= $current_page == 'practice_areas.php' ? 'active' : '' ?>"><i class="fas fa-balance-scale"></i> Practice Areas</a></li>
            <li><a href="attorneys.php" class="<?= $current_page == 'attorneys.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Attorneys</a></li>
            <li><a href="messages.php" class="<?= $current_page == 'messages.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Messages</a></li>
            <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h2 style="margin: 0; color: var(--admin-primary); font-size: 1.5rem;">
                <?php 
                if($current_page == 'index.php') echo 'Dashboard';
                elseif($current_page == 'messages.php') echo 'Messages';
                ?>
            </h2>
            <div>
                Logged in as <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
            </div>
        </div>
<?php endif; ?>
