<?php

namespace Emitters;

use Tester\TestContext;
use Emitters\YamlEmitter;
use JsonParser\Lexer\Lexer;
use JsonParser\Parser\Parser;

class YamlEmitterTest
{

    private static function runTest(TestContext $ctx, string $jsonInput, string $expectedResult)
    {
        $parseRes = (new Parser(new Lexer($jsonInput, 2)))->parse();
        if ($parseRes->isErr()) {
            $ctx->error("Invalid test input! Fix the test.");
            return;
        }
        $emitter = new YamlEmitter();
        $emitRes = $emitter->emit($parseRes->ok());
        if ($emitRes !== $expectedResult) {
            $ctx->fail("Expected:\n{$expectedResult}\nReceived:\n{$emitRes}");
            return;
        }
    }

    public static function testSingleString(TestContext $ctx)
    {
        $jsonInput = '"test string"';
        $expectedResult = '"test string"';
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
        
        $jsonInput = '""';
        $expectedResult = '""';
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }

    public static function testSingleBool(TestContext $ctx)
    {
        $jsonInput = 'false';
        $expectedResult = 'false';
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
        
        $jsonInput = 'true';
        $expectedResult = 'true';
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }

    public static function testSingleNum(TestContext $ctx)
    {   
        $jsonInput = '{"num": 534.123123}';
        $expectedResult = 'num: 534.123123';
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }

    public static function testSimpleArray(TestContext $ctx)
    {
        $jsonInput = '[1, 2, 3]';
        $expectedResult = "- 1\n- 2\n- 3";
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }
    
    public static function testEmptyArray(TestContext $ctx) 
    {
        $jsonInput = '[]';
        $expectedResult = '[]';
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }

    public static function testNestedStructures1(TestContext $ctx)
    {
        $jsonInput = '{"key1": [1, 2, {"key2": "value2"}], "key3": {"key4": false}}';
        $expectedResult = "key1:\n\t- 1\n\t- 2\n\t- key2: \"value2\"\nkey3:\n\tkey4: false";
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }
    
    public static function testNestedStructures2(TestContext $ctx)
    {
        $jsonInput = '{"users": [{"id": 1, "name": "Alice", "tags": ["admin", "user"]}, {"id": 2, "name": "Bob", "tags": ["user"], "metadata": {"active": true, "score": 95.5}}], "config": {"timeout": 30, "retries": 3, "flags": {"debug": false, "verbose": true}}}';
        $expectedResult = "users:\n\t- id: 1\n\t\tname: \"Alice\"\n\t\ttags:\n\t\t\t- \"admin\"\n\t\t\t- \"user\"\n\t- id: 2\n\t\tname: \"Bob\"\n\t\ttags:\n\t\t\t- \"user\"\n\t\tmetadata:\n\t\t\tactive: true\n\t\t\tscore: 95.5\nconfig:\n\ttimeout: 30\n\tretries: 3\n\tflags:\n\t\tdebug: false\n\t\tverbose: true";
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }

}