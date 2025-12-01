<?php
require_once __DIR__ . '/./autoload.php';

use DemoTest\DemoTest;
use Tester\Tester;

$tester = new Tester();

// register tests here
$tester->RegisterTest([DemoTest::class, 'TestWithFail']);
$tester->RegisterTest([DemoTest::class, 'TestWithMultipleErrors']);
$tester->RegisterTest([DemoTest::class, 'TestWithMultipleLogs']);
$tester->RegisterTest([DemoTest::class, 'TestWithMultipleErrorsWithFail']);
$tester->RegisterTest([DemoTest::class, 'TestWithUnhandleException']);
// to here

$tester->RunTests();

$results = $tester->getResults();

foreach ($results as $testName => $result) {
    echo '===' .$testName . '==='. PHP_EOL;

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