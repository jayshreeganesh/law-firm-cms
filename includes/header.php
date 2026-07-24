<?php require_once 'db.php'; ?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Page View Analytics Tracking
try {
    $current_url = $_SERVER['REQUEST_URI'];
    // Filter out query parameters for cleaner analytics if desired, or keep as is
    $parsed_url = parse_url($current_url, PHP_URL_PATH);
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("INSERT INTO page_views (view_date, page_url, views) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE views = views + 1");
    $stmt->execute([$today, $parsed_url]);
} catch (Exception $e) {
    // Silently ignore if DB isn't ready
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $final_desc = isset($seo_description) && $seo_description ? $seo_description : get_setting($pdo, 'seo_description');
        $final_keywords = isset($seo_keywords) && $seo_keywords ? $seo_keywords : get_setting($pdo, 'seo_keywords');
    ?>
    <meta name="description" content="<?= htmlspecialchars($final_desc) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($final_keywords) ?>">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?><?= htmlspecialchars(get_setting($pdo, 'site_name')) ?></title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="container nav-container">
        <a href="index.php" class="logo">
            Justice<span>.</span>
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="practice-areas.php">Practice Areas</a></li>
            <li><a href="attorneys.php">Attorneys</a></li>
            <li><a href="blog.php">News</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <a href="book.php" class="btn btn-primary">Free Consultation</a>
        <button class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <!-- Multi-Language Support Widget -->
    <div style="background-color: #f1f5f9; padding: 5px 0; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">
        <div class="container" style="display: flex; justify-content: flex-end;">
            <div id="google_translate_element"></div>
        </div>
    </div>
</header>

<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
