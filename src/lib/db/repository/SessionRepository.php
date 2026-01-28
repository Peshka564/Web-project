<?php

namespace db\repository;

use DateTime;
use db\models\Session;
use db\models\User;
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
    
    public function createSessionForUser(User $user): Session {
        $token = bin2hex(random_bytes(32));
        $userId = $user->id;
        $expires_at = (new DateTime())->modify('+2 days');
        $expires_at = $expires_at->format('Y-m-d H:i:s');
        $newSession = new Session($userId, $token, $expires_at);

        $newSession = $this->create($newSession);
        return $newSession;
    }

    public function deleteSessionForUser(string $session_token) {
        $userSession = $this->findByToken($session_token);
        if(!$userSession) {
            throw new Exception('Invalid user token');
        }
        $this->delete($userSession->id);
    }
}
