<?php

namespace db\repository;

use db\DBClient;
use Exception;
use PDO;

/**
 * @template Model
 */
abstract class BaseRepository {
    protected PDO $db;
    protected string $modelClass;
    protected string $table;
    
    public function __construct(DBClient $db) {
        $this->db = $db->conn;
    }
    
    /**
     * @return ?Model
     */
    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $sucessful = $stmt->execute(['id' => $id]);
        if(!$sucessful) {
            throw new Exception("Query failed");
        }
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->modelClass);
        return $stmt->fetch() ?: null;
    }
    
    /**
     * @return Model[]
     */
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->modelClass);
    }
    
    /**
     * @param Model $model - the data to insert
     * @return Model - the newly created data
     */
    public function create(object $model) {
        // Form the sql query
        $data = $model->getColumnValues();
        if(count($data) === 0) {
            throw new Exception("No data to insert");
        }
        $columns = implode(', ', array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        // Insert the data
        $stmt = $this->db->prepare($sql);
        $successful = $stmt->execute($data);
        if(!$successful) {
            throw new Exception("Insert failed");
        }

        // Return the new model
        $newId = (int)$this->db->lastInsertId();
        $newModel = $this->findById($newId);
        if($newModel === null) {
            throw new Exception("Failed to retrieve newly created model");
        }
        return $newModel;
    }

    /**
     * @param Model $model - the data to insert
     * @return Model - the newly created data
     */
    public function update(int $id, object $model) {
        // Form the sql query
        $data = $model->getColumnValues();
        if(count($data) === 0) {
            throw new Exception("No data to update");
        }

        $updates = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$updates} WHERE id = :id";
        
        // Update the data
        $stmt = $this->db->prepare($sql);
        $successful = $stmt->execute([...$data, 'id' => $id]);
        if(!$successful) {
            throw new Exception("Update failed");
        }

        // Return the new model
        $newModel = $this->findById($id);
        if($newModel === null) {
            throw new Exception("Failed to retrieve updated model");
        }
        return $newModel;
    }
    
    public function delete(int $id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $successful = $stmt->execute(['id' => $id]);
        if(!$successful) {
            throw new Exception("Delete failed");
        }
    }
}
