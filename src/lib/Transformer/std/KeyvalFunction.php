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

class KeyvalFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "keyval";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 2) {
            throw new EvaluationException("Keyval function needs 2 arguments(the first must be string)");
        }

        /**
         * @var StringNode
         */
        $keyNode = Evaluator::eval($args[0], $node, $ctx);
        if ($keyNode->getType() !== ASTNodeType::String) {
            throw new EvaluationException("Keyval function needs string as first argument");
        }

        $valueNode = Evaluator::eval($args[1], $node, $ctx);

        return  new ObjectNode([
            new KeyValueNode(
                $keyNode,
                $valueNode
            ),
        ]);
    }
}