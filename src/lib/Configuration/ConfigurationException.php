<?php

namespace Configuration;

use Exception;
use Throwable;

class ConfigurationException extends Exception{
    public function __construct(string $msg = "", int $code = 0, Throwable | null $cause = null) {
        parent::__construct($msg, $code, $cause);
    }
}