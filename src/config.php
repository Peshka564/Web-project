<?php
require_once __DIR__ . "/autoload.php";

use Configuration\ConfigurationService;

ConfigurationService::load(
    __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "app.json"
);