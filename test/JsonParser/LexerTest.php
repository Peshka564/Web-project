<?php

namespace JsonParser;

use Tester\TestContext;
use JsonParser\Lexer\Lexer;
use JsonParser\Token\Token;
use JsonParser\Token\TokenType;

class LexerTest
{
    public static function testWhiteSpace(TestContext $ctx)
    {
        $lexer = new Lexer("  \t  \t\r\n   \n    ", 4);
        $expected = [
            new Token(TokenType::HorizontalWhiteSpace, "  \t  \t\r", 1, 1),
            new Token(TokenType::VerticalWhiteSpace, "\n", 1, 13),
            new Token(TokenType::HorizontalWhiteSpace, '   ', 2, 1),
            new Token(TokenType::VerticalWhiteSpace, "\n", 2, 4),
            new Token(TokenType::HorizontalWhiteSpace, '    ', 3, 1),
            new Token(TokenType::EOF, "", 3, 5)
        ];

        $encounteredEOF = false;
        $ind = 0;
        while (!$encounteredEOF) {
            $token = $lexer->nextToken();
            $expectedToken = $expected[$ind];

            if ($token->getType() !== $expectedToken->getType()) {
                $expectedTokenString = $expectedToken->getType()->asString();
                $tokenTypeString = $token->getType()->asString();
                $ctx->fail("expected token type $expectedTokenString, but got $tokenTypeString");
            }
            if ($token->getLiteral() !== $expectedToken->getLiteral()) {
                $expectedLiteral = $expectedToken->getLiteral();
                $tokenLiteral = $token->getLiteral();
                $ctx->fail("expected token literal $expectedLiteral, but got $tokenLiteral");
            }
            if ($token->getRow() !== $expectedToken->getRow()) {
                $expectedRow = $expectedToken->getRow();
                $tokenRow = $token->getRow();
                $ctx->error("expected token row $expectedRow, but got $tokenRow");
            }
            if ($token->getCol() !== $expectedToken->getCol()) {
                $expectedCol = $expectedToken->getCol();
                $tokenCol = $token->getCol();
                $ctx->error("expected token row $expectedCol, but got $tokenCol");
            }

            $ind++;
            if ($token->getType() === TokenType::EOF) {
                $encounteredEOF = true;
            }
        }
    }

    public static function testLexerStringEscapeWithoutFollowingChar(TestContext $ctx)
    {
        $input = '"\\"';
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected invalid token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Invalid) {
            $ctx->fail("expected invalid token, but got $token");
        }
    }

    public static function testLexerStringEscapeWithAllValidSingleChar(TestContext $ctx)
    {
        $input = '"\" \\\\ \/ \b \f \r \n \t"';
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected string token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::StringLiteral) {
            $ctx->fail("expected string token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected the whole input, but got $token");
        }
    }
    public static function testLexerStringEscapeWithInvalidEscape(TestContext $ctx)
    {
        $input = '"hello \g world"';
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected invalid token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Invalid) {
            $ctx->fail("expected invalid token, but got $token");
        }
    }

    public static function testLexerStringEscapeWithValidUnicodeEscape(TestContext $ctx)
    {
        $input = '"\u1111"';
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected string token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::StringLiteral) {
            $ctx->fail("expected string token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected the whole input, but got $token");
        }
    }

    public static function testLexerStringEscapeWithInvalidUnicodeEscape1(TestContext $ctx)
    {
        $input = '"\u111g"';
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected invalid token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Invalid) {
            $ctx->fail("expected invalid token, but got $token");
        }
    }

    public static function testLexerStringEscapeWithInvalidUnicodeEscape2(TestContext $ctx)
    {
        $input = '"\u111"';
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected invalid token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Invalid) {
            $ctx->fail("expected invalid token, but got $token");
        }
    }

    public static function testLexerStringWithUTF8(TestContext $ctx)
    {
        $input = '"Здравейте, нещо си нещо си 😀"';
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected string token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::StringLiteral) {
            $ctx->fail("expected string token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected the whole input, but got $token");
        }
    }

    public static function testLexerStringWithControlChar(TestContext $ctx)
    {
        $input = "\"hello \n world\"";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected invalid token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Invalid) {
            $ctx->fail("expected invalid token, but got $token");
        }
    }


    public static function testLexerNumberWithPositiveInteger(TestContext $ctx)
    {
        $input = "1234";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected number token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::NumberLiteral) {
            $ctx->fail("expected number token, but got $token");
        }
        if ($token->getLiteral() !== "1234") {
            $ctx->fail("expected number literal $input, but got $token");
        }
    }

    public static function testLexerNumberWithNegativeInteger(TestContext $ctx)
    {
        $input = "-1234";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected number token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::NumberLiteral) {
            $ctx->fail("expected number token, but got $token");
        }
        if ($token->getLiteral() !== "-1234") {
            $ctx->fail("expected number literal $input, but got $token");
        }
    }

    public static function testLexerNumberWithInvalidOctal(TestContext $ctx)
    {
        $input = "01234";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected invalid token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Invalid) {
            $ctx->fail("expected invalid token, but got $token");
        }
    }

    public static function testLexerNumberWithPositiveFraction(TestContext $ctx)
    {
        $input = "1234.567";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected number token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::NumberLiteral) {
            $ctx->fail("expected number token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected number literal $input, but got $token");
        }
    }

    public static function testLexerNumberWithNegativeFraction(TestContext $ctx)
    {
        $input = "-1234.567";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected number token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::NumberLiteral) {
            $ctx->fail("expected number token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected number literal $input, but got $token");
        }
    }

    public static function testLexerNumberWithPositiveFractionStartingWithZero(TestContext $ctx)
    {
        $input = "0.567";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected number token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::NumberLiteral) {
            $ctx->fail("expected number token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected number literal $input, but got $token");
        }
    }

    public static function testLexerNumberWithNegativeFractionZero(TestContext $ctx)
    {
        $input = "-0.567";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected number token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::NumberLiteral) {
            $ctx->fail("expected number token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected number literal $input, but got $token");
        }
    }

    public static function testLexerNumberWithSciNotation(TestContext $ctx)
    {
        $input = "1234e11";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected number token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::NumberLiteral) {
            $ctx->fail("expected number token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected number literal $input, but got $token");
        }
    }

    public static function testLexerNumberWithPlusSciNotation(TestContext $ctx)
    {
        $input = "1234E+11";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected number token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::NumberLiteral) {
            $ctx->fail("expected number token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected number literal $input, but got $token");
        }
    }

    public static function testLexerNumberWithMinusSciNotation(TestContext $ctx)
    {
        $input = "12343e-11";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected number token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::NumberLiteral) {
            $ctx->fail("expected number token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected number literal $input, but got $token");
        }
    }

    public static function testLexerNumberWithNegativeFractionWithPlueSciNotation(TestContext $ctx)
    {
        $input = "-12343.567E+11";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected number token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::NumberLiteral) {
            $ctx->fail("expected number token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected number literal $input, but got $token");
        }
    }

    public static function testLexerNumberWithInvalidNoDigitAfterDot1(TestContext $ctx)
    {
        $input = "0.e3";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected invalid token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Invalid) {
            $ctx->fail("expected invalid token, but got $token");
        }
    }

    public static function testLexerNumberWithInvalidNoDigitAfterDot2(TestContext $ctx)
    {
        $input = "0.";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected invalid token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Invalid) {
            $ctx->fail("expected invalid token, but got $token");
        }
    }

    public static function testLexerNumberWithInvalidNoDigitAfterExp1(TestContext $ctx)
    {
        $input = "10e";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected invalid token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Invalid) {
            $ctx->fail("expected invalid token, but got $token");
        }
    }

    public static function testLexerNumberWithInvalidNoDigitAfterExp2(TestContext $ctx)
    {
        $input = "10E+";
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected invalid token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Invalid) {
            $ctx->fail("expected invalid token, but got $token");
        }
    }

    public static function testLexerKeywordNull(TestContext $ctx)
    {
        $input = 'null';
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected null token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::Null) {
            $ctx->fail("expected null token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected null literal, but got $token");
        }
    }
    public static function testLexerKeywordTrue(TestContext $ctx)
    {
        $input = 'true';
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected true token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::True) {
            $ctx->fail("expected true token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected true literal, but got $token");
        }
    }
    public static function testLexerKeywordFalse(TestContext $ctx)
    {
        $input = 'false';
        $lexer = new Lexer($input, 4);
        if (!$lexer->hasNextToken()) {
            $ctx->fail('expected false token, but got EOF token');
        }
        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::False) {
            $ctx->fail("expected false token, but got $token");
        }
        if ($token->getLiteral() !== $input) {
            $ctx->fail("expected false literal, but got $token");
        }
    }


    public static function testLexerEntireValid1(TestContext $ctx)
    {
        $input = '{
	"name": "Name",
	"age": 123,
	"human": true,
	"array": [
		"Programming",
		false,
		42.69e+2,
		null
	]
}   ';

        $expected = [
            new Token(TokenType::OpeningCurlyBracket, '{', 1, 1),
            new Token(TokenType::VerticalWhiteSpace, "\n", 1, 2),

            new Token(TokenType::HorizontalWhiteSpace, "\t", 2, 1),
            new Token(TokenType::StringLiteral, '"name"', 2, 5),
            new Token(TokenType::Colon, ':', 2, 11),
            new Token(TokenType::HorizontalWhiteSpace, ' ', 2, 12),
            new Token(TokenType::StringLiteral, '"Name"', 2, 13),
            new Token(TokenType::Comma, ',', 2, 19),
            new Token(TokenType::VerticalWhiteSpace, "\n", 2, 20),

            new Token(TokenType::HorizontalWhiteSpace, "\t", 3, 1),
            new Token(TokenType::StringLiteral, '"age"', 3, 5),
            new Token(TokenType::Colon, ':', 3, 10),
            new Token(TokenType::HorizontalWhiteSpace, ' ', 3, 11),
            new Token(TokenType::NumberLiteral, '123', 3, 12),
            new Token(TokenType::Comma, ',', 3, 15),
            new Token(TokenType::VerticalWhiteSpace, "\n", 3, 16),

            new Token(TokenType::HorizontalWhiteSpace, "\t", 4, 1),
            new Token(TokenType::StringLiteral, '"human"', 4, 5),
            new Token(TokenType::Colon, ':', 4, 12),
            new Token(TokenType::HorizontalWhiteSpace, ' ', 4, 13),
            new Token(TokenType::True, 'true', 4, 14),
            new Token(TokenType::Comma, ',', 4, 18),
            new Token(TokenType::VerticalWhiteSpace, "\n", 4, 19),

            new Token(TokenType::HorizontalWhiteSpace, "\t", 5, 1),
            new Token(TokenType::StringLiteral, '"array"', 5, 5),
            new Token(TokenType::Colon, ':', 5, 12),
            new Token(TokenType::HorizontalWhiteSpace, ' ', 5, 13),
            new Token(TokenType::OpeningSquareBracket, '[', 5, 14),
            new Token(TokenType::VerticalWhiteSpace, "\n", 5, 15),

            new Token(TokenType::HorizontalWhiteSpace, "\t\t", 6, 1),
            new Token(TokenType::StringLiteral, '"Programming"', 6, 9),
            new Token(TokenType::Comma, ',', 6, 22),
            new Token(TokenType::VerticalWhiteSpace, "\n", 6, 23),

            new Token(TokenType::HorizontalWhiteSpace, "\t\t", 7, 1),
            new Token(TokenType::False, 'false', 7, 9),
            new Token(TokenType::Comma, ',', 7, 14),
            new Token(TokenType::VerticalWhiteSpace, "\n", 7, col: 15),

            new Token(TokenType::HorizontalWhiteSpace, "\t\t", 8, 1),
            new Token(TokenType::NumberLiteral, '42.69e+2', 8, 9),
            new Token(TokenType::Comma, ',', 8, 17),
            new Token(TokenType::VerticalWhiteSpace, "\n", 8, col: 18),

            new Token(TokenType::HorizontalWhiteSpace, "\t\t", 9, 1),
            new Token(TokenType::Null, 'null', 9, 9),
            new Token(TokenType::VerticalWhiteSpace, "\n", 9, col: 13),

            new Token(TokenType::HorizontalWhiteSpace, "\t", 10, 1),
            new Token(TokenType::ClosingSquareBracket, ']', 10, 5),
            new Token(TokenType::VerticalWhiteSpace, "\n", 10, col: 6),


            new Token(TokenType::ClosingCurlyBracket, '}', 11, 1),
            new Token(TokenType::HorizontalWhiteSpace, "   ", 11, 2),
        ];

        $lexer = new Lexer($input, 4);
        $ind = 0;
        while ($lexer->hasNextToken()) {
            $token = $lexer->nextToken();
            $expectedToken = $expected[$ind++];
            if ($token->getType() !== $expectedToken->getType()) {
                $ctx->fail("expected $expectedToken, but got $token");
            }
            if ($token->getLiteral() !== $expectedToken->getLiteral()) {
                $ctx->fail("expected $expectedToken, but got $token");
            }
            if ($token->getRow() !== $expectedToken->getRow()) {
                $ctx->fail("expected $expectedToken, but got $token");
            }
            if ($token->getCol() !== $expectedToken->getCol()) {
                $ctx->fail(error: "expected $expectedToken, but got $token");
            }
        }

        if ($ind !== count($expected)) {
            $ctx->fail("more tokens expected");
        }

        $token = $lexer->nextToken();
        if ($token->getType() !== TokenType::EOF) {
            $ctx->fail("the last token must be EOF, but got $token");
        }
    }
}