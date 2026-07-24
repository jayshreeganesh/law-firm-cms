<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'superadmin') {
    http_response_code(403);
    exit("Unauthorized");
}

$db_host = 'localhost'; // Should ideally pull from env
$db_user = 'root';
$db_pass = '';
$db_name = 'law_firm_cms';

$backup_file = $db_name . '_' . date("Y-m-d_H-i-s") . '.sql';

header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename=' . $backup_file);

// Log backup action
$stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, ip_address) VALUES (?, ?, ?)");
$stmt->execute([$_SESSION['admin_id'], 'Downloaded Database Backup', $_SERVER['REMOTE_ADDR']]);

// This requires mysqldump to be in PATH. For cross-platform fallback, we can use a basic PHP export loop.
$command = "mysqldump --user={$db_user} --password={$db_pass} --host={$db_host} {$db_name}";
$output = shell_exec($command);

if ($output) {
    echo $output;
} else {
    // Basic PHP fallback if mysqldump is not available
    $tables = [];
    $result = $pdo->query("SHOW TABLES");
    while($row = $result->fetch(PDO::FETCH_NUM)){
        $tables[] = $row[0];
    }
    
    $sql = "-- Database Backup for {$db_name}\n\n";
    foreach($tables as $table){
        $result = $pdo->query("SELECT * FROM {$table}");
        $numCols = $result->columnCount();
        
        $sql .= "DROP TABLE IF EXISTS {$table};\n";
        $row2 = $pdo->query("SHOW CREATE TABLE {$table}")->fetch(PDO::FETCH_NUM);
        $sql .= $row2[1].";\n\n";
        
        while($row = $result->fetch(PDO::FETCH_NUM)){
            $sql .= "INSERT INTO {$table} VALUES(";
            for($j=0; $j<$numCols; $j++){
                $row[$j] = addslashes($row[$j]);
                $row[$j] = str_replace("\n","\\n",$row[$j]);
                if(isset($row[$j])){
                    $sql .= '"'.$row[$j].'"';
                }else{
                    $sql .= '""';
                }
                if($j<($numCols-1)){
                    $sql .= ',';
                }
            }
            $sql .= ");\n";
        }
        $sql .= "\n\n";
    }
    echo $sql;
}
exit;
