<?php
require_once "autoload.php";

use Configuration\ConfigurationService;

ConfigurationService::load(__DIR__ . "/../../app.json");