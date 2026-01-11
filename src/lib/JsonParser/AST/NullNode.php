<?php

namespace JsonParser\AST;

class NullNode extends LeafNode
{   
    public function getType(): ASTNodeType{
        return ASTNodeType::Null;
    }
}