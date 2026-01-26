<?php

namespace db\models;

class Session extends BaseModel {
    public int $user_id;
    public string $token;
    public string $expires_at;
    
    // These defaults are only for PDO to not freak out when inserting into the model
    public function __construct(int $user_id = 0, string $token = '', string $expires_at = '') {
        $this->user_id = $user_id;
        $this->token = $token;
        $this->expires_at = $expires_at;
    }

    public function getColumnValues(): array {
        return [
            'user_id' => $this->user_id,
            'token' => $this->token,
            'expires_at' => $this->expires_at,
        ];
    }
}
