<?php

namespace Tester;

use Throwable;

class Tester
{

    /**
     * @var string[]
     */
    private array $testNames = [];

    /**
     * @var array<string, callable(TestContext):void>
     */
    private array $tests = [];

    /**
     * @var array<string, string[]>
     */
    private array $logs = [];

    /**
     * @var array<string, string[]>
     */
    private array $errors = [];

    /**
     * @var array<string, ?Throwable>
     */
    private array $exceptions = [];

    /**
     * @param callable(TestContext):void $testFunc
     */
    public function RegisterTest(string $testName, callable $testFunc): void
    {
        $this->testNames[] = $testName;
        $this->tests[$testName] = $testFunc;
    }

    public function RunTests(): void
    {
        foreach ($this->tests as $testName => $testFunc) {
            $ctx = new TestContext();
            try {
                $testFunc($ctx);
            } catch (TestFailureException $e) {

            } catch (Throwable $e) {
                $this->exceptions[$testName] = $e;
            } finally {
                $this->logs[$testName] = $ctx->getLogs();
                $this->errors[$testName] = $ctx->getErrors();
                if (!array_key_exists($testName, $this->exceptions)) {
                    $this->exceptions[$testName] = null;
                }
            }
        }
    }

    /**
     * @return array<string, TestResult>
     */
    public function getResults(): array
    {
        $results = [];
        foreach ($this->testNames as $testName) {
            $result = new TestResult($this->logs[$testName], $this->errors[$testName], $this->exceptions[$testName]);
            $results[$testName] = $result;
        }
        return $results;
    }
}
