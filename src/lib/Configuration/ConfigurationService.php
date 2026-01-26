<?php

namespace Configuration;

class ConfigurationService
{
    public static function load(string $appJsonPath)
    {
        if (!file_exists($appJsonPath) || !is_readable($appJsonPath)) {
            throw new ConfigurationException("The app.json does not exists or is missing read permissions");
        }

        $file = file_get_contents($appJsonPath);
        if (!$file) {
            throw new ConfigurationException("Couldn`t read app.json");
        }

        $conf = json_decode($file, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ConfigurationException("The app.json is not valid json file");
        }

        self::loadJsonParserConf($conf);
        self::loadEmittersConf($conf);
        self::loadTransformerConf($conf);
        self::loadDbConf($conf);
    }

    private static function loadJsonParserConf(array $conf)
    {
        if (!is_array($conf) || !array_key_exists("json-parser", $conf)) {
            throw new ConfigurationException("Missing configuration: .\"json-parser\"");
        }
        $jsonParserConf = $conf["json-parser"];

        self::loadJsonParserLexerConf($jsonParserConf);
    }

    private static function loadJsonParserLexerConf(array $conf)
    {
        if (!is_array($conf) || !array_key_exists("lexer", $conf)) {
            throw new ConfigurationException("Missing configuration: .\"json-parser\".\"lexer\"");
        }
        $lexerConf = $conf["lexer"];
        if (!is_array($lexerConf) || !array_key_exists("tabColms", $lexerConf)) {
            throw new ConfigurationException("Missing configuration: .\"json-parser\".\"lexer\".\"tabColms\"");
        }
        $tabColms = $lexerConf["tabColms"];
        if (!is_int($tabColms)) {
            throw new ConfigurationException(".\"json-parser\".\"lexer\".\"tabColms\" must be int");
        }
        define("JSON_PARSER_LEXER_TAB_COLMS", $tabColms);
    }

    private static function loadEmittersConf(array $conf)
    {
        if (!is_array($conf) || !array_key_exists("emitters", $conf)) {
            throw new ConfigurationException("Missing configuration: .\"emitters\"");
        }
        $emittersConf = $conf["emitters"];
        self::loadEmittersJsonEmitterConf($emittersConf);
        self::loadEmittersYamlEmitterConf($emittersConf);
        self::loadEmittersTOMLEmitterConf($emittersConf);
    }

    private static function loadEmittersJsonEmitterConf(array $conf)
    {
        if (!is_array($conf) || !array_key_exists("json-emitter", $conf)) {
            throw new ConfigurationException("Missing configuration: .\"emitters\".\"json-emitter\"");
        }
        $jsonEmitterConf = $conf["json-emitter"];

        if (!is_array($jsonEmitterConf) || !array_key_exists("indentationString", $jsonEmitterConf)) {
            throw new ConfigurationException("Missing configuration: .\"emitters\".\"json-emitter\".\"indentationString\"");
        }
        $indentationString = $jsonEmitterConf["indentationString"];
        if (!is_string($indentationString)) {
            throw new ConfigurationException(".\"emitters\".\"json-emitter\".\"indentationString\" must be string");
        }

        if (!is_array($jsonEmitterConf) || !array_key_exists("newLineString", $jsonEmitterConf)) {
            throw new ConfigurationException("Missing configuration: .\"emitters\".\"json-emitter\".\"newLineString\"");
        }
        $newLineString = $jsonEmitterConf["newLineString"];
        if (!is_string($newLineString)) {
            throw new ConfigurationException(".\"emitters\".\"json-emitter\".\"newLineString\" must be string");
        }

        define("EMITTERS_JSON_EMITTERS_INDENTATION_STRING", $indentationString);
        define("EMITTERS_JSON_EMITTERS_NEWLINE_STRING", $newLineString);
    }

    private static function loadEmittersYamlEmitterConf(array $conf)
    {
        if (!is_array($conf) || !array_key_exists("yaml-emitter", $conf)) {
            throw new ConfigurationException("Missing configuration: .\"emitters\".\"yaml-emitter\"");
        }
        $yamlEmitterConf = $conf["yaml-emitter"];

        if (!is_array($yamlEmitterConf) || !array_key_exists("indentationString", $yamlEmitterConf)) {
            throw new ConfigurationException("Missing configuration: .\"emitters\".\"yaml-emitter\".\"indentationString\"");
        }
        $indentationString = $yamlEmitterConf["indentationString"];
        if (!is_string($indentationString)) {
            throw new ConfigurationException(".\"emitters\".\"yaml-emitter\".\"indentationString\" must be string");
        }

        if (!is_array($yamlEmitterConf) || !array_key_exists("newLineString", $yamlEmitterConf)) {
            throw new ConfigurationException("Missing configuration: .\"emitters\".\"yaml-emitter\".\"newLineString\"");
        }
        $newLineString = $yamlEmitterConf["newLineString"];
        if (!is_string($newLineString)) {
            throw new ConfigurationException(".\"emitters\".\"yaml-emitter\".\"newLineString\" must be string");
        }

        define("EMITTERS_YAML_EMITTERS_INDENTATION_STRING", $indentationString);
        define("EMITTERS_YAML_EMITTERS_NEWLINE_STRING", $newLineString);
    }

    private static function loadEmittersTOMLEmitterConf(array $conf)
    {
        if (!is_array($conf) || !array_key_exists("toml-emitter", $conf)) {
            throw new ConfigurationException("Missing configuration: .\"emitters\".\"toml-emitter\"");
        }
        $tomlEmitterConf = $conf["toml-emitter"];

        if (!is_array($tomlEmitterConf) || !array_key_exists("indentationString", $tomlEmitterConf)) {
            throw new ConfigurationException("Missing configuration: .\"emitters\".\"toml-emitter\".\"indentationString\"");
        }
        $indentationString = $tomlEmitterConf["indentationString"];
        if (!is_string($indentationString)) {
            throw new ConfigurationException(".\"emitters\".\"toml-emitter\".\"indentationString\" must be string");
        }

        if (!is_array($tomlEmitterConf) || !array_key_exists("newLineString", $tomlEmitterConf)) {
            throw new ConfigurationException("Missing configuration: .\"emitters\".\"toml-emitter\".\"newLineString\"");
        }
        $newLineString = $tomlEmitterConf["newLineString"];
        if (!is_string($newLineString)) {
            throw new ConfigurationException(".\"emitters\".\"tml-emitter\".\"newLineString\" must be string");
        }

        define("EMITTERS_TOML_EMITTERS_INDENTATION_STRING", $indentationString);
        define("EMITTERS_TOML_EMITTERS_NEWLINE_STRING", $newLineString);
    }

    public static function loadTransformerConf(array $conf)
    {
        if (!is_array($conf) || !array_key_exists("transformer", $conf)) {
            throw new ConfigurationException("Missing configuration: .\"transformer\"");
        }
        $transformerConf = $conf["transformer"];

        if (!is_array($transformerConf) || !array_key_exists("stdlib", $transformerConf)) {
            throw new ConfigurationException("Missing configuration: .\"transformer\".\"stdlib\"");
        }
        $stdlib = $transformerConf["stdlib"];
        if (!is_string($stdlib)) {
            throw new ConfigurationException(".\"transformer\".\"stdlib\" must be string");
        }

        if (!is_array($transformerConf) || !array_key_exists("plugins", $transformerConf)) {
            throw new ConfigurationException("Missing configuration: .\"transformer\".\"plugins\"");
        }
        $plugins = $transformerConf["plugins"];
        if (!is_string($plugins)) {
            throw new ConfigurationException(".\"transformer\".\"plugins\" must be string");
        }

        define("TRANSFORMER_STDLIB_PATH", $stdlib);
        define("TRANSFORMER_PLUGINS_PATH", $plugins);
    }

    private static function loadDbConf(array $conf)
    {
        if (!is_array($conf) || !array_key_exists("db", $conf)) {
            throw new ConfigurationException("Missing configuration: .\"db\"");
        }
        $dbConf = $conf["db"];
        self::loadDbClientConf($dbConf);
    }

    private static function loadDbClientConf(array $conf)
    {
        if (!is_array($conf) || !array_key_exists("client", $conf)) {
            throw new ConfigurationException("Missing configuration: .\"db\".\"client\"");
        }
        $dbClientConf = $conf["client"];

        if (!is_array($dbClientConf) || !array_key_exists("host", $dbClientConf)) {
            throw new ConfigurationException("Missing configuration: .\"db\".\"client\".\"host\"");
        }
        $host = $dbClientConf["host"];
        if (!is_string($host)) {
            throw new ConfigurationException(".\"db\".\"client\".\"host\" must be string");
        }

        if (!is_array($dbClientConf) || !array_key_exists("port", $dbClientConf)) {
            throw new ConfigurationException("Missing configuration: .\"db\".\"client\".\"port\"");
        }
        $port = $dbClientConf["port"];
        if (!is_int($port)) {
            throw new ConfigurationException(".\"db\".\"client\".\"port\" must be int");
        }

        if (!is_array($dbClientConf) || !array_key_exists("dbName", $dbClientConf)) {
            throw new ConfigurationException("Missing configuration: .\"db\".\"client\".\"dbName\"");
        }
        $dbName = $dbClientConf["dbName"];
        if (!is_string($dbName)) {
            throw new ConfigurationException(".\"db\".\"client\".\"dbName\" must be string");
        }

        if (!is_array($dbClientConf) || !array_key_exists("user", $dbClientConf)) {
            throw new ConfigurationException("Missing configuration: .\"db\".\"client\".\"user\"");
        }
        $user = $dbClientConf["user"];
        if (!is_string($user)) {
            throw new ConfigurationException(".\"db\".\"client\".\"user\" must be string");
        }

        if (!is_array($dbClientConf) || !array_key_exists("password", $dbClientConf)) {
            throw new ConfigurationException("Missing configuration: .\"db\".\"client\".\"password\"");
        }
        $pass = $dbClientConf["password"];
        if (!is_string($pass)) {
            throw new ConfigurationException(".\"db\".\"client\".\"pass\" must be string");
        }

        define("DB_CLIENT_HOST", $host);
        define("DB_CLIENT_PORT", $port);  
        define("DB_CLIENT_DBNAME", $dbName);
        define("DB_CLIENT_USER", $user);
        define("DB_CLIENT_PASS", $pass);
    }
}