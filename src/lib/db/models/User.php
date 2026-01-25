<?php

namespace db\models;

class User extends BaseModel {
    public string $email;
    public string $password;
    
    // These defaults are only for PDO to not freak out when inserting into the model
    public function __construct(string $email = '', string $password = '') {
        $this->email = $email;
        $this->password = $password;
    }

    public function getColumnValues(): array {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
