<?php

namespace db\repository;

use db\models\History;
use db\repository\BaseRepository;
use Exception;
use PDO;

class HistoryRepository extends BaseRepository {
    protected string $table = 'history';
    protected string $modelClass = History::class;
    
    /**
     * @return History[]
     */
    public function findByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM history WHERE user_id = :user_id");
        $successful = $stmt->execute(['user_id' => $userId]);
        if(!$successful) {
            throw new Exception('Failed to fetch history');
        }
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->modelClass);
        return $stmt->fetchAll();
    }
}
