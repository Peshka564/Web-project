<?php

namespace JsonParser\Parser;

use Exception;

use JsonParser\AST\ASTNode;

class ParserResult
{
    private ASTNode | null $ok = null;
    private ParserError | null $err = null;

    public function __construct(ASTNode | ParserError $val)
    {
        if ($val instanceof ASTNode) {
            $this->ok = $val;
        } else if ($val instanceof ParserError) {
            $this->err = $val;
        } else {
            throw new Exception("Are you stupid?");
        }
    }

    public function isOk(): bool {
        return $this->ok !== null;
    } 
    
    public function isErr(): bool {
        return !$this->isOk();
    }

    public function ok(): ASTNode | null {
        return $this->ok;
    }

    public function err(): ParserError {
        return $this->err;
    }
}