<?php

namespace db\models;

class User extends BaseModel {
    public string $username;
    public string $password;
    
    // These defaults are only for PDO to not freak out when inserting into the model
    public function __construct(string $username = '', string $password = '') {
        $this->username = $username;
        $this->password = $password;
    }

    public function getColumnValues(): array {
        return [
            'username' => $this->username,
            'password' => $this->password,
        ];
    }
}
