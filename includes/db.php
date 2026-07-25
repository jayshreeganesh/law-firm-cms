<?php
session_start();

// Database Configuration
$config_file = __DIR__ . '/config.php';
if (!file_exists($config_file)) {
    header("Location: /setup.php");
    exit;
}

require_once $config_file;

// SQLite specific config
$sqlite_path = __DIR__ . '/../database.sqlite'; // Path to SQLite database file

try {
    // Build DSN based on the selected driver
    switch (strtolower($db_driver)) {
        case 'mysql':
        case 'mariadb':
            // Supports MySQL and MariaDB
            $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
            $pdo = new PDO($dsn, $db_user, $db_pass);
            break;
        case 'sqlite':
            // Supports SQLite
            $dsn = "sqlite:$sqlite_path";
            $pdo = new PDO($dsn);
            break;
        case 'sqlsrv':
            // Supports Microsoft SQL Server
            $dsn = "sqlsrv:Server=$db_host;Database=$db_name";
            $pdo = new PDO($dsn, $db_user, $db_pass);
            break;
        case 'oci':
            // Supports Oracle
            $dsn = "oci:dbname=//$db_host/$db_name;charset=AL32UTF8";
            $pdo = new PDO($dsn, $db_user, $db_pass);
            break;
        case 'pgsql':
            // Supports PostgreSQL
            $dsn = "pgsql:host=$db_host;dbname=$db_name";
            $pdo = new PDO($dsn, $db_user, $db_pass);
            break;
        default:
            throw new Exception("Unsupported database driver: " . $db_driver);
    }
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function get_setting($pdo, $key) {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : '';
}

function db_limit_offset_sql($driver, $sql, $limit, $offset = 0) {
    $driver = strtolower($driver);
    if ($driver === 'sqlsrv' || $driver === 'oci') {
        return $sql . " OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY";
    } else {
        return $sql . " LIMIT $limit OFFSET $offset";
    }
}

function db_upsert_page_view($pdo, $driver, $date, $url) {
    $driver = strtolower($driver);
    try {
        if ($driver === 'mysql' || $driver === 'mariadb') {
            $stmt = $pdo->prepare("INSERT INTO page_views (view_date, page_url, views) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE views = views + 1");
            $stmt->execute([$date, $url]);
        } elseif ($driver === 'sqlite' || $driver === 'pgsql') {
            $stmt = $pdo->prepare("INSERT INTO page_views (view_date, page_url, views) VALUES (?, ?, 1) ON CONFLICT(view_date, page_url) DO UPDATE SET views = page_views.views + 1");
            $stmt->execute([$date, $url]);
        } elseif ($driver === 'sqlsrv' || $driver === 'oci') {
            $stmt = $pdo->prepare("MERGE INTO page_views target USING (SELECT ? as v_date, ? as v_url) source ON (target.view_date = source.v_date AND target.page_url = source.v_url) WHEN MATCHED THEN UPDATE SET views = views + 1 WHEN NOT MATCHED THEN INSERT (view_date, page_url, views) VALUES (source.v_date, source.v_url, 1);");
            $stmt->execute([$date, $url]);
        }
    } catch (Exception $e) {}
}
?>
