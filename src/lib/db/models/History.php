<?php

namespace db\models;

class History extends BaseModel {
    public int $user_id;
    public string $name;
    public ?string $description;
    public string $input_data_path;
    public ?string $s_expression_path;
    public string $executed_at;
    
    // These defaults are only for PDO to not freak out when inserting into the model
    public function __construct(
        int $user_id = 0,
        string $name = '',
        ?string $description = null,
        string $input_data_path = '',
        ?string $s_expression_path = null,
        // string $executed_at = ''
    ) {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->description = $description;
        $this->input_data_path = $input_data_path;
        $this->s_expression_path = $s_expression_path;
        // $this->executed_at = $executed_at;
    }

    public function getColumnValues(): array {
        return [
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'input_data_path' => $this->input_data_path,
            's_expression_path' => $this->s_expression_path,
            // 'executed_at' => $this->executed_at,
        ];
    }
}
