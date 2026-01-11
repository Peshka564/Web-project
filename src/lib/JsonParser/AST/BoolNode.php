<?php

namespace JsonParser\AST;

class BoolNode extends LeafNode
{   
    public function getType(): ASTNodeType{
        return ASTNodeType::Bool;
    }
}