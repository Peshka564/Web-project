<?php

namespace Emitters\TomlEmitter;

class TomlEmitterConfig
{
    public function __construct(public string $indentationString = "\t", public string $newLineString = "\n")
    {}
}