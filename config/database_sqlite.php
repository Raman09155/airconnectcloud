<?php
// SQLite Database - No MySQL needed
class Database {
    private $db_file = 'contact_data.db';
    private $conn;

    public function getConnection() {
        try {
            $this->conn = new PDO("sqlite:" . __DIR__ . "/../" . $this->db_file);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTable();
        } catch(PDOException $e) {
            error_log("Connection error: " . $e->getMessage());
        }
        return $this->conn;
    }
    
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS form_submissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT NOT NULL,
            company TEXT NOT NULL,
            description TEXT,
            ip_address TEXT,
            user_agent TEXT,
            submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->conn->exec($sql);
    }
}
?>