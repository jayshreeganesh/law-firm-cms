<?php
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    
    public function testDatabaseHelperFunctions() {
        // We will include db.php to test the limit offset helper function
        require_once __DIR__ . '/../includes/db.php';
        
        $sql = "SELECT * FROM users";
        
        // Test standard offset format (MySQL, SQLite)
        $mysql_sql = db_limit_offset_sql('mysql', $sql, 10, 5);
        $this->assertEquals("SELECT * FROM users LIMIT 10 OFFSET 5", $mysql_sql);
        
        // Test ANSI SQL fetch format (SQL Server, Oracle)
        $oracle_sql = db_limit_offset_sql('oci', $sql, 10, 5);
        $this->assertEquals("SELECT * FROM users OFFSET 5 ROWS FETCH NEXT 10 ROWS ONLY", $oracle_sql);
    }
}
