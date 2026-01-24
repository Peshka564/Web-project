<?php

namespace Transformer\Evaluator;

class TransformerContext
{

    /**
     * 
     * @var array<string, class-string<TransformerFunction>>
     */
    private array $funcs;

    public function __construct(string $stdDir, string | null $pluginDir = null)
    {
        $this->funcs = [];
        self::loadFuncsFromDir($stdDir);
        if ($pluginDir !== null) {
            self::loadFuncsFromDir($pluginDir);
        }
    }

    private function loadFuncsFromDir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (glob($dir . "/*.php") as $file) {
            require_once $file;

            $class = basename($file, '.php');

            if (!class_exists($class)) {
                throw new FailedClassLoadingException("Expected class $class was not found in $file");
            }

            if (!is_subclass_of($class, TransformerFunction::class)) {
                throw new FailedClassLoadingException("All classes must implement TransformerFunction");
            }

            $funcName = $class::getFunctionName();
            $this->funcs[$funcName] = $class;
        }
    }

    /**
     * @return string[]
     */
    public function getLoadedFunctionNames(): array
    {
        return array_keys($this->funcs);
    }

    public function doesContainFunction(string $funcName){
        return array_key_exists($funcName , $this->funcs);
    }

    public function getFunction(string $funcName){
        return new $this->funcs[$funcName]();
    }
}