<?php
session_start();

$message = '';

$config_file = __DIR__ . '/includes/config.php';
$sqlite_path = __DIR__ . '/database.sqlite';
$already_setup = file_exists($config_file);

if (isset($_POST['reset_setup']) && $already_setup) {
    $ts = date('Ymd_His');
    rename($config_file, __DIR__ . '/includes/config.backup_' . $ts . '.php');
    if (file_exists($sqlite_path)) {
        rename($sqlite_path, __DIR__ . '/database.backup_' . $ts . '.sqlite');
    }
    header("Location: setup.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$already_setup) {
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
        
        $schema_file = __DIR__ . "/database/schema/" . strtolower($db_driver) . ".sql";
        if (file_exists($schema_file)) {
            $sql = file_get_contents($schema_file);
            $queries = array_filter(array_map("trim", explode(";", $sql)));
        } else {
            throw new Exception("Schema file not found for driver: " . $db_driver);
        }


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

        $message = "
            <div style='color: green; font-weight: bold; text-align: center; margin-bottom: 1rem;'>
                Installation Successful! Redirecting to Admin Dashboard...
            </div>
            <div style='background: #f8fafc; padding: 1rem; border-radius: 4px; text-align: center; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;'>
                <p style='margin: 0 0 0.5rem 0; color: #64748b;'>Save these default credentials:</p>
                <strong style='font-size: 1.2rem; color: #0f172a;'>Username: admin</strong><br>
                <strong style='font-size: 1.2rem; color: #0f172a;'>Password: admin123</strong>
            </div>
            <a href='admin/login.php' class='btn' style='display: block; text-align: center; text-decoration: none;'>Go to Admin Login Now</a>
            <meta http-equiv='refresh' content='4;url=admin/login.php'>
        ";

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
        
        <?php if ($already_setup): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <strong>Warning:</strong> The application is already set up and configured.
            </div>
            <p>If you need to start over, you can reset the setup. This will backup your current <code>config.php</code> and database (if using SQLite) so you don't lose your previous configuration.</p>
            <form method="POST" action="setup.php">
                <input type="hidden" name="reset_setup" value="1">
                <button type="submit" class="btn" style="background: #ef4444;" onclick="return confirm('Are you sure you want to backup your config and reset the installation?');">Backup & Reset Setup</button>
            </form>
        <?php else: ?>
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
        <?php endif; ?>
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
