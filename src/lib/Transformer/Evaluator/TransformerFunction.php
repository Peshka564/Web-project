<?php

namespace Transformer\Evaluator;

use JsonParser\AST\ASTNode;

interface TransformerFunction
{
    public static function getFunctionName(): string;
    public function eval(array $args, ASTNode $input, TransformerContext $ctx): ASTNode;
}