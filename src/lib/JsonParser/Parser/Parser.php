<?php

namespace JsonParser\Parser;

use JsonParser\AST\ArrayNode;
use JsonParser\AST\BoolNode;
use JsonParser\AST\KeyValueNode;
use JsonParser\AST\NullNode;
use JsonParser\AST\NumberNode;
use JsonParser\AST\ObjectNode;
use JsonParser\AST\StringNode;

use JsonParser\Lexer\Lexer;
use JsonParser\Token\Token;
use JsonParser\Token\TokenType;

class Parser
{
    private Lexer $lexer;
    private Token|null $currToken;

    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
        $this->currToken = null;
        $this->loadToken();
    }

    private function loadToken()
    {
        $this->currToken = $this->lexer->nextToken();
    }

    public function parse(): ParserResult
    {
        $result = $this->parseJson();
        if ($result->isErr()) {
            return $result;
        }
        $this->skipWhiteSpaceTokens();
        if ($this->currToken->getType() !== TokenType::EOF) {
            // TODO add parsing error for expecting EOF
            return new ParserResult(new ParserError());
        }
        return $result;
    }

    private function parseJson(): ParserResult
    {
        $this->skipWhiteSpaceTokens();
        switch ($this->currToken->getType()) {
            case TokenType::Null:
                return new ParserResult($this->parseNullNode());
            case TokenType::True:
            case TokenType::False:
                return new ParserResult($this->parseBoolNode());
            case TokenType::NumberLiteral:
                return new ParserResult($this->parseNumberNode());
            case TokenType::StringLiteral:
                return new ParserResult($this->parseStringNode());
            case TokenType::OpeningSquareBracket:
                return $this->parseArrayNode();
            case TokenType::OpeningCurlyBracket:
                return $this->parseObjectNode();
            default:
                // TODO add parsing errors for different tokens like EOF
                return new ParserResult(new ParserError());
        }
    }

    private function skipWhiteSpaceTokens()
    {
        while (
            $this->currToken->getType() === TokenType::HorizontalWhiteSpace
            || $this->currToken->getType() === TokenType::VerticalWhiteSpace
        ) {
            $this->loadToken();
        }
    }

    private function parseNullNode(): NullNode
    {
        $token = new NullNode($this->currToken);
        $this->loadToken();
        return $token;
    }

    private function parseBoolNode(): BoolNode
    {
        $token = new BoolNode($this->currToken);
        $this->loadToken();
        return $token;
    }

    private function parseNumberNode(): NumberNode
    {
        $token = new NumberNode($this->currToken);
        $this->loadToken();
        return $token;
    }

    private function parseStringNode(): StringNode
    {
        $token = new StringNode($this->currToken);
        $this->loadToken();
        return $token;
    }

    private function parseArrayNode(): ParserResult
    {
        $children = [];
        $this->loadToken();
        while ($this->currToken->getType() !== TokenType::ClosingSquareBracket) {
            $result = $this->parseJson();
            if ($result->isErr()) {
                return $result;
            }
            $children[] = $result->ok();

            $this->skipWhiteSpaceTokens();
            if (
                $this->currToken->getType() !== TokenType::Comma
                && $this->currToken->getType() !== TokenType::ClosingSquareBracket
            ) {
                // TODO add parsing error 
                return new ParserResult(new ParserError());
            }

            if ($this->currToken->getType() !== TokenType::ClosingSquareBracket) {
                $this->loadToken();
            }

        }
        $this->loadToken();
        return new ParserResult(new ArrayNode($children));
    }

    private function parseKeyValueNode(): ParserResult
    {
        $this->skipWhiteSpaceTokens();
        if ($this->currToken->getType() !== TokenType::StringLiteral) {
            // TODO add parsing error
            return new ParserResult(new ParserError());
        }
        $key = $this->parseStringNode();

        $this->skipWhiteSpaceTokens();
        if ($this->currToken->getType() !== TokenType::Colon) {
            // TODO add parsing error
            return new ParserResult(new ParserError());
        }

        $this->loadToken();
        $valueResult = $this->parseJson();
        if ($valueResult->isErr()) {
            // Maybe error wrap
            return $valueResult;
        }

        return new ParserResult(new KeyValueNode($key, $valueResult->ok()));
    }

    private function parseObjectNode(): ParserResult
    {
        $keys = [];
        $children = [];
        $this->loadToken();
        while ($this->currToken->getType() !== TokenType::ClosingCurlyBracket) {
            $result = $this->parseKeyValueNode();
            if ($result->isErr()) {
                return $result;
            }
            /**
             * @var KeyValueNode
             */
            $keyValue = $result->ok();
            $keyLiteral = $keyValue->getKeyNode()->getToken()->getLiteral();
            if (array_key_exists($keyLiteral, $keys)) {
                // TODO add parsing error when duplicate keys
                return new ParserResult(new ParserError());
            }

            $children[] = $keyValue;
            $keys[$keyLiteral] = true;

            $this->skipWhiteSpaceTokens();
            if (
                $this->currToken->getType() !== TokenType::Comma
                && $this->currToken->getType() !== TokenType::ClosingCurlyBracket
            ) {
                // TODO add parsing error 
                return new ParserResult(new ParserError());
            }

            if ($this->currToken->getType() !== TokenType::ClosingCurlyBracket) {
                $this->loadToken();
            }
        }
        $this->loadToken();
        return new ParserResult(new ObjectNode($children));
    }
}