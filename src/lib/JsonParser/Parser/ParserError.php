<?php

namespace JsonParser\Parser;

use JsonParser\Token\Token;

class ParserError
{
    private Token $token;
    private string $parserErrorMsg;

    public function __construct()
    {
        // TODO
    }

    public function __toString(): string
    {
        // TODO
        return "";
    }
}