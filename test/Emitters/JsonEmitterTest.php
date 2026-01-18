<?php

namespace Emitters;

use Tester\TestContext;
use JsonParser\Lexer\Lexer;
use JsonParser\Parser\Parser;
use Emitters\JsonEmitter;

class JsonEmitterTest{
    public static function testJsonEmitterNullInput(TestContext $ctx){
        $input = 'null';
        $lexer = new Lexer($input, 0);
        $parser = new Parser($lexer);
        $result = $parser->parse();

        //no parsing error handling, technically node could be handwritten AST root, same goes for all tests

        $JsonEmitter = new JsonEmitter();
        $node = $result->ok();
        $output = $JsonEmitter->emit($node);
        $expectedOutput = 'null';

        if($output !== $expectedOutput){
            $ctx->fail("Expected json $expectedOutput but got $output");
        }
    }

    public static function testJsonEmitterBoolInput(TestContext $ctx){
        $input = 'true';
        $lexer = new Lexer($input, 0);
        $parser = new Parser($lexer);
        $result = $parser->parse();

        $JsonEmitter = new JsonEmitter();
        $node = $result->ok();
        $output = $JsonEmitter->emit($node);
        $expectedOutput = 'true';

        if($output !== $expectedOutput){
            $ctx->fail("Expected json $expectedOutput but got $output");
        }
    }

    public static function testJsonEmitterNumberInput(TestContext $ctx){
        $input = '132.43';
        $lexer = new Lexer($input, 0);
        $parser = new Parser($lexer);
        $result = $parser->parse();

        $JsonEmitter = new JsonEmitter();
        $node = $result->ok();
        $output = $JsonEmitter->emit($node);
        $expectedOutput = '132.43';

        if($output !== $expectedOutput){
            $ctx->fail("Expected json $expectedOutput but got $output");
        }
    }

    public static function testJsonEmitterStringInput(TestContext $ctx){
        $input = '"Just a random string"';
        $lexer = new Lexer($input, 0);
        $parser = new Parser($lexer);
        $result = $parser->parse();

        $JsonEmitter = new JsonEmitter();
        $node = $result->ok();
        $output = $JsonEmitter->emit($node);
        $expectedOutput = '"Just a random string"';

        if($output !== $expectedOutput){
            $ctx->fail("Expected json $expectedOutput but got $output");
        }
    }

    public static function testJsonEmitterKeyValueInput(TestContext $ctx){
        $input = '{"a":1,"b":"x"}';
        $lexer = new Lexer($input, 0);
        $parser = new Parser($lexer);
        $result = $parser->parse();

        $JsonEmitter = new JsonEmitter();
        $node = $result->ok();
        $output = $JsonEmitter->emit($node);
        $expectedOutput =
            '{' . "\n" .
            '  "a": 1,' . "\n" .
            '  "b": "x"' . "\n".
            '}';

        if($output !== $expectedOutput){
            $ctx->fail("Expected json $expectedOutput but got $output");
        }
    }

    public static function testJsonEmitterArrayInput(TestContext $ctx){
        $input = '{"arr":[1,"x",null]}';
        $lexer = new Lexer($input, 0);
        $parser = new Parser($lexer);
        $result = $parser->parse();

        $JsonEmitter = new JsonEmitter();
        $node = $result->ok();
        $output = $JsonEmitter->emit($node);
        $expectedOutput =
            '{' . "\n" .
            '  "arr": [' . "\n" .
            '    1,' . "\n" .
            '    "x",' . "\n" .
            '    null' . "\n" .
            '  ]' . "\n" .
            '}';

        if ($output !== $expectedOutput) {
            $ctx->fail("Expected json $expectedOutput but got $output");
        }
    }

    public static function testJsonEmitterNestedObjectInput(TestContext $ctx){
        $input = '{"a":{"b":1,"c":"x"}}';
        $lexer = new Lexer($input, 0);
        $parser = new Parser($lexer);
        $result = $parser->parse();

        $JsonEmitter = new JsonEmitter();
        $node = $result->ok();
        $output = $JsonEmitter->emit($node);

        $expectedOutput =
            '{' . "\n" .
            '  "a": {' . "\n" .
            '    "b": 1,' . "\n" .
            '    "c": "x"' . "\n" .
            '  }' . "\n" .
            '}';

        if ($output !== $expectedOutput) {
            $ctx->fail("Expected json $expectedOutput but got $output");
        }
    }

}