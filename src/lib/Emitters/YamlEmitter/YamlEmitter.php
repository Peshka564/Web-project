<?php

namespace Emitters\YamlEmitter;

use function array_slice, count;
use Exception;
use Emitters\Emitter;
use JsonParser\AST\ArrayNode;
use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\KeyValueNode;
use JsonParser\AST\LeafNode;
use JsonParser\AST\ObjectNode;

class YamlEmitter implements Emitter
{
    public function __construct(private ?YamlEmitterConfig $config = null)
    {
        if ($this->config === null) {
            $this->config = new YamlEmitterConfig();
        }
    }

    private function emitLeaf(LeafNode $node): string
    {
        switch ($node->getType()) {
            case ASTNodeType::Bool:
            case ASTNodeType::Null:
            case ASTNodeType::Number:
            # Assuming no newlines are present in the string
            case ASTNodeType::String:
                return $node->getToken()->getLiteral();
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
        return array_map(fn($line) => $this->config->indentationString . $line, $lines);
    }

    /** @return string[] - an array of lines */
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

            $arrayElementLines = [];
            foreach ($node->getChildren() as $arrayElement) {
                $childrenLines = $this->emitHelper($arrayElement);
                // The first line is not indented and is prefixed with "- "
                $firstLine = "- " . (count($childrenLines) > 0 ? $childrenLines[0] : '');
                // Indent the remaining lines
                $remainingLines = $this->indentLines(array_slice($childrenLines, 1));
                $childrenLines = [$firstLine, ...$remainingLines];
                
                $arrayElementLines = [...$arrayElementLines, ...$childrenLines];
            }

            return $arrayElementLines;
        }
        if ($node->getType() === ASTNodeType::Object) {
            if (!($node instanceof ObjectNode)) {
                throw new Exception("Expected ObjectNode");
            }
            
            if(count($node->getChildren()) === 0) {
                return ['{}'];
            }

            // Here we just return the rows one after another, no indenting
            $arrayElementLines = [];
            foreach ($node->getChildren() as $arrayElement) {
                $childrenLines = $this->emitHelper($arrayElement);
                $arrayElementLines = [...$arrayElementLines, ...$childrenLines];
            }
            return $arrayElementLines;
        }
        if ($node->getType() === ASTNodeType::KeyValue) {
            if (!($node instanceof KeyValueNode)) {
                throw new Exception("Expected KeyValueNode");
            }
            
            $valueLines = $this->emitHelper($node->getValueNode());
            // We need to also remove quotes from key
            $keyLiteral = str_replace('"', "", $node->getKeyNode()->getToken()->getLiteral());

            // We keep the key and value on the same line only if the value is a leaf node
            if($node->getValueNode()->isLeafNode()) {
                return ["{$keyLiteral}: {$valueLines[0]}"];
            }
            $keyLine = "{$keyLiteral}:";
            $indentedValueLines = $this->indentLines($valueLines);
            return [$keyLine, ...$indentedValueLines];
        }
        throw new Exception("Invalid AST node type");
    }
    public function emit(ASTNode $root): string
    {
        $yamlRows = $this->emitHelper($root);
        return implode($this->config->newLineString, $yamlRows);
    }
}