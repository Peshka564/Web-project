<?php

namespace JsonParser\AST;

class StringNode extends LeafNode
{    
    public function getType(): ASTNodeType{
        return ASTNodeType::String;
    }
}