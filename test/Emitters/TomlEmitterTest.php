<?php

namespace Emitters;

use Emitters\TomlEmitter\TomlEmitter;
use Emitters\TomlEmitter\TomlEmitterConfig;
use Tester\TestContext;
use JsonParser\Lexer\Lexer;
use JsonParser\Parser\Parser;

class TomlEmitterTest
{

    private static function runTest(TestContext $ctx, string $jsonInput, string | null $expectedResult)
    {
        $parseRes = (new Parser(new Lexer($jsonInput, 2)))->parse();
        if ($parseRes->isErr()) {
            $ctx->error("Invalid test input! Fix the test.");
            return;
        }
        $emitter = new TomlEmitter(new TomlEmitterConfig("  ", "\n"));
        $expectedResult = $expectedResult === null ? $expectedResult : str_replace("\r\n", "\n", $expectedResult);
        $emitRes = $emitter->emit($parseRes->ok());
        if ($emitRes !== $expectedResult) {
            file_put_contents("toml_emitter_received.txt", $emitRes);
            file_put_contents("toml_emitter_expected.txt", $expectedResult);
            $receivedLen = strlen($emitRes);
            $expectedLen = strlen($expectedResult);
            print($receivedLen . "\n");
            print($expectedLen . "\n");
            $ctx->fail("Expected:\n{$expectedResult}\nReceived:\n{$emitRes}");
            return;
        }
    }

    public static function testNonObjectRoot(TestContext $ctx)
    {
        $jsonInput = '[1, 2, 3]';
        $expectedResult = null;
        TomlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
        
        $jsonInput = '"just a string"';
        $expectedResult = null;
        TomlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
        
        $jsonInput = '123.132';
        $expectedResult = null;
        TomlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }

    public static function testSimpleTable(TestContext $ctx)
    {
        $jsonInput = '{"key1": 123.5, "key2": "test", "key3": true, "key4": [1, 2, 3]}';
        $expectedResult = "key1 = 123.5\nkey2 = \"test\"\nkey3 = true\nkey4 = [ 1, 2, 3 ]";
        TomlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }
    
    public static function testComplexTable(TestContext $ctx)
    {
        $jsonInput = 
'{
  "user": {
    "name": "pesho"
  },
  "id": 1,
  "tools": [
    {
      "hammer": {
        "size": {
          "length": "20cm"
        },
        "id": 1,
        "weight": {
          "kg": 3
        },
        "broken": false
      },
      "missing": true
    }
  ],
  "address": {
      "street": {
        "name": "test"
      },
      "id": 1,
      "country": {
        "name": "test"
      },
      "missing": false
  },
  "test": [1, {"b": 1, "d": [{"a": 1}]}, {"c": 2}],
  "empty": []
}';
        $expectedResult = 
'id = 1
test = [ 1, { b = 1, d = [ { a = 1 } ] }, { c = 2 } ]
empty = [ ]

[user]
name = "pesho"

[[tools]]
missing = true

  [tools.hammer]
  id = 1
  broken = false

    [tools.hammer.size]
    length = "20cm"

    [tools.hammer.weight]
    kg = 3

[address]
id = 1
missing = false

  [address.street]
  name = "test"

  [address.country]
  name = "test"';
        TomlEmitterTest::runTest($ctx, $jsonInput, $expectedResult);
    }
}