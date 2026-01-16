<?php

namespace Tester;

class TestContext
{
    /**
     * @var string[]
     */
    private array $logs = [];

    /**
     * @var string[]
     */
    private array $errors = [];

    public function log(string $log): void
    {
        $this->logs[] = $log;
    }

    public function error(string $error): void
    {
        $this->errors[] = $error;
    }

    public function fail(string $error): void
    {
        $this->errors[] = $error;
        throw new TestFailureException();
    }

    /**
     * @return string[]
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}