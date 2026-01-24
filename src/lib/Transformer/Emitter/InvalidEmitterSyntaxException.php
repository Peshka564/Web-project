<?php

namespace Transformer\Emitter;

use Exception;
use Throwable;

class InvalidEmitterSyntaxException extends Exception{
    public function __construct(string $msg = "", int $code = 0, Throwable | null $cause = null) {
        parent::__construct($msg, $code, $cause);
    }
}