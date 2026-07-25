<?php require_once 'db.php'; ?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Offline Localization System
if (isset($_GET['lang'])) {
    $allowed_langs = ['en', 'hi', 'mr', 'gu'];
    if (in_array($_GET['lang'], $allowed_langs)) {
        $_SESSION['app_lang'] = $_GET['lang'];
    }
    // Redirect to remove ?lang= from url
    $url = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: $url");
    exit;
}
$current_lang = $_SESSION['app_lang'] ?? 'en';
$lang_file = __DIR__ . "/../lang/{$current_lang}.php";
if (file_exists($lang_file)) {
    require_once $lang_file;
} else {
    require_once __DIR__ . "/../lang/en.php";
}

// Page View Analytics Tracking
try {
    $current_url = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $parsed_url = parse_url($current_url, PHP_URL_PATH);
    $today = date('Y-m-d');
    
    if (isset($db_driver)) {
        db_upsert_page_view($pdo, $db_driver, $today, $parsed_url);
    }
} catch (Exception $e) {
    // Silently ignore if DB isn't ready
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
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
            <li><a href="index.php"><?= $lang['home'] ?></a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="practice-areas.php"><?= $lang['practice_areas'] ?></a></li>
            <li><a href="attorneys.php"><?= $lang['our_attorneys'] ?></a></li>
            <li><a href="blog.php"><?= $lang['news'] ?></a></li>
            <li><a href="contact.php"><?= $lang['contact'] ?></a></li>
        </ul>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button id="darkModeToggleFront" class="btn" style="background: none; border: none; font-size: 1.25rem; color: var(--text-color); cursor: pointer;">
                <i class="fas fa-moon"></i>
            </button>
            <a href="book.php" class="btn btn-primary"><?= $lang['free_consultation'] ?></a>
        </div>
        <button class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <!-- Multi-Language Support Widget -->
    <div style="background-color: #f1f5f9; padding: 5px 0; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <strong>Native Offline Languages:</strong>
                <a href="?lang=en" style="text-decoration: none; color: <?= $current_lang == 'en' ? 'var(--secondary-color)' : '#64748b' ?>; font-weight: <?= $current_lang == 'en' ? 'bold' : 'normal' ?>;">English</a> |
                <a href="?lang=hi" style="text-decoration: none; color: <?= $current_lang == 'hi' ? 'var(--secondary-color)' : '#64748b' ?>; font-weight: <?= $current_lang == 'hi' ? 'bold' : 'normal' ?>;">हिन्दी (Hindi)</a> |
                <a href="?lang=mr" style="text-decoration: none; color: <?= $current_lang == 'mr' ? 'var(--secondary-color)' : '#64748b' ?>; font-weight: <?= $current_lang == 'mr' ? 'bold' : 'normal' ?>;">मराठी (Marathi)</a> |
                <a href="?lang=gu" style="text-decoration: none; color: <?= $current_lang == 'gu' ? 'var(--secondary-color)' : '#64748b' ?>; font-weight: <?= $current_lang == 'gu' ? 'bold' : 'normal' ?>;">ગુજરાતી (Gujarati)</a>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="color: #64748b;">Or translate to any language via Google:</span>
                <div id="google_translate_element"></div>
            </div>
        </div>
    </div>
</header>

<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
