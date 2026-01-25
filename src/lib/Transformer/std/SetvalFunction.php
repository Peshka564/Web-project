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

class SetvalFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "setval";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 3) {
            throw new EvaluationException("Keyval function needs 2 arguments(the first must be object and second must be string)");
        }

        /**
         * @var ObjectNode
         */
        $objectNode = Evaluator::eval($args[0], $node, $ctx);
        if ($objectNode->getType() !== ASTNodeType::Object) {
            throw new EvaluationException("Setval function needs object as first argument");
        }

        /**
         * @var StringNode
         */
        $keyNode = Evaluator::eval($args[1], $node, $ctx);
        if ($keyNode->getType() !== ASTNodeType::String) {
            throw new EvaluationException("Setval function needs string as second argument");
        }

        $searchedKeyValue = substr($keyNode->getToken()->getLiteral(), 1, -1);
        $pairs = $objectNode->getChildren();
        
        $result = [];
        $found = false;
        foreach ($pairs as $pair) {
            $childKeyNode = $pair->getKeyNode();
            $childKeyValue = substr($childKeyNode->getToken()->getLiteral(), 1, -1);
            if ($childKeyValue === $searchedKeyValue) {
                $valueNode = Evaluator::eval($args[2], $node, $ctx);
                $result[] = new KeyValueNode($childKeyNode, $valueNode);
                $found = true;
            } else {
                $result[] = $pair;
            }
        }
        
        if (!$found) {
            throw new EvaluationException("Setval key not found");
        }
        return new ObjectNode($result);
    }
}