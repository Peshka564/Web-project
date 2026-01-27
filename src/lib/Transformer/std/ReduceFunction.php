<?php

use JsonParser\AST\ArrayNode;
use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\KeyValueNode;
use JsonParser\AST\ObjectNode;
use JsonParser\AST\StringNode;
use JsonParser\Token\Token;
use JsonParser\Token\TokenType;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class ReduceFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "reduce";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 3) {
            throw new EvaluationException("Reduce function needs 3 arguments(the first must be array)");
        }

        /**
         * @var ArrayNode
         */
        $arrayNode = Evaluator::eval($args[0], $node, $ctx);
        if ($arrayNode->getType() !== ASTNodeType::Array) {
            throw new EvaluationException("Reduce function needs array as first argument");
        }

        $accumulatedNode = Evaluator::eval($args[1], $node, $ctx);

        $children = $arrayNode->getChildren();
        foreach ($children as $child) {

            $accumulatedNode = Evaluator::eval(
                $args[2],
                self::createReduceObject($accumulatedNode, $child),
                 $ctx
            );
        }

        return $accumulatedNode;
    }

    private function createReduceObject(ASTNode $accumulatedValue, ASTNode $elementNode): ObjectNode
    {
        return new ObjectNode([
            new KeyValueNode(
                new StringNode(new Token(TokenType::StringLiteral, "\"acc\"", 0, 0)),
                $accumulatedValue
            ),
            new KeyValueNode(
                new StringNode(new Token(TokenType::StringLiteral, "\"elem\"", 0, 0)),
                $elementNode
            ),
        ]);
    }
}