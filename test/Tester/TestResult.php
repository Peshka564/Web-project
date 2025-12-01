<?php

namespace Tester;

use Throwable;

class TestResult
{
    /**
     * @var string[]
     */
    private array $logs = [];

    /**
     * @var string[]
     */
    private array $errors = [];

    private ?Throwable $exception;

    /**
     * @param string[] $logs
     * @param string[] $errors
     * @param ?Throwable $exception
     */
    public function __construct(array $logs, array $errors, ?Throwable $exception)
    {
        $this->logs = $logs;
        $this->errors = $errors;
        $this->exception = $exception;
    }

    public function ok(): bool
    {
        return empty($this->errors) && $this->exception === null;
    }

    public function hasLogs(): bool
    {
        return !empty($this->logs);
    }

    /**
     * @return string[]
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasUnhandledException(): bool {
        return $this->exception !== null;
    }

    public function getUnhandledException(): ?Throwable {
        return $this->exception;
    }
}