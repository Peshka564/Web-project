<?php

namespace Transformer\Evaluator;

use JsonParser\AST\ASTNode;
use Transformer\Emitter\Emitter;
use Transformer\Emitter\InvalidEmitterStateException;
use Transformer\Emitter\InvalidEmitterSyntaxException;
use Transformer\Parser\Parser;
use Transformer\Parser\ParsingException;

class Evaluator
{
    public static function eval(string $expr, ASTNode $node, TransformerContext $ctx): ASTNode
    {
        $parser = new Parser($expr);
        try {
            $parser->parse();
        } catch (ParsingException $e) {
            throw new EvaluationException("Error while parsing: " . $e->getMessage(), 0, $e);
        }
        $subNode = null;
        try {
            $subNode = new Emitter()->emit($parser->getEmitter(), $node);
        } catch (InvalidEmitterStateException | InvalidEmitterSyntaxException $th) {
            throw new EvaluationException("error while traversing the AST");
        }

        $funcName = $parser->getFunctionName();
        if (!$ctx->doesContainFunction($funcName)) {
            throw new EvaluationException("Function with name $funcName was not loaded");
        }

        $args = $parser->getArguments();

        return $ctx->getFunction($funcName)->eval($args, $subNode, $ctx);
    }
}