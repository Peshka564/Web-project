<?php

namespace Emitters;

use Exception;
use JsonParser\AST\ArrayNode;
use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\KeyValueNode;
use JsonParser\AST\LeafNode;
use function array_slice, count;
use JsonParser\AST\ObjectNode;

class YamlEmitter
{
    private function emitLeaf(LeafNode $node): string
    {
        switch ($node->getType()) {
            case ASTNodeType::Bool:
            case ASTNodeType::Null:
            case ASTNodeType::Number:
                return $node->getToken()->getLiteral();
            # Assuming no newlines are present in the string for now
            case ASTNodeType::String:
                return "\"{$node->getToken()->getLiteral()}\"";
            default:
                throw new Exception("Invalid leaf node");
        }
    }

    /**
     * @param string[] $lines
     * @return string[]
     */
    private function indentLines(array $lines): array
    {
        return array_map(fn($line) => "\t{$line}", $lines);
    }

    /** @return string[] */
    private function emitHelper(ASTNode $node): array
    {
        if ($node->isLeafNode()) {
            return [$this->emitLeaf($node)];
        }
        if ($node->getType() === ASTNodeType::Array) {
            if (!($node instanceof ArrayNode)) {
                throw new Exception("Expected ArrayNode");
            }

            if(count($node->getChildren()) === 0) {
                return ['[]'];
            }

            $arrayElementRows = [];
            foreach ($node->getChildren() as $arrayElement) {
                $arrayElementRows[] = $this->emitHelper($arrayElement);
            }

            return array_map(function($childrenLines) {
                // The first line is not indented and is prefixed with "- "
                $firstLine = "- " . (count($childrenLines) > 0 ? $childrenLines[0] : '');
                // Indent the remaining lines
                $remainingLines = $this->indentLines(array_slice($childrenLines, 1));
                return [$firstLine, ...$remainingLines];
            }, $arrayElementRows);
        }
        if ($node->getType() === ASTNodeType::Object) {
            if (!($node instanceof ObjectNode)) {
                throw new Exception("Expected ObjectNode");
            }
            
            if(count($node->getChildren()) === 0) {
                return ['{}'];
            }

            // Here we just return the rows one after another, no indenting
            $arrayElementRows = [];
            foreach ($node->getChildren() as $arrayElement) {
                $arrayElementRows[] = $this->emitHelper($arrayElement);
            }
            return $arrayElementRows;
        }
        if ($node->getType() === ASTNodeType::KeyValue) {
            if (!($node instanceof KeyValueNode)) {
                throw new Exception("Expected KeyValueNode");
            }
            
            $valueLines = $this->emitHelper($node->getValueNode());
            $keyLiteral = $node->getKeyNode()->getToken()->getLiteral();

            $keyLine = $keyLiteral . ": " . (count($valueLines) > 0 ? $valueLines[0] : '');
            $indentedValueLines = $this->indentLines(array_slice($valueLines, 1));
            return [$keyLine, ...$indentedValueLines];
        }
        throw new Exception("Invalid AST node type");
    }
    public function emit(ASTNode $root): string
    {
        $yamlRows = $this->emitHelper($root);
        return implode("\n", $yamlRows);
    }
}