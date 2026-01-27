<?php

use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\NumberNode;
use JsonParser\AST\StringNode;
use JsonParser\Token\Token;
use JsonParser\Token\TokenType;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class SubstrFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "substr";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 3) {
            throw new EvaluationException("Substr function needs 3 arguments(the first must be string, the second and third must be number)");
        }

        /**
         * @var StringNode
         */
        $textNode = Evaluator::eval($args[0], $node, $ctx);
        if ($textNode->getType() !== ASTNodeType::String) {
            throw new EvaluationException("Substr function needs string as first argument");
        }
        $textValue = substr($textNode->getToken()->getLiteral(), 1, -1);

        /**
         * @var NumberNode
         */
        $startNode = Evaluator::eval($args[1], $node, $ctx);
        /**
         * @var NumberNode
         */
        $lenNode = Evaluator::eval($args[2], $node, $ctx);
        if ($startNode->getType() !== ASTNodeType::Number || $lenNode->getType() !== ASTNodeType::Number) {
            throw new EvaluationException("Substr function needs numbers for second and third argument");
        }

        $startValue = floor(doubleval($startNode->getToken()->getLiteral()));
        $lenValue = floor(doubleval($lenNode->getToken()->getLiteral()));

        $result = mb_substr($textValue, $startValue, $lenValue);
        
        $tokenType = TokenType::StringLiteral;
        $tokenLiteral = "\"" . $result . "\"";
        return new StringNode(new Token($tokenType, $tokenLiteral, 0, 0));
    }
}