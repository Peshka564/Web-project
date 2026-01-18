<?php

namespace Emitters;

use Emitters\JsonEmitter\JsonEmitter;
use Emitters\JsonEmitter\JsonEmitterConfig;
use Tester\TestContext;
use JsonParser\Lexer\Lexer;
use JsonParser\Parser\Parser;

class JsonEmitterTest
{
    private static function runTest(TestContext $ctx, string $jsonInput, string $expectedResult)
    {
        $parseRes = (new Parser(new Lexer($jsonInput, 2)))->parse();
        if ($parseRes->isErr()) {
            $ctx->error("Invalid test input! Fix the test.");
            return;
        }
        $emitter = new JsonEmitter(new JsonEmitterConfig("  ", "\n"));
        $emitRes = $emitter->emit($parseRes->ok());
        if ($emitRes !== $expectedResult) {
            $ctx->fail("Expected:\n{$expectedResult}\nReceived:\n{$emitRes}");
            return;
        }
    }

    public static function testJsonEmitterNullInput(TestContext $ctx){
        $input = 'null';
        $expectedOutput = 'null';
        JsonEmitterTest::runTest($ctx, $input, $expectedOutput);
    }

    public static function testJsonEmitterBoolInput(TestContext $ctx){
        $input = 'true';
        $expectedOutput = 'true';
        JsonEmitterTest::runTest($ctx, $input, $expectedOutput);
    }

    public static function testJsonEmitterNumberInput(TestContext $ctx){
        $input = '132.43';
        $expectedOutput = '132.43';
        JsonEmitterTest::runTest($ctx, $input, $expectedOutput);
    }

    public static function testJsonEmitterStringInput(TestContext $ctx){
        $input = '"Just a random string"';
        $expectedOutput = '"Just a random string"';
        JsonEmitterTest::runTest($ctx, $input, $expectedOutput);
    }

    public static function testJsonEmitterKeyValueInput(TestContext $ctx){
        $input = '{"a":1,"b":"x"}';
        $expectedOutput =
            '{' . "\n" .
            '  "a": 1,' . "\n" .
            '  "b": "x"' . "\n".
            '}';
        JsonEmitterTest::runTest($ctx, $input, $expectedOutput);
    }

    public static function testJsonEmitterArrayInput(TestContext $ctx){
        $input = '{"arr":[1,"x",null]}';
        $expectedOutput =
            '{' . "\n" .
            '  "arr": [' . "\n" .
            '    1,' . "\n" .
            '    "x",' . "\n" .
            '    null' . "\n" .
            '  ]' . "\n" .
            '}';
        JsonEmitterTest::runTest($ctx, $input, $expectedOutput);
    }

    public static function testJsonEmitterNestedObjectInput(TestContext $ctx){
        $input = '{"a":{"b":1,"c":"x"}}';
        $expectedOutput =
            '{' . "\n" .
            '  "a": {' . "\n" .
            '    "b": 1,' . "\n" .
            '    "c": "x"' . "\n" .
            '  }' . "\n" .
            '}';
        JsonEmitterTest::runTest($ctx, $input, $expectedOutput);
    }

}