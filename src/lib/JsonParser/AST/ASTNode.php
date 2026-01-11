<?php

namespace JsonParser\AST;

interface ASTNode
{
    public function getType(): ASTNodeType;

    public function isLeafNode(): bool;
}