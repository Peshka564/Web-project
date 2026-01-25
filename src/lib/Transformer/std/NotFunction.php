<?php

use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\BoolNode;
use JsonParser\Token\Token;
use JsonParser\Token\TokenType;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class NotFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "not";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 1) {
            throw new EvaluationException("Not function needs only 1 boolean argument");
        }

        /**
         * @var BoolNode
         */
        $subNode = Evaluator::eval($args[0], $node, $ctx);

        if ($subNode->getType() !== ASTNodeType::Bool) {
            throw new EvaluationException("Not function needs only 1 boolean argument");
        }
        $subNodeValue = $subNode->getToken()->getType() !== TokenType::True;

        $tokenType = $subNodeValue ? TokenType::True : TokenType::False;
        $tokenLiteral = $subNodeValue ? "true" : "false";
        return new BoolNode(new Token($tokenType, $tokenLiteral, 0, 0));
    }
}