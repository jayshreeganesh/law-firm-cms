<?php
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_driver = $_POST['db_driver'];
    $db_host   = $_POST['db_host'];
    $db_name   = $_POST['db_name'];
    $db_user   = $_POST['db_user'];
    $db_pass   = $_POST['db_pass'];
    $dummy_data = isset($_POST['dummy_data']) ? true : false;

    $sqlite_path = __DIR__ . '/database.sqlite';
    
    try {
        // Build DSN
        switch (strtolower($db_driver)) {
            case 'mysql':
            case 'mariadb':
                $dsn = "mysql:host=$db_host;charset=utf8mb4"; // Connect without dbname first to create it
                $pdo = new PDO($dsn, $db_user, $db_pass);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
                $pdo->exec("USE `$db_name`");
                
                $auto_inc = "INT AUTO_INCREMENT PRIMARY KEY";
                $timestamp = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
                $boolean = "BOOLEAN";
                $enum = "ENUM('client', 'admin') DEFAULT 'client'";
                $enum_status = "ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending'";
                break;
            case 'sqlite':
                $dsn = "sqlite:$sqlite_path";
                $pdo = new PDO($dsn);
                $auto_inc = "INTEGER PRIMARY KEY AUTOINCREMENT";
                $timestamp = "DATETIME DEFAULT CURRENT_TIMESTAMP";
                $boolean = "INTEGER";
                $enum = "TEXT DEFAULT 'client'";
                $enum_status = "TEXT DEFAULT 'pending'";
                break;
            case 'pgsql':
                $dsn = "pgsql:host=$db_host;dbname=$db_name";
                $pdo = new PDO($dsn, $db_user, $db_pass);
                $auto_inc = "SERIAL PRIMARY KEY";
                $timestamp = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
                $boolean = "BOOLEAN";
                $enum = "VARCHAR(50) DEFAULT 'client'";
                $enum_status = "VARCHAR(50) DEFAULT 'pending'";
                break;
            case 'sqlsrv':
                $dsn = "sqlsrv:Server=$db_host;Database=$db_name";
                $pdo = new PDO($dsn, $db_user, $db_pass);
                $auto_inc = "INT IDENTITY(1,1) PRIMARY KEY";
                $timestamp = "DATETIME DEFAULT GETDATE()";
                $boolean = "BIT";
                $enum = "VARCHAR(50) DEFAULT 'client'";
                $enum_status = "VARCHAR(50) DEFAULT 'pending'";
                break;
            case 'oci':
                $dsn = "oci:dbname=//$db_host/$db_name;charset=AL32UTF8";
                $pdo = new PDO($dsn, $db_user, $db_pass);
                $auto_inc = "NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY";
                $timestamp = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
                $boolean = "NUMBER(1)";
                $enum = "VARCHAR2(50) DEFAULT 'client'";
                $enum_status = "VARCHAR2(50) DEFAULT 'pending'";
                break;
            default:
                throw new Exception("Unsupported driver");
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // CREATE TABLES
        $queries = [
            "CREATE TABLE users (
                id $auto_inc,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100) NOT NULL,
                created_at $timestamp
            )",
            "CREATE TABLE practice_areas (
                id $auto_inc,
                title VARCHAR(100) NOT NULL,
                description TEXT,
                icon VARCHAR(50) DEFAULT 'fas fa-balance-scale',
                created_at $timestamp
            )",
            "CREATE TABLE attorneys (
                id $auto_inc,
                name VARCHAR(100) NOT NULL,
                position VARCHAR(100),
                bio TEXT,
                image VARCHAR(255),
                email VARCHAR(100),
                phone VARCHAR(50),
                created_at $timestamp
            )",
            "CREATE TABLE messages (
                id $auto_inc,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(50),
                subject VARCHAR(200),
                message TEXT NOT NULL,
                is_read $boolean DEFAULT 0,
                created_at $timestamp
            )",
            "CREATE TABLE posts (
                id $auto_inc,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                image VARCHAR(255),
                created_at $timestamp,
                updated_at $timestamp
            )",
            "CREATE TABLE settings (
                setting_key VARCHAR(50) PRIMARY KEY,
                setting_value TEXT
            )",
            "CREATE TABLE case_results (
                id $auto_inc,
                title VARCHAR(255) NOT NULL,
                amount VARCHAR(50),
                case_type VARCHAR(100),
                description TEXT,
                created_at $timestamp
            )",
            "CREATE TABLE clients (
                id $auto_inc,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at $timestamp
            )",
            "CREATE TABLE newsletter_subscribers (
                id $auto_inc,
                email VARCHAR(100) NOT NULL UNIQUE,
                subscribed_at $timestamp
            )"
        ];

        // Drop existing tables if they exist to avoid conflict (basic implementation)
        $tables = ['newsletter_subscribers', 'clients', 'case_results', 'settings', 'posts', 'messages', 'attorneys', 'practice_areas', 'users'];
        foreach ($tables as $table) {
            try { $pdo->exec("DROP TABLE $table"); } catch (Exception $e) {}
        }

        // Execute table creations
        foreach ($queries as $q) {
            $pdo->exec($q);
        }

        // 1. Initial Seeder (Required Data)
        $password_hash = password_hash('admin123', PASSWORD_BCRYPT);
        $pdo->exec("INSERT INTO users (username, password, email) VALUES ('admin', '$password_hash', 'admin@lawyer-cms.local')");
        
        $pdo->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('site_name', 'Justice & Partners')");
        $pdo->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('site_email', 'contact@justicepartners.com')");
        $pdo->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('site_phone', '+1 (555) 123-4567')");
        $pdo->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('site_address', '123 Legal Avenue, Suite 100, New York, NY 10001')");

        // 2. Faker Dummy Data (Optional)
        if ($dummy_data) {
            $pdo->exec("INSERT INTO practice_areas (title, description, icon) VALUES ('Corporate Law', 'Comprehensive legal solutions for businesses of all sizes.', 'fas fa-briefcase')");
            $pdo->exec("INSERT INTO practice_areas (title, description, icon) VALUES ('Family Law', 'Compassionate representation in family matters.', 'fas fa-users')");
            
            $pdo->exec("INSERT INTO case_results (title, amount, case_type, description) VALUES ('Major Corporate Merger', '$5M', 'Corporate', 'Successfully negotiated and finalized a complex merger between two tech giants.')");
            
            $pdo->exec("INSERT INTO posts (title, content) VALUES ('New Changes to Family Law', 'Here are the latest updates to family law that you should know about...')");
            
            $pdo->exec("INSERT INTO attorneys (name, position, bio) VALUES ('Jane Doe', 'Senior Partner', 'Jane has over 20 years of experience in corporate law.')");
        }

        // Save Configuration to config.php
        $config_content = "<?php\n"
            . "\$db_driver = '$db_driver';\n"
            . "\$db_host = '$db_host';\n"
            . "\$db_name = '$db_name';\n"
            . "\$db_user = '$db_user';\n"
            . "\$db_pass = '$db_pass';\n";
            
        file_put_contents(__DIR__ . '/includes/config.php', $config_content);

        $message = "<div style='color: green; font-weight: bold;'>Installation Successful! You can now visit the <a href='index.php'>Home Page</a>. (Default Admin login: admin / admin123)</div>";

    } catch (Exception $e) {
        $message = "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Setup Wizard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .setup-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.5rem; }
        input, select { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { display: inline-block; width: 100%; background: #2563eb; color: white; padding: 1rem; border: none; border-radius: 4px; font-size: 1.1rem; cursor: pointer; }
        .btn:hover { background: #1d4ed8; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        .checkbox-group input { width: auto; }
    </style>
</head>
<body>
    <div class="setup-container">
        <h2>Setup Wizard</h2>
        <p>Configure your database to install the CMS.</p>
        
        <?= $message ?>
        
        <form method="POST" action="setup.php">
            <div class="form-group">
                <label>Database Type</label>
                <select name="db_driver" id="db_driver" onchange="toggleFields()">
                    <option value="mysql">MySQL</option>
                    <option value="mariadb">MariaDB</option>
                    <option value="sqlite">SQLite</option>
                    <option value="pgsql">PostgreSQL</option>
                    <option value="sqlsrv">MS SQL Server</option>
                    <option value="oci">Oracle</option>
                </select>
            </div>
            
            <div id="connection_fields">
                <div class="form-group">
                    <label>Database Host</label>
                    <input type="text" name="db_host" value="localhost">
                </div>
                <div class="form-group">
                    <label>Database Name</label>
                    <input type="text" name="db_name" value="lawyer_cms">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="db_user" value="root">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="db_pass">
                </div>
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" name="dummy_data" id="dummy_data" checked>
                <label for="dummy_data" style="margin-bottom:0; font-weight: normal;">Generate Dummy Content (Faker)</label>
            </div>
            
            <button type="submit" class="btn">Install Database</button>
        </form>
    </div>

    <script>
        function toggleFields() {
            var driver = document.getElementById('db_driver').value;
            var fields = document.getElementById('connection_fields');
            if (driver === 'sqlite') {
                fields.style.display = 'none';
            } else {
                fields.style.display = 'block';
            }
        }
    </script>
</body>
</html>
