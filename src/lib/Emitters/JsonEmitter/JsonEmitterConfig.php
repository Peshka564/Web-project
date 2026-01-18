<?php

namespace Emitters\JsonEmitter;

class JsonEmitterConfig
{
    public function __construct(public string $indentationString = "\t", public string $newLineString = "\n")
    {}
}