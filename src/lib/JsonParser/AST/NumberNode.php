<?php

namespace JsonParser\AST;

class NumberNode extends LeafNode
{    
    public function getType(): ASTNodeType{
        return ASTNodeType::Number;
    }
}