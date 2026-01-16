<?php

namespace JsonParser\AST;

class ArrayNode implements ASTNode
{
    /**
     * @var ASTNode[]
     */
    private array $children;

    /**
     * @param ASTNode[] $children
     */
    public function __construct(array $children)
    {
        $this->children = $children;
    }

    public function getType(): ASTNodeType
    {
        return ASTNodeType::Array;
    }

    /**
     * @return ASTNode[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function isLeafNode(): bool
    {
        return false;
    }
}