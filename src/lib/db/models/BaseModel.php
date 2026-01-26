<?php

namespace db\models;

abstract class BaseModel {
    public ?int $id;
    public ?string $created_at;
    public ?string $updated_at;

    abstract public function getColumnValues();
}
