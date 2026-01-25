<?php

use JsonParser\AST\ASTNode;
use JsonParser\Lexer\Lexer;
use JsonParser\Parser\Parser;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\TransformerContext;
use Transformer\Evaluator\TransformerFunction;

class LiteralFunction implements TransformerFunction {

    public static function getFunctionName(): string {
        return "literal";
    }

    public function eval(array $args, ASTNode $node, TransformerContext $ctx): ASTNode {
        if (count($args) !== 1) {
            throw new EvaluationException("Literal function must have only 1 arument");
        }

        $literal = self::removeClosingBracket(self::removeOpeningBracket($args[0]));

        $parser = new Parser(new Lexer($literal, 4));
        $parserResult = $parser->parse();

        if ($parserResult->isErr()) {
            throw new EvaluationException("Invalid literal, the literal must be valid json: " . $parserResult->err());
        }

        return $parserResult->ok();
    }

    private function removeOpeningBracket($input) : string {
        $ind = 0;
        $len = strlen($input);
        while ($input[$ind] !== "(" && $ind < $len) {
            $ind++;
        }
        if ($ind === $len) {
            throw new EvaluationException("Missing opening bracket in argument 1 of literal function");
        }
        return substr($input, $ind+1);
    }

    private function removeClosingBracket($input) : string {
        $ind = strlen($input) - 1;

        while ($input[$ind] !== ")" && $ind >= 0) {
            $ind--;
        }
        if ($ind < 0) {
            throw new EvaluationException("Missing closing bracket in argument 1 of literal function");
        }
        return substr($input, 0, $ind);
    }
}