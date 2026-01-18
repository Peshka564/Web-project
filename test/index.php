<?php
require_once __DIR__ . '/./autoload.php';

use DemoTest\DemoTest;
use JsonParser\LexerTest;
use JsonParser\ParserTest;
use Emitters\JsonEmitterTest;
use Tester\Tester;

$tester = new Tester();

// register tests from here

// lexer whitespace tests
$tester->RegisterTest([LexerTest::class, 'testWhiteSpace']);

// lexer string tests
$tester->RegisterTest([LexerTest::class, 'testLexerStringEscapeWithoutFollowingChar']);
$tester->RegisterTest([LexerTest::class, 'testLexerStringEscapeWithAllValidSingleChar']);
$tester->RegisterTest([LexerTest::class, 'testLexerStringEscapeWithInvalidEscape']);
$tester->RegisterTest([LexerTest::class, 'testLexerStringEscapeWithValidUnicodeEscape']);
$tester->RegisterTest([LexerTest::class, 'testLexerStringEscapeWithInvalidUnicodeEscape1']);
$tester->RegisterTest([LexerTest::class, 'testLexerStringEscapeWithInvalidUnicodeEscape2']);
$tester->RegisterTest([LexerTest::class, 'testLexerStringWithUTF8']);
$tester->RegisterTest([LexerTest::class, 'testLexerStringWithControlChar']);

// lexer number tests
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithPositiveInteger']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithNegativeInteger']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithInvalidOctal']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithPositiveFraction']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithNegativeFraction']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithPositiveFractionStartingWithZero']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithNegativeFractionZero']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithSciNotation']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithPlusSciNotation']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithMinusSciNotation']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithNegativeFractionWithPlueSciNotation']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithInvalidNoDigitAfterDot1']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithInvalidNoDigitAfterDot2']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithInvalidNoDigitAfterExp1']);
$tester->RegisterTest([LexerTest::class, 'testLexerNumberWithInvalidNoDigitAfterExp2']);


// lexer keyword tests
$tester->RegisterTest([LexerTest::class, 'testLexerKeywordNull']);
$tester->RegisterTest([LexerTest::class, 'testLexerKeywordTrue']);
$tester->RegisterTest([LexerTest::class, 'testLexerKeywordFalse']);

// entire lexer
$tester->RegisterTest([LexerTest::class, 'testLexerEntireValid1']);

// parser tests
$tester->RegisterTest([ParserTest::class, 'testNullNode']);
$tester->RegisterTest([ParserTest::class, 'testBoolArrayNode']);
$tester->RegisterTest([ParserTest::class, 'testNumberNode']);
$tester->RegisterTest([ParserTest::class, 'testStringNode']);
$tester->RegisterTest([ParserTest::class, 'testObjectNode']);

// jsonEmitter tests
$tester->RegisterTest([JsonEmitterTest::class, 'testJsonEmitterNullInput']);
$tester->RegisterTest([JsonEmitterTest::class, 'testJsonEmitterBoolInput']);
$tester->RegisterTest([JsonEmitterTest::class, 'testJsonEmitterNumberInput']);
$tester->RegisterTest([JsonEmitterTest::class, 'testJsonEmitterStringInput']);
$tester->RegisterTest([JsonEmitterTest::class, 'testJsonEmitterKeyValueInput']);
$tester->RegisterTest([JsonEmitterTest::class, 'testJsonEmitterArrayInput']);
$tester->RegisterTest([JsonEmitterTest::class, 'testJsonEmitterNestedObjectInput']);
// to here

$tester->RunTests();

$results = $tester->getResults();

foreach ($results as $testName => $result) {
    echo '===' . $testName . '===' . PHP_EOL;

    echo 'Status: ';
    if ($result->ok()) {
        echo "\033[32mPASSED\033[0m";
    } else {
        echo "\033[31mFAILED\033[0m";
    }
    echo PHP_EOL;

    if ($result->hasLogs()) {
        foreach ($result->getLogs() as $log) {
            echo 'Log: ' . $log . PHP_EOL;
        }
    }

    if ($result->hasErrors()) {
        foreach ($result->getErrors() as $error) {
            echo 'Error: ' . $error . PHP_EOL;
        }
    }

    if ($result->hasUnhandledException()) {
        echo 'Unhandled exception: ' . $result->getUnhandledException()->getMessage() . PHP_EOL;
    }

    echo PHP_EOL;
}