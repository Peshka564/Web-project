<?php

namespace db;

use PDO;
use PDOException;

class DBClient
{
    public PDO $conn;
    private string $dbName;

    public function __construct(string $host, int $port, string $dbName, string $user, string $pass)
    {
        $this->dbName = $dbName;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        try {
            $this->conn = new PDO(
                "mysql:host={$host}:{$port};dbname={$dbName}",
                $user,
                $pass,
                $options
            );
        } catch (PDOException $e) {
            exit("Database connection failed: " . $e->getMessage());
        }
    }
}
