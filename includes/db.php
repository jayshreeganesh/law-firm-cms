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
?>
