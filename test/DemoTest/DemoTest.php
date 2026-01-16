<?php

namespace DemoTest;

use Tester\TestContext;

class DemoTest
{
    public static function TestWithFail(TestContext $context)
    {
        $context->fail('failed test');
    }

    public static function TestWithMultipleErrors(TestContext $context)
    {
        $context->error('error 1');
        $context->error('error 2');
        $context->error('error 3');
        $context->error('error 4');
    }


    public static function TestWithMultipleLogs(TestContext $context)
    {
        $context->log('log 1');
        $context->log('log 2');
        $context->log('log 3');
        $context->log('log 4');
    }

    public static function TestWithMultipleErrorsWithFail(TestContext $context)
    {
        $context->error('error 1');
        $context->error('error 2');
        $context->fail('failed test');
        $context->error('error 3');
        $context->error('error 4');
    }

    public static function TestWithUnhandleException(TestContext $context) {
        throw new \Exception('Some Unhandled exception');
    }
}