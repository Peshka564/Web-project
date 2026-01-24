<?php

use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\BoolNode;
use JsonParser\AST\StringNode;
use JsonParser\Token\Token;
use JsonParser\Token\TokenType;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class PrefixFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "prefix";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 2) {
            throw new EvaluationException("Prefix function needs 2 string arguments");
        }

        /**
         * @var StringNode
         */
        $left = Evaluator::eval($args[0], $node, $ctx);
        /**
         * @var StringNode
         */
        $right = Evaluator::eval($args[1], $node, $ctx);
        if ($left->getType() !== ASTNodeType::String || $right->getType() !== ASTNodeType::String) {
            throw new EvaluationException("Prefix function needs 2 string arguments");
        }

        $leftValue = substr($left->getToken()->getLiteral(), 1, -1);
        $rightValue = substr($right->getToken()->getLiteral(), 1, -1);

        $result = str_starts_with($leftValue, $rightValue);

        $tokenType = $result ? TokenType::True : TokenType::False;
        $tokenLiteral = $result ? "true" : "false";
        return new BoolNode(new Token($tokenType, $tokenLiteral, 0, 0));
    }
}