<?php

namespace Transformer\Evaluator;

use Exception;
use Throwable;

class EvaluationException extends Exception {
    public function __construct(string $msg = "", $code = 0, Throwable | null $cause = null) {
        parent::__construct($msg, $code, $cause);
    }
}