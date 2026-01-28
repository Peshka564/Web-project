<?php
require_once __DIR__ . "/autoload.php";

use Configuration\ConfigurationService;

ConfigurationService::load(
    __DIR__ . "/../config/app.json"
);