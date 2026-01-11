<?php

namespace JsonParser\AST;

use JsonParser\Token;

class KeyValueNode implements ASTNode
{
    private StringNode $keyNode;
    private ASTNode $valueNode; 
    
    public function __construct(StringNode $keyNode, ASTNode $valueNode)
    {
        $this->keyNode = $keyNode;
        $this->valueNode = $valueNode;
    }

    public function getType(): ASTNodeType
    {
        return ASTNodeType::KeyValue;
    }

    public function getKeyNode(): StringNode
    {
        return $this->keyNode;
    }
    public function getValueNode(): ASTNode
    {
        return $this->valueNode;
    }

    public function isLeafNode(): bool
    {
        return false;
    }
}