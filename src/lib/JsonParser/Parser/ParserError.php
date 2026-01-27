<?php

namespace JsonParser\Parser;

use JsonParser\Token\Token;

class ParserError
{
    private Token | null $token;
    private string $msg;

    public function __construct(string $msg, Token | null $token = null) 
    {
        $this->msg = $msg;
        $this->token = $token;
    }

    public function hasToken(): bool {
        return $this->token !== null;
    }

    public function getToken(): Token | null{
        return $this->token;
    }

    public function getMessage(): string {
        return $this->msg;
    }

    public function __toString(): string
    {
        $token = $this->token;
        $msg = $this->msg;
        $result =  "Parsing error - $msg";
        if (self::hasToken()) {
            $result .= " - $token";
        }
        return $result;
    }
}