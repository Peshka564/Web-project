<?php

use JsonParser\AST\ArrayNode;
use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class MapFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "map";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 2) {
            throw new EvaluationException("Map function needs 2 arguments(the first must be array)");
        }

        /**
         * @var ArrayNode
         */
        $arrayNode = Evaluator::eval($args[0], $node, $ctx);
        if ($arrayNode->getType() !== ASTNodeType::Array) {
            throw new EvaluationException("Map function needs array as first argument");
        }

        $children = $arrayNode->getChildren();
        $result = [];
        foreach ($children as $child) {
            $childResultNode = Evaluator::eval($args[1], $child, $ctx);
            $result[] = $childResultNode;
        }

        return new ArrayNode($result);
    }
}