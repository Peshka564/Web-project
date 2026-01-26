<?php

namespace db\repository;

use db\models\User;
use db\repository\BaseRepository;
use Exception;
use PDO;

class UserRepository extends BaseRepository {
    protected string $table = 'users';
    protected string $modelClass = User::class;
    
    public function findByUsername(string $username): ?User {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $successful = $stmt->execute(['username' => $username]);
        if(!$successful) {
            throw new Exception('Failed to find');
        }
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->modelClass);
        return $stmt->fetch() ?: null;
    }
}
