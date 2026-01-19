<?php

namespace Emitters;

use Emitters\YamlEmitter\YamlEmitter;
use Emitters\YamlEmitter\YamlEmitterConfig;
use Tester\TestContext;
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
        $emitter = new YamlEmitter(new YamlEmitterConfig(" ", "\n"));
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
        $expectedResult = "key1:\n - 1\n - 2\n - key2: \"value2\"\nkey3:\n key4: false";
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }
    
    public static function testNestedStructures2(TestContext $ctx)
    {
        $jsonInput = '{"users": [{"id": 1, "name": "Alice", "tags": ["admin", "user"]}, {"id": 2, "name": "Bob", "tags": ["user"], "metadata": {"active": true, "score": 95.5}}], "config": {"timeout": 30, "retries": 3, "flags": {"debug": false, "verbose": true}}}';
        $expectedResult = "users:\n - id: 1\n  name: \"Alice\"\n  tags:\n   - \"admin\"\n   - \"user\"\n - id: 2\n  name: \"Bob\"\n  tags:\n   - \"user\"\n  metadata:\n   active: true\n   score: 95.5\nconfig:\n timeout: 30\n retries: 3\n flags:\n  debug: false\n  verbose: true";
        YamlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }

}