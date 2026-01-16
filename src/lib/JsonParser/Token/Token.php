<?php

namespace JsonParser\Token;

class Token
{
    private TokenType $type;
    private string $literal;
    private int $row;
    private int $col;

    public function __construct(TokenType $type, string $literal, int $row, int $col)
    {
        $this->type = $type;
        $this->literal = $literal;
        $this->row = $row;
        $this->col = $col;
    }

    public function getType(): TokenType
    {
        return $this->type;
    }

    public function getLiteral(): string
    {
        return $this->literal;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function getCol(): int
    {
        return $this->col;
    }

    public function __toString(): string
    {
        return "{ type=" . $this->type->toString() . ", literal=" . $this->literal . ", row=" . $this->row . " col=" . $this->col . " }";
    }
}