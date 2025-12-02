<?php

namespace JsonParser;

enum TokenType
{
    case Invalid;

    case HorizontalWhiteSpace;
    case VerticalWhiteSpace;
    case EOF;

    case Colon;
    case Comma;

    case OpeningSquareBracket;
    case ClosingSquareBracket;
    case OpeningCurlyBracket;
    case ClosingCurlyBracket;

    case Null;
    case True;
    case False;
    case NumberLiteral;
    case StringLiteral;

    public function asString(): string
    {
        switch ($this) {
            case TokenType::Invalid:
                return 'Invalid';
            case TokenType::HorizontalWhiteSpace:
                return 'HorizontalWhiteSpace';
            case TokenType::VerticalWhiteSpace:
                return 'VerticalWhiteSpace';
            case TokenType::EOF:
                return 'EOF';
            case TokenType::Colon:
                return 'Colon';
            case TokenType::Comma:
                return 'Comma';
            case TokenType::OpeningSquareBracket:
                return 'OpeningSquareBracket';
            case TokenType::ClosingSquareBracket:
                return 'ClosingSquareBracket';
            case TokenType::OpeningCurlyBracket:
                return 'OpeningCurlyBracket';
            case TokenType::ClosingCurlyBracket:
                return 'ClosingCurlyBracket';
            case TokenType::Null:
                return 'Null';
            case TokenType::False:
                return 'False';
            case TokenType::True:
                return 'True';
            case TokenType::NumberLiteral:
                return 'NumberLiteral';
            case TokenType::StringLiteral:
                return 'StringLiteral';
            default:
                return 'UNKNOWN TOKEN';
        }
    }
}