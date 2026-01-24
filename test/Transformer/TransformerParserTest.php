<?php

namespace Transformer;

use Tester\TestContext;
use Transformer\Parser\Parser;

class TransformerParserTest
{
    public static function testParserEmitter(TestContext $ctx)
    {
        $parser = new Parser("  (.\"array\".4.\"name\" len)   ");
        $parser->parse();

        $actualEmitter = $parser->getEmitter();
        $expectedEmitter = ".\"array\".4.\"name\"";

        if ($actualEmitter !== $expectedEmitter) {
            $ctx->fail("expected \"$expectedEmitter\", but got \"$actualEmitter\"");
        }
    }

    public static function testParserFunctionName(TestContext $ctx)
    {
        $parser = new Parser("  (.\"array\".4.\"name\" len)   ");
        $parser->parse();

        $actualFunctionName = $parser->getFunctionName();
        $expectedFunctionName = "len";

        if ($actualFunctionName !== $expectedFunctionName) {
            $ctx->fail("expected \"$expectedFunctionName\", but got \"$actualFunctionName\"");
        }
    }

    public static function testParserArguments(TestContext $ctx)
    {
        $parser = new Parser("  (.\"array\" filter (. pipe ( len) (lt (5 ))))   ");
        $parser->parse();

        $actualArguments = $parser->getArguments();
        $expectedArgument1 = "(. pipe ( len) (lt (5 )))";

        $actualArgumentCount = count($actualArguments);
        if ($actualArgumentCount !== 1) {
            $ctx->fail("expected 1 argument, $actualArgumentCount");
        }

        $actualArgument1 = $actualArguments[0];
        if ($actualArgument1 !== $expectedArgument1) {
            $ctx->fail("expected \"$expectedArgument1\", but got \"$actualArgument1\"");
        }
    }

    public static function testParserArgumentsRecursive(TestContext $ctx)
    {
        $parser = new Parser("  (.\"array\" filter (. pipe ( len) (lt (literal (5) ) )))   ");
        $parser->parse();

        $actualArguments = $parser->getArguments();
        $expectedArgument1 = "(. pipe ( len) (lt (literal (5) ) ))";

        $actualArgumentCount = count($actualArguments);
        if ($actualArgumentCount !== 1) {
            $ctx->fail("expected 1 argument, $actualArgumentCount");
        }

        $actualArgument1 = $actualArguments[0];
        if ($actualArgument1 !== $expectedArgument1) {
            $ctx->fail("expected \"$expectedArgument1\", but got \"$actualArgument1\"");
        }

        // level 2
        $parser = new Parser($actualArgument1);
        $parser->parse();

        $actualArguments = $parser->getArguments();
        $expectedArgument1 = "( len)";
        $expectedArgument2 = "(lt (literal (5) ) )";

        $actualArgumentCount = count($actualArguments);
        if ($actualArgumentCount !== 2) {
            $ctx->fail("expected 2 argument, $actualArgumentCount");
        }

        $actualArgument1 = $actualArguments[0];
        if ($actualArgument1 !== $expectedArgument1) {
            $ctx->fail("expected \"$expectedArgument1\", but got \"$actualArgument1\"");
        }
        $actualArgument2 = $actualArguments[1];
        if ($actualArgument2 !== $expectedArgument2) {
            $ctx->fail("expected \"$expectedArgument1\", but got \"$actualArgument1\"");
        }
    }
}