<?php

namespace Transformer;

use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\NumberNode;
use JsonParser\Lexer\Lexer;
use JsonParser\Parser\Parser;
use Tester\TestContext;
use Transformer\Emitter;

class TransformerEmitterTest {
    public static function testEmitter(TestContext $ctx) {
        $parser = new Parser(new Lexer('{
    "array": [
        {
            "key": 5
        },
        {
            "key": 3
        },
        {
            "key": 8
        }
    ]}', 4));
        $res = $parser->parse();

        if ($res->isErr()) {
            $ctx->fail("parser error");
        }
        /**
         * @var ASTNode
         */
        $node = $res->ok();

        $emitterExpr = ".\"array\".2.\"key\"";
        $emitter = new Emitter();        
        $subNode = $emitter->emit($emitterExpr, $node);

        $subNodeType = $subNode->getType();
        if ($subNodeType !== ASTNodeType::Number) {
            $subNodeTypeString = $subNodeType->toString();
            $ctx->fail("Expected number node, but got $subNodeTypeString");
        }
        
        /**
         * @var NumberNode
         */
        $numberNode = $subNode;
        $numberNodeLiteral = $numberNode->getToken()->getLiteral();
        if ($numberNodeLiteral !== "8") {
            $ctx->fail("Expected \"8\", but got \"$numberNodeLiteral\"");
        }
    }
}