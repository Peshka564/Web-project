<?php

use JsonParser\AST\ASTNode;
use JsonParser\AST\StringNode;
use JsonParser\Token\Token;
use JsonParser\Token\TokenType;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class PluginFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "plugin";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        return new StringNode(new Token(TokenType::StringLiteral, "plugin function", 0, 0));
    }
}