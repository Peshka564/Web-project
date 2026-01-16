<?php

namespace JsonParser\AST;

class ObjectNode implements ASTNode
{
    /**
     * @var KeyValueNode[]
     */
    private array $children;

    /**
     * @param KeyValueNode[] $children
     */
    public function __construct(array $children)
    {
        $this->children = $children;
    }

    public function getType(): ASTNodeType
    {
        return ASTNodeType::Object;
    }

    /**
     * @return KeyValueNode[]
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