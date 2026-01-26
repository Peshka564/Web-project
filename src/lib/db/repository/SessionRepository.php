<?php

namespace db\repository;

use db\models\Session;
use db\repository\BaseRepository;
use Exception;
use PDO;

class SessionRepository extends BaseRepository {
    protected string $table = 'sessions';
    protected string $modelClass = Session::class;
    
    public function findByToken(string $token): ?Session {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE token = :token");
        $successful = $stmt->execute(['token' => $token]);
        if(!$successful) {
            throw new Exception('Failed to find');
        }
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->modelClass);
        return $stmt->fetch() ?: null;
    }
    
}
