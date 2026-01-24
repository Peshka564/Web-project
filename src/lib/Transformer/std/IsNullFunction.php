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

class IsNullFunction implements TransformerFunction {

    public static function getFunctionName(): string {
        return "is-null";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode {
        if (count($args) !== 1) {
            throw new EvaluationException("IsNull function needs 1 argument argument");
        }

        $subNode = Evaluator::eval($args[0], $node, $ctx);
        if ($subNode->getType() === ASTNodeType::Null) {
            return new BoolNode(new Token(TokenType::True, "true", 0, 0));
        } else {
            return new BoolNode(new Token(TokenType::False, "false", 0, 0));
        }
    }
}