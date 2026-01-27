<?php

use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\KeyValueNode;
use JsonParser\AST\ObjectNode;
use JsonParser\AST\StringNode;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class MergeFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "merge";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 2) {
            throw new EvaluationException("Merge function needs 2 object arguments");
        }

        /**
         * @var ObjectNode
         */
        $leftNode = Evaluator::eval($args[0], $node, $ctx);
        /**
         * @var ObjectNode
         */
        $rightNode = Evaluator::eval($args[1], $node, $ctx);
        if ($leftNode->getType() !== ASTNodeType::Object || $rightNode->getType() !== ASTNodeType::Object) {
            throw new EvaluationException("Merge function needs 2 object arguments");
        }

        $keys = [];
        $result = $leftNode->getChildren();
        foreach ($result as $childNode) {
            $key = substr($childNode->getKeyNode()->getToken()->getLiteral(), 1, -1);
            $keys[$key] = true;
        }


        $children = $rightNode->getChildren();
        foreach ($children as $childNode) {
            $childKeyNode = $childNode->getKeyNode();
            $childKeyValue = substr($childKeyNode->getToken()->getLiteral(), 1, -1);
            if (!array_key_exists($childKeyValue, $keys)) {
                $result[] = $childNode;
                $keys[$childKeyValue] = true;
            }
        }
        
        return new ObjectNode($result);
    }
}