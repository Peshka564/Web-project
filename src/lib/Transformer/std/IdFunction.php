<?php

use JsonParser\AST\ASTNode;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class IdFunction implements TransformerFunction {

    public static function getFunctionName(): string {
        return "id";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode {
        if (count($args) !== 0) {
            throw new EvaluationException("Id function doesn't need arguments");
        }

        return $node;
    }
}