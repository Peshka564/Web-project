<?php

use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\BoolNode;
use JsonParser\AST\NumberNode;
use JsonParser\Token\Token;
use JsonParser\Token\TokenType;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class NeFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "ne";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 2) {
            throw new EvaluationException("Ne function needs 2 number arguments");
        }

        /**
         * @var NumberNode
         */
        $left = Evaluator::eval($args[0], $node, $ctx);
        /**
         * @var NumberNode
         */
        $right = Evaluator::eval($args[1], $node, $ctx);
        if ($left->getType() !== ASTNodeType::Number || $right->getType() !== ASTNodeType::Number) {
            throw new EvaluationException("Ne function needs 2 number arguments");
        }

        $leftValue = doubleval($left->getToken()->getLiteral());
        $rightValue = doubleval($right->getToken()->getLiteral());

        $result = $leftValue !== $rightValue;

        $tokenType = $result ? TokenType::True : TokenType::False;
        $tokenLiteral = $result ? "true" : "false";
        return new BoolNode(new Token($tokenType, $tokenLiteral, 0, 0));
    }
}