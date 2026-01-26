<?php

namespace db;

use PDO;
use PDOException;

class DBClient {
    public PDO $conn;
    private string $dbName;
    
    public function __construct(string $dbName = 'demo') {
        $this->dbName = $dbName;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        try {
            $this->conn = new PDO(
                "mysql:host=db;dbname={$dbName}",
                "admin",
                "admin",
                $options
            );
        } catch (PDOException $e) {
            exit("Database connection failed: " . $e->getMessage());
        }
    }
}
