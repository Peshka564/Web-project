<?php

namespace Emitters\YamlEmitter;

class YamlEmitterConfig
{
    public function __construct(public string $indentationString = "\t", public string $newLineString = "\n")
    {}
}