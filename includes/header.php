<?php require_once 'db.php'; ?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Offline Localization System
if (isset($_GET['lang'])) {
    $allowed_langs = ['en', 'as', 'bn', 'brx', 'doi', 'gu', 'hi', 'kn', 'ks', 'gom', 'mai', 'ml', 'mni', 'mr', 'ne', 'or', 'pa', 'sa', 'sat', 'sd', 'ta', 'te', 'ur'];
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
                <select onchange="window.location.href='?lang='+this.value" style="padding: 2px 5px; border-radius: 4px; border: 1px solid #cbd5e1; font-size: 0.85rem;">
                    <option value="en" <?= $current_lang == 'en' ? 'selected' : '' ?>>English</option>
                    <option value="as" <?= $current_lang == 'as' ? 'selected' : '' ?>>অসমীয়া (Assamese)</option>
                    <option value="bn" <?= $current_lang == 'bn' ? 'selected' : '' ?>>বাংলা (Bengali)</option>
                    <option value="brx" <?= $current_lang == 'brx' ? 'selected' : '' ?>>बर’ (Bodo)</option>
                    <option value="doi" <?= $current_lang == 'doi' ? 'selected' : '' ?>>डोगरी (Dogri)</option>
                    <option value="gu" <?= $current_lang == 'gu' ? 'selected' : '' ?>>ગુજરાતી (Gujarati)</option>
                    <option value="hi" <?= $current_lang == 'hi' ? 'selected' : '' ?>>हिन्दी (Hindi)</option>
                    <option value="kn" <?= $current_lang == 'kn' ? 'selected' : '' ?>>ಕನ್ನಡ (Kannada)</option>
                    <option value="ks" <?= $current_lang == 'ks' ? 'selected' : '' ?>>کٲشُر (Kashmiri)</option>
                    <option value="gom" <?= $current_lang == 'gom' ? 'selected' : '' ?>>कोंकणी (Konkani)</option>
                    <option value="mai" <?= $current_lang == 'mai' ? 'selected' : '' ?>>मैथिली (Maithili)</option>
                    <option value="ml" <?= $current_lang == 'ml' ? 'selected' : '' ?>>മലയാളം (Malayalam)</option>
                    <option value="mni" <?= $current_lang == 'mni' ? 'selected' : '' ?>>মৈতৈলোন (Manipuri)</option>
                    <option value="mr" <?= $current_lang == 'mr' ? 'selected' : '' ?>>मराठी (Marathi)</option>
                    <option value="ne" <?= $current_lang == 'ne' ? 'selected' : '' ?>>नेपाली (Nepali)</option>
                    <option value="or" <?= $current_lang == 'or' ? 'selected' : '' ?>>ଓଡ଼ିଆ (Odia)</option>
                    <option value="pa" <?= $current_lang == 'pa' ? 'selected' : '' ?>>ਪੰਜਾਬੀ (Punjabi)</option>
                    <option value="sa" <?= $current_lang == 'sa' ? 'selected' : '' ?>>संस्कृतम् (Sanskrit)</option>
                    <option value="sat" <?= $current_lang == 'sat' ? 'selected' : '' ?>>ᱥᱟᱱᱛᱟᱲᱤ (Santali)</option>
                    <option value="sd" <?= $current_lang == 'sd' ? 'selected' : '' ?>>سنڌي (Sindhi)</option>
                    <option value="ta" <?= $current_lang == 'ta' ? 'selected' : '' ?>>தமிழ் (Tamil)</option>
                    <option value="te" <?= $current_lang == 'te' ? 'selected' : '' ?>>తెలుగు (Telugu)</option>
                    <option value="ur" <?= $current_lang == 'ur' ? 'selected' : '' ?>>اردو (Urdu)</option>
                </select>
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
