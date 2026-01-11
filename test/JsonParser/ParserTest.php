<?php

namespace JsonParser;

use JsonParser\AST\ArrayNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\LeafNode;
use JsonParser\AST\NullNode;
use JsonParser\AST\ObjectNode;
use JsonParser\AST\StringNode;
use Tester\TestContext;
use JsonParser\Lexer\Lexer;
use JsonParser\Parser\Parser;
use JsonParser\Token\Token;
use JsonParser\Token\TokenType;

class ParserTest
{
    public static function testNullNode(TestContext $ctx)
    {
        $input = '  null   ';
        $lexer = new Lexer($input, 4);
        $parser = new Parser($lexer);
        $result = $parser->parse();
        if ($result->isErr()) {
            $ctx->fail("error while parsing");
        }

        /**
         * @var NullNode
         */
        $node = $result->ok();
        if ($node->getType() !== ASTNodeType::Null) {
            $nodeType = $node->getType()->asString();
            $ctx->fail("expected null node, but got $nodeType");
        }

        if ($node->getToken()->getLiteral() !== "null") {
            $nodeLiteral = $node->getToken()->getLiteral();
            $ctx->fail("expected null, but got $nodeLiteral");
        }
    }

    public static function testBoolArrayNode(TestContext $ctx)
    {
        $input = '  [ true , false ]   ';
        $lexer = new Lexer($input, 4);
        $parser = new Parser($lexer);
        $result = $parser->parse();
        if ($result->isErr()) {
            $ctx->fail("error while parsing");
        }

        /**
         * @var ArrayNode
         */
        $node = $result->ok();
        if ($node->getType() !== ASTNodeType::Array) {
            $nodeType = $node->getType()->asString();
            $ctx->fail("expected array node, but got $nodeType");
        }

        $children = $node->getChildren();
        if (count($children) !== 2) {
            $childrenCount = count($children);
            $ctx->fail("expected 2 children, but got $childrenCount");
        }

        foreach ($children as $child) {
            if ($child->getType() !== ASTNodeType::Bool) {
                $nodeType = $child->getType()->asString();
                $ctx->fail("expected bool node, but got $nodeType");
            }
        }
    }

    public static function testNumberNode(TestContext $ctx)
    {
        $input = '  123.0   ';
        $lexer = new Lexer($input, 4);
        $parser = new Parser($lexer);
        $result = $parser->parse();
        if ($result->isErr()) {
            $ctx->fail("error while parsing");
        }

        /**
         * @var StringNode
         */
        $node = $result->ok();
        if ($node->getType() !== ASTNodeType::Number) {
            $nodeType = $node->getType()->asString();
            $ctx->fail("expected number node, but got $nodeType");
        }

        if ($node->getToken()->getLiteral() !== "123.0") {
            $nodeLiteral = $node->getToken()->getLiteral();
            $ctx->fail("expected 123.0, but got $nodeLiteral");
        }
    }

    public static function testStringNode(TestContext $ctx)
    {
        $input = '  "hello world"   ';
        $lexer = new Lexer($input, 4);
        $parser = new Parser($lexer);
        $result = $parser->parse();
        if ($result->isErr()) {
            $ctx->fail("error while parsing");
        }

        /**
         * @var StringNode
         */
        $node = $result->ok();
        if ($node->getType() !== ASTNodeType::String) {
            $nodeType = $node->getType()->asString();
            $ctx->fail("expected string node, but got $nodeType");
        }

        if ($node->getToken()->getLiteral() !== "\"hello world\"") {
            $nodeLiteral = $node->getToken()->getLiteral();
            $ctx->fail("expected \"hello world\", but got $nodeLiteral");
        }
    }


    public static function testObjectNode(TestContext $ctx)
    {
        $input = '  {
	"name"  : "Name",
	"age": 123,
	"human": true,
	"array": [
		"Programming",
		false,
		42.69e+2,
		null
	]
}  
';
        $lexer = new Lexer($input, 4);
        $parser = new Parser($lexer);
        $result = $parser->parse();
        if ($result->isErr()) {
            $ctx->fail("error while parsing");
        }


        /**
         * @var ObjectNode
         */
        $node = $result->ok();
        if ($node->getType() !== ASTNodeType::Object) {
            $nodeType = $node->getType()->asString();
            $ctx->fail("expected object node, but got $nodeType");
        }

        $children = $node->getChildren();
        if (count($children) !== 4) {
            $childrenCount = count($children);
            $ctx->fail("expected 4 children, but got $childrenCount");
        }


        $expectedChildrenKeyLiteral = ["\"name\"", "\"age\"", "\"human\"", "\"array\""];
        $expectedChildrenValueTypes = [ASTNodeType::String, ASTNodeType::Number, ASTNodeType::Bool, ASTNodeType::Array];

        for ($i = 0; $i < 4; $i++) {
            $keyValueNode = $children[$i];
            $keyLiteral = $keyValueNode->getKeyNode()->getToken()->getLiteral();
            if ($keyLiteral !== $expectedChildrenKeyLiteral[$i]) {
                $expectedKeyLiteral = $expectedChildrenKeyLiteral[$i];
                $ctx->fail("expected $expectedKeyLiteral, but got $keyLiteral");
            }

            $valueNodeType = $keyValueNode->getValueNode()->getType();
            if ($valueNodeType !== $expectedChildrenValueTypes[$i]) {
                $valueNodeTypeAsString = $valueNodeType->asString();
                $expectedValueNodeTypeAsString = $expectedChildrenValueTypes[$i]->asString();
                $ctx->fail("for key $keyLiteral: expected type $expectedValueNodeTypeAsString, but got $valueNodeTypeAsString");
            }
        }

        /**
         * @var ArrayNode
         */
        $array = $children[3]->getValueNode();
        $arrayChildren = $array->getChildren();
        $expectedArrayChildrenLiteral = ["\"Programming\"", "false", "42.69e+2", "null"];
        $expectedArrayChildrenTypes = [ASTNodeType::String, ASTNodeType::Bool, ASTNodeType::Number, ASTNodeType::Null];

        if (count($arrayChildren) !== 4) {
            $arrayChildrenCount = count($arrayChildren);
            $ctx->fail("expected 4 array children, but got $arrayChildrenCount");
        }

      for ($i = 0; $i < 4; $i++) {
            /**
             * @var LeafNode
             */
            $node = $arrayChildren[$i];
            $type = $node->getType();
            $literal = $node->getToken()->getLiteral();
            if ($type !== $expectedArrayChildrenTypes[$i]) {
                $typeAsString = $type->asString();
                $expectedTypeAsString = $expectedArrayChildrenTypes[$i]->asString();
                $ctx->fail("expected type $expectedTypeAsString, but got $typeAsString");
            }

            if ($literal !== $expectedArrayChildrenLiteral[$i]) {
                $expectedLiteral = $expectedArrayChildrenLiteral[$i];
                $ctx->fail("expected literal $literal, but got $expectedLiteral");
            }
        }
    }
}