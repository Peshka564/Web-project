<?php

use JsonParser\AST\ASTNode;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class PipeFunction implements TransformerFunction {

    public static function getFunctionName(): string {
        return "pipe";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode {
        foreach ($args as $expr) {
            $node = Evaluator::eval($expr, $node, $ctx);
        }
        return $node;
    }
}