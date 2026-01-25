<?php

use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class DefaultFunction implements TransformerFunction {

    public static function getFunctionName(): string {
        return "default";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode {
        if (count($args) !== 2) {
            throw new EvaluationException("Default function needs 2 arguments");
        }

        
        $subNode = Evaluator::eval($args[0], $node, $ctx);
        if ($subNode->getType() === ASTNodeType::Null) {
            return Evaluator::eval($args[1], $node, $ctx);
        } else {
            return $subNode;
        }
    }
}