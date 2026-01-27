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

class LenFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "len";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 1) {
            throw new EvaluationException("Len function needs only 1 string argument");
        }

        /**
         * @var StringNode
         */
        $subNode = Evaluator::eval($args[0], $node, $ctx);
        if ($subNode->getType() !== ASTNodeType::String) {
            throw new EvaluationException("Len function needs only 1 string argument");
        }

        $subNodeValue = substr($subNode->getToken()->getLiteral(), 1, -1);

        $result = mb_strlen($subNodeValue);

        $tokenType = TokenType::NumberLiteral;
        $tokenLiteral = strval($result);
        return new NumberNode(new Token($tokenType, $tokenLiteral, 0, 0));
    }
}