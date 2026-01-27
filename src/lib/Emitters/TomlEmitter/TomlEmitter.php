<?php

namespace Emitters\TomlEmitter;

use function count;
use Exception;
use Emitters\Emitter;
use JsonParser\AST\ArrayNode;
use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\KeyValueNode;
use JsonParser\AST\LeafNode;
use JsonParser\AST\ObjectNode;
use Emitters\TomlEmitter\TomlEmitterConfig;

class TomlEmitter implements Emitter
{
    public function __construct(private ?TomlEmitterConfig $config = null)
    {
        if ($this->config === null) {
            $this->config = new TomlEmitterConfig();
        }
    }

    private function emitArrayInline(ArrayNode $node): string
    {
        $elements = array_map(fn($child) => $this->emitInline($child), $node->getChildren());
        if (count($elements) === 0) {
            return '[ ]';
        }
        return '[ ' . implode(', ', $elements) . ' ]';
    }

    private function emitObjectInline(ObjectNode $node): string
    {
        $elements = array_map(fn($child) => $this->emitInline($child), $node->getChildren());
        if (count($elements) === 0) {
            return '{ }';
        }
        return '{ ' . implode(', ', $elements) . ' }';
    }

    private function emitInline(ASTNode $node): string
    {
        switch ($node->getType()) {
            case ASTNodeType::Bool:
            case ASTNodeType::Null:
            case ASTNodeType::Number:
            // Note: Maybe configure string to be printed with '' instead of ""
            case ASTNodeType::String:
                if(!($node instanceof LeafNode)) {
                    throw new Exception("Expected LeafNode");
                }
                return $node->getToken()->getLiteral();
            case ASTNodeType::Array:
                return $this->emitArrayInline($node);
            case ASTNodeType::Object:
                return $this->emitObjectInline($node);
            case ASTNodeType::KeyValue:
                if(!($node instanceof KeyValueNode)) {
                    throw new Exception("Expected KeyValueNode");
                }
                $key = str_replace('"', '', $node->getKeyNode()->getToken()->getLiteral());
                $value = $this->emitInline($node->getValueNode());
                return "{$key} = {$value}";
            default:
                throw new Exception("Invalid node to print inline");
        }
    }

    private function indentLine(string $line, int $indentationLevel): string
    {
        $times = $indentationLevel > 0 ? $indentationLevel : 0;
        return str_repeat($this->config->indentationString, $times) . $line;
    }

    /** Determines if a node can be printed inline without the need for making separate tables */
    private function canBePrintedInline(ASTNode $node): bool {
        if ($node->isLeafNode()) {
            return true;
        }
        if ($node->getType() === ASTNodeType::Array) {
            if (!($node instanceof ArrayNode)) {
                throw new Exception("Expected ArrayNode");
            }
            // If the array contains any non-object child, it cannot be printed as separate tables, so it needs to be printed inline
            $isEmpty = count($node->getChildren()) === 0;
            $hasNonTables = count(array_filter($node->getChildren(), fn($child) => $child->getType() !== ASTNodeType::Object)) > 0;
            return $isEmpty || $hasNonTables;
        }
        if ($node->getType() === ASTNodeType::Object) {
            if (!($node instanceof ObjectNode)) {
                throw new Exception("Expected ObjectNode");
            }
            return count($node->getChildren()) === 0;
        }
        return false;
    }

    /**
     * @param $node - the object node to emit
     * @param $path - the current path (table name) or null for the root
     * @param $formattedTableName - the formatted table name with brackets
     * @param $indentationLevel - subtables are always indented
     * @return string - an array of lines 
    */
    private function emitObject(ObjectNode $node, string | null $path, string $formattedTableName, int $indentationLevel): string
    {
        $tableHeader = $path === null ? "" : $this->indentLine($formattedTableName, $indentationLevel) . $this->config->newLineString;
        
        $primitiveFields = array_filter($node->getChildren(), (fn(KeyValueNode $child) => $this->canBePrintedInline($child->getValueNode())));
        $tableFields = array_filter($node->getChildren(), (fn(KeyValueNode $child) => !$this->canBePrintedInline($child->getValueNode())));
        
        $primitiveFieldsLines = [];
        foreach($primitiveFields as $field) {
            $primitiveFieldsLines[] = $this->indentLine($this->emitInline($field), $indentationLevel);
        }
        $primitiveFieldsStr = implode($this->config->newLineString, $primitiveFieldsLines);

        $tables = [];
        foreach ($tableFields as $tableField) {
            $tableFieldKey = $tableField->getKeyNode();
            $key = str_replace('"', '', $tableFieldKey->getToken()->getLiteral());
            $newPath = $path === null ? $key : "{$path}.{$key}";

            $tableFieldValue = $tableField->getValueNode();
            // A single object -> print it as a toml table
            if($tableFieldValue->getType() === ASTNodeType::Object) {
                $tableStr = $this->emitObject($tableFieldValue, $newPath, "[{$newPath}]", $indentationLevel + 1);
                $tables[] = $tableStr;
            }
            // Array of tables -> print each of them with the same name/header
            if($tableFieldValue->getType() === ASTNodeType::Array) {
                if(!($tableFieldValue instanceof ArrayNode)) {
                    throw new Exception("Expected ArrayNode");
                }
                foreach($tableFieldValue->getChildren() as $table) {
                    $tableStr = $this->emitObject($table, $newPath, "[[{$newPath}]]", $indentationLevel + 1);
                    $tables[] = $tableStr;
                }
            }
        }
        $tableFieldsStr = implode("{$this->config->newLineString}{$this->config->newLineString}", $tables);

        // Separate primitive fields and table fields with a new line 
        if(!empty($primitiveFieldsStr) && !empty($tableFieldsStr)) {
            $tableBody = $primitiveFieldsStr . $this->config->newLineString . $this->config->newLineString . $tableFieldsStr;
        } else {
            $tableBody = $primitiveFieldsStr . $tableFieldsStr;
        }

        return $tableHeader . $tableBody;
    }

    public function emit(ASTNode $root): string
    {
        if ($root->getType() !== ASTNodeType::Object) {
            throw new Exception("TOML root must be an object", 1);
        }
        // indentationLevel -1 because the we don't indent the subtables of the root table
        return $this->emitObject($root, null, "", -1);
    }
}