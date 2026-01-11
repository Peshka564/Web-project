<?php

namespace JsonParser\AST;

enum ASTNodeType
{
    case Null;
    case Bool;
    case Number;
    case String;
    case KeyValue;
    case Array;
    case Object;

    public function asString(): string
    {
        switch ($this) {
            case ASTNodeType::Null:
                return "NullNode";
            case ASTNodeType::Bool:
                return 'BoolNode';
            case ASTNodeType::Number:
                return 'NumberNode';
            case ASTNodeType::String:
                return 'StringNode';
            case ASTNodeType::Array:
                return 'ArrayNode';
            case ASTNodeType::KeyValue:
                return 'KeyValueNode';
            case ASTNodeType::Object:
                return 'ObjectNode';
            default:
                return 'UNKNOWN Node';
        }
    }
}