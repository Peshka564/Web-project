<?php

use JsonParser\AST\ArrayNode;
use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\BoolNode;
use JsonParser\Token\TokenType;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class FilterFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "filter";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 2) {
            throw new EvaluationException("Filter function needs 2 arguments(the first must be array)");
        }

        /**
         * @var ArrayNode
         */
        $arrayNode = Evaluator::eval($args[0], $node, $ctx);
        if ($arrayNode->getType() !== ASTNodeType::Array) {
            throw new EvaluationException("FIlter function needs array as first argument");
        }

        $children = $arrayNode->getChildren();
        $result = [];
        foreach ($children as $child) {
            /**
             * @var BoolNode
             */
            $childResultNode = Evaluator::eval($args[1], $child, $ctx);
            if ($childResultNode->getType() !== ASTNodeType::Bool) {
                throw new EvaluationException("Filter function needs the second argument to return boolean for each element");
            }
            $childResultValue = $childResultNode->getToken()->getType() === TokenType::True;
            if ($childResultValue) {
                $result[] = $child;
            }
        }

        return new ArrayNode($result);
    }
}