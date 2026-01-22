<?php

namespace Transformer;

use Exception;
use Throwable;

class ParsingException extends Exception {
    public function __construct(string $msg = "", $code = 0, Throwable | null $cause = null) {
        parent::__construct($msg, $code, $cause);
    }
}