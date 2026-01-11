<?php

namespace JsonParser\AST;

use JsonParser\Token\Token;

abstract class LeafNode implements ASTNode
{
    private Token $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }
    
    public function getToken(): Token{
        return $this->token;
    }

    public function isLeafNode(): bool {
        return true;
    }
}