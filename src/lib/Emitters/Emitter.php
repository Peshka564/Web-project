<?php

namespace Emitters;

use JsonParser\AST\ASTNode;

interface Emitter
{
    public function emit(ASTNode $root): string;
}