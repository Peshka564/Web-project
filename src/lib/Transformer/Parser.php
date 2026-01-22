<?php

namespace Transformer;

use JsonParser\AST\ASTNode;

class Parser
{
    private string $expr;
    private int $exprLeftInd;
    private int $exprRightInd;

    private string|null $emitter;
    private string|null $functionName;
    /**
     * @var string[]
     */
    private array $arguments;

    public function __construct(string $expr)
    {
        $this->expr = $expr;
        $this->exprLeftInd = 0;
        $this->exprRightInd = strlen($expr) - 1;
        $this->emitter = null;
        $this->functionName = null;
        $this->arguments = [];
    }

    public function parse()
    {
        self::readOpeningBracket();
        self::readToClosingBracket();
        self::parseEmitter();
        self::parseFunctionName();
        self::parseArguments();
    }

    public function getEmitter(): string
    {
        return $this->emitter;
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    } 
    
    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    private function readOpeningBracket()
    {
        while ($this->exprLeftInd <= $this->exprRightInd) {
            $currChar = $this->expr[$this->exprLeftInd];
            if (self::isWhitespace($currChar)) {
                $this->exprLeftInd++;
            } else if ($currChar === "(") {
                $this->exprLeftInd++;
                return;
            } else {
                // TODO return result not exception
                throw new ParsingException();
            }
        }
        // TODO return result not exception
        throw new ParsingException();
    }

    private function readToClosingBracket()
    {
        while ($this->exprLeftInd <= $this->exprRightInd) {
            $currChar = $this->expr[$this->exprRightInd];
            if (self::isWhitespace($currChar)) {
                $this->exprRightInd--;
            } else if ($currChar === ")") {
                $this->exprRightInd--;
                return;
            } else {
                // TODO return result not exception
                throw new ParsingException();
            }
        }
        // TODO return result not exception
        throw new ParsingException();
    }

    private function isWhitespace(string $ch): bool
    {
        return $ch === " " || $ch === "\t" || $ch === "\r" || $ch === "\n";
    }

    private function isDigit(string $ch): bool
    {
        return "0" <= $ch && $ch <= "9";
    }

    private function parseEmitter()
    {
        while ($this->exprLeftInd <= $this->exprRightInd) {
            $currChar = $this->expr[$this->exprLeftInd];
            if (self::isWhitespace($currChar)) {
                $this->exprLeftInd++;
            } else if ($currChar === ".") {
                break;
            } else {
                // TODO return result not exception
                throw new ParsingException("emitter must start with .");
            }
        }
        if ($this->expr[$this->exprLeftInd] !== ".") {
            // TODO return result not exception
            throw new ParsingException("emitter must start with .");
        }

        $emitterLeftInd = $this->exprLeftInd;
        $inString = false;
        $inNumber = false;
        $dot = false;
        $nummberOfDots = 0;
        while ($this->exprLeftInd <= $this->exprRightInd) {
            $currChar = $this->expr[$this->exprLeftInd];
            $nextChar = $this->exprLeftInd < $this->exprRightInd ? $this->expr[$this->exprLeftInd + 1] : "\0";
            if ($currChar === "\"") {
                if (!$inString && !$dot) {
                    // TODO return result not exception
                    throw new ParsingException("before every field must have .");
                } else if (!$inString && $inNumber) {
                    // TODO return result not exception
                    throw new ParsingException("cannot start field directly after index");
                } else if ($inString) {
                    $dot = false;
                }
                $inString = !$inString;
            } else if ($inString) {
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
                    $this->exprLeftInd++;
                } else if ($currChar === "\\") {
                    // TODO return result not exception
                    throw new ParsingException("invalid escape");
                }
            } else if (self::isDigit($currChar)) {
                if (!$dot) {
                    // TODO return result not exception
                    throw new ParsingException("before every index must have .");
                } else if (!$inNumber && $currChar === "0" && self::isDigit($nextChar)) {
                    // TODO return result not exception
                    throw new ParsingException("index does not support octal");
                } else if (!$inNumber) {
                    $inNumber = true;
                }
            } else if ($currChar === ".") {
                if ($dot && !$inNumber) {
                    // TODO return result not exception
                    throw new ParsingException("cannot have . one after the other");
                }
                $inNumber = false;
                $dot = true;
                $nummberOfDots++;
            } else if (self::isWhitespace($currChar)) {
                $inNumber = false;
                break;
            } else {
                // TODO return result not exception
                throw new ParsingException("invalid character");
            }
            $this->exprLeftInd++;
        }
        if ($this->exprLeftInd > $this->exprRightInd) {
            if ($inNumber) {
                $dot = false;
            }
            $inNumber = false;
        }

        if ($dot && $nummberOfDots !== 1 || $inString) {
            // TODO return result not exception
            throw new ParsingException("cannot finist on dot or unclosed string");
        }

        $this->emitter = substr($this->expr, $emitterLeftInd, $this->exprLeftInd - $emitterLeftInd);
    }

    private function parseFunctionName()
    {
        while ($this->exprLeftInd <= $this->exprRightInd) {
            $currChar = $this->expr[$this->exprLeftInd];
            if (self::isWhitespace($currChar)) {
                $this->exprLeftInd++;
            } else {
                break;
            }
        }
        if ($this->exprLeftInd > $this->exprRightInd) {
            // TODO return result not exception
            throw new ParsingException("missing function name");
        }


        $functionNameLeftInd = $this->exprLeftInd;
        while ($this->exprLeftInd <= $this->exprRightInd) {
            $currChar = $this->expr[$this->exprLeftInd];
            if (!self::isWhitespace($currChar)) {
                $this->exprLeftInd++;
            } else {
                break;
            }
        }


        $this->functionName = substr($this->expr, $functionNameLeftInd, $this->exprLeftInd - $functionNameLeftInd);
    }

    private function parseArguments()
    {
        $arguments = [];
        while ($this->exprLeftInd <= $this->exprRightInd) {
            $currChar = $this->expr[$this->exprLeftInd];
            if (self::isWhitespace($currChar)) {
                $this->exprLeftInd++;
            } else if ($currChar === "(") {
                $arguments[] = self::parseArgument();
            } else {
                // TODO return result not exception
                throw new ParsingException("Invalid argument");
            }
        }
        $this->arguments = $arguments;
    }

    private function parseArgument(): string
    {
        // requires a $this->$expr[$this->$exprLeftInd] === "("
        if ($this->expr[$this->exprLeftInd] !== "(") {
            // TODO return result not exception
            throw new ParsingException("Argument must start with (");
        }
        $argumentLeftInd = $this->exprLeftInd;
        $this->exprLeftInd++;

        $brackets = 1;
        $inString = false;
        while ($this->exprLeftInd <= $this->exprRightInd) {
            $currChar = $this->expr[$this->exprLeftInd];

            if ($currChar === "\\") {
                $this->exprLeftInd++;
            } else if ($currChar === "\"") {
                $inString = !$inString;
            } else if ($currChar === "(" && !$inString) {
                $brackets++;
            } else if ($currChar === ")" && !$inString) {
                $brackets--;
                if ($brackets === 0) {
                    break;
                }
            }
            $this->exprLeftInd++;
        }

        if ($brackets !== 0) {
            // TODO return result not exception
            throw new ParsingException("Missing closing brackets");
        }
        if ($this->expr[$this->exprLeftInd] === ")") {
            $this->exprLeftInd++;
        }

        return substr($this->expr, $argumentLeftInd, $this->exprLeftInd - $argumentLeftInd);
    }
}