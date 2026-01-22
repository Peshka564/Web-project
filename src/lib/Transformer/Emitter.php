<?php

namespace Transformer;

use JsonParser\AST\ArrayNode;
use JsonParser\AST\ASTNode;
use JsonParser\AST\ASTNodeType;
use JsonParser\AST\ObjectNode;

class Emitter
{
    public function emit(string $emitter, ASTNode $node): ASTNode
    {
        if ($emitter === ".") {
            return $node;
        }

        $ind = 1;
        $len = strlen($emitter);

        while ($ind < $len) {
            $currChar = $emitter[$ind];
            if ($currChar === "\"") {
                $key = self::readString($emitter, $ind, $len);
                $ind += strlen($key);
                $node = self::getValueFromNode($key, $node);
            } else if (self::isDigit($currChar)) {
                $literal = self::readNumber($emitter, $ind, $len);
                $ind += strlen($literal);
                $index = intval($literal);
                $node = self::getElementFromNode($index, $node);
            } else if ($currChar === ".") {
                $ind++;
            } else {
                throw new InvalidEmitterSyntaxException("Unexpected character");
            }
        }

        return $node;
    }

    private function readString(string $emitter, int $ind, int $len): string
    {    
        $leftStringInd = $ind;
        $ind++;
        while ($ind < $len) {
            $currChar = $emitter[$ind];
            $nextChar = $ind + 1 < $len ? $emitter[$ind + 1] : "\0";
            if (
                $currChar === "\\" && (
                    $nextChar === '"'
                    || $nextChar === '\\'
                    || $nextChar === '/'
                    || $nextChar === 'b'
                    || $nextChar === 'f'
                    || $nextChar === 'n'
                    || $nextChar === 'r'
                    || $nextChar === 't'
                )
            ) {
                $ind++;
            }
            if ($currChar === "\"") {
                $ind++;
                return substr($emitter, $leftStringInd, $ind - $leftStringInd);
            }
            $ind++;
        }
        throw new InvalidEmitterSyntaxException("Missing closing string quote");
    }

    private function readNumber(string $emitter, int $ind, int $len): string
    {
        $leftNumberInd = $ind;
        while ($ind < $len && self::isDigit($emitter[$ind])) {
            $ind++;
        }
        $literal = substr($emitter, $leftNumberInd, $ind - $leftNumberInd);

        if ($literal[0] === "0" && strlen($literal) !== 1) {
            throw new InvalidEmitterStateException("index doesn`t suppor octal");
        }

        return $literal;
    }

    private function getValueFromNode(string $key, ASTNode $node): ASTNode{
        if ($node->getType() !== ASTNodeType::Object) {
            throw new InvalidEmitterStateException("Expected object node");
        }

        /**
         * @var ObjectNode
         */
        $objectNode = $node;
        $chidren = $objectNode->getChildren();

        foreach ($chidren as $keyValueNode) {
            if ($keyValueNode->getKeyNode()->getToken()->getLiteral() === $key) {
                return $keyValueNode->getValueNode();
            }
        }

        throw new InvalidEmitterStateException("The object does not contain such key");
    }

    private function getElementFromNode(int $index, ASTNode $node): ASTNode{
        if ($node->getType() !== ASTNodeType::Array) {
            throw new InvalidEmitterStateException("Expected array node");
        }

        /**
         * @var ArrayNode
         */
        $objectNode = $node;
        $chidren = $objectNode->getChildren();

        if (count($chidren) <= $index) {
            throw new InvalidEmitterStateException("Index is of bounce (it is greater or equal to the elements count)");
        }

        return $chidren[$index];
    }

    private function isDigit(string $ch): bool
    {
        return "0" <= $ch && $ch <= "9";
    }
}