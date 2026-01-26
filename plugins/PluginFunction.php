<?php

use JsonParser\AST\ASTNode;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class PluginFunction implements TransformerFunction {

    public static function getFunctionName(): string {
        return "plugin";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode {
        return $node;
    }
}