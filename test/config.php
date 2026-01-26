<?php
require_once "autoload.php";

use Configuration\ConfigurationService;

ConfigurationService::load(__DIR__ . "/../config/app.json");

// echo JSON_PARSER_LEXER_TAB_COLMS . "\n";
// echo EMITTERS_JSON_EMITTERS_INDENTATION_STRING . "\n";
// echo EMITTERS_JSON_EMITTERS_NEWLINE_STRING . "\n";
// echo EMITTERS_YAML_EMITTERS_INDENTATION_STRING . "\n";
// echo EMITTERS_YAML_EMITTERS_NEWLINE_STRING . "\n";
// echo EMITTERS_TOML_EMITTERS_INDENTATION_STRING . "\n";
// echo EMITTERS_TOML_EMITTERS_NEWLINE_STRING . "\n";
// echo TRANSFORMER_STDLIB_PATH . "\n";
// echo TRANSFORMER_PLUGINS_PATH . "\n";
// echo DB_CLIENT_HOST . "\n";
// echo DB_CLIENT_PORT . "\n";
// echo DB_CLIENT_DBNAME . "\n";
// echo DB_CLIENT_USER . "\n";
// echo DB_CLIENT_PASS . "\n";
