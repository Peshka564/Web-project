<?php

namespace Emitters\JsonEmitter;

use function count;
use Exception;
use Emitters\Emitter;
use JsonParser\AST\ArrayNode;
use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\KeyValueNode;
use JsonParser\AST\ObjectNode;

class JsonEmitter implements Emitter
{
    public function __construct(private ?JsonEmitterConfig $config = null)
    {
        if ($this->config === null) {
            $this->config = new JsonEmitterConfig();
        }
    }

    public function emit(ASTNode $node): string
    {
        return $this->emitNode($node, 0);
    }

    private function emitNode($node,int $indentationLevel): string
    {
        switch ($node->getType())
        {
        case ASTNodeType::Object:
            return $this->emitObjectNode($node, $indentationLevel);
        case ASTNodeType::Array:
            return $this->emitArrayNode($node, $indentationLevel);
        case ASTNodeType::KeyValue;
            return $this->emitValueNode($node, $indentationLevel);
        case ASTNodeType::String:
            return $node->getToken()->getLiteral();
        case ASTNodeType::Number:
            return $node->getToken()->getLiteral();
        case ASTNodeType::Bool:
            return $node->getToken()->getLiteral();
        case ASTNodeType::Null:
            return $node->getToken()->getLiteral();
        default:
            throw new Exception("Unknown node type");
        }
    }

     private function emitObjectNode(ObjectNode $node, int $indentationLevel): string
    {
        $children = $node->getChildren();
        if (count($children) === 0) {
            return "{}";
        }

        $indention = $this->calculateIndentation($indentationLevel);
        $innerIndention = $this->calculateIndentation($indentationLevel + 1);

        $result = "{" . $this->config->newLineString;

        $count = count($children);
        foreach ($children as $index => $child) {
            $result .= $innerIndention;
            $result .= $this->emitNode($child, $indentationLevel + 1);

            if ($index < $count - 1) {
                $result .= ",";
            }
            $result .= $this->config->newLineString;
        }

        $result .= $indention . "}";

        return $result;
    }

    private function emitArrayNode(ArrayNode $node, int $indentationLevel): string
    {
        $children = $node->getChildren();
        if (count($children) === 0) {
            return "[]";
        }

        $indention = $this->calculateIndentation($indentationLevel);
        $innerIndention = $this->calculateIndentation($indentationLevel + 1);

        $result = "[" . $this->config->newLineString;

        $count = count($children);
        foreach ($children as $index => $child) {
            $result .= $innerIndention;
            $result .= $this->emitNode($child, $indentationLevel + 1);

            if ($index < $count - 1) {
                $result .= ",";
            }
            $result .= $this->config->newLineString;
        }

        $result .= $indention . "]";

        return $result;
    }

    private function emitValueNode(KeyValueNode $node, int $indentionLevel): string
    {
        $key = $this->emitNode($node->getKeyNode(), $indentionLevel);
        $value = $this->emitNode($node->getValueNode(), $indentionLevel);

        return "{$key}: {$value}";
    }

    private function calculateIndentation(int $indentationLevel): string
    {
        return str_repeat($this->config->indentationString, $indentationLevel);
    }

}