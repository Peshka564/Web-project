<?php

use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\BoolNode;
use JsonParser\Token\TokenType;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class IfFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "if";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 3) {
            throw new EvaluationException("And function needs 3 arguments(the first must be boolean)");
        }

        /**
         * @var BoolNode
         */
        $cond = Evaluator::eval($args[0], $node, $ctx);
        if ($cond->getType() !== ASTNodeType::Bool) {
            throw new EvaluationException("If function needs boolean as first argument");
        }

        $condValue = $cond->getToken()->getType() === TokenType::True;
        if ($condValue) {
            return Evaluator::eval($args[1], $node, $ctx);
        } else {
            return Evaluator::eval($args[2], $node, $ctx);
        }
    }
}