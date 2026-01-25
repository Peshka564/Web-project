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

class AndFunction implements TransformerFunction
{

    public static function getFunctionName(): string
    {
        return "and";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        if (count($args) !== 2) {
            throw new EvaluationException("And function needs 2 boolean arguments");
        }

        /**
         * @var BoolNode
         */
        $left = Evaluator::eval($args[0], $node, $ctx);

        if ($left->getType() !== ASTNodeType::Bool) {
            throw new EvaluationException("And function needs 2 boolean arguments");
        }
        $leftValue = $left->getToken()->getType() === TokenType::True;
        if (!$leftValue) {
            return new BoolNode(new Token(TokenType::False, "false", 0, 0));
        }

        /**
         * @var BoolNode
         */
        $right = Evaluator::eval($args[1], $node, $ctx);
        if ($right->getType() !== ASTNodeType::Bool) {
            throw new EvaluationException("And function needs 2 boolean arguments");
        }

        $tokenType = $right->getToken()->getType();
        $tokenLiteral = $right->getToken()->getLiteral();
        return new BoolNode(new Token($tokenType, $tokenLiteral, 0, 0));
    }
}