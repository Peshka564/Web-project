<?php

namespace JsonParser;

class Lexer
{
    private string $input;
    private int $inputLen;

    private int $ind;

    private int $row;
    private int $col;

    private int $tabColms;


    public function __construct(string $input, int $tabColms)
    {
        $this->input = $input;
        $this->inputLen = strlen($input);
        $this->ind = 0;
        $this->row = 1;
        $this->col = 1;
        $this->tabColms = $tabColms;
    }

    private function isEOF(): bool
    {
        return $this->inputLen === $this->ind;
    }

    private function isWhiteSpace(): bool
    {
        if (self::isEOF()) {
            return false;
        }
        $currChar = $this->input[$this->ind];
        return $currChar === ' ' || $currChar === "\t" || $currChar === "\r" || $currChar === "\n";
    }

    private function getWhiteSpaceToken(): Token
    {
        $currChar = $this->input[$this->ind++];
        if ($currChar === "\n") {
            $token = new Token(TokenType::VerticalWhiteSpace, "\n", $this->row, $this->col);
            $this->col = 1;
            $this->row++;
            return $token;
        } else {
            $this->ind--;
        }

        $colmIncr = 0;
        $literal = '';
        while (self::isWhiteSpace()) {
            $currChar = $this->input[$this->ind++];
            if ($currChar === "\n") {
                $this->ind--;
                break;
            } else if ($currChar === ' ') {
                $colmIncr++;
            } else if ($currChar === "\t") {
                $colmIncr += $this->tabColms;
            }
            $literal .= $currChar;
        }


        $token = new Token(TokenType::HorizontalWhiteSpace, $literal, $this->row, $this->col);
        $this->col += $colmIncr;
        return $token;
    }

    private function tryGetNullToken(): ?Token
    {
        if ($this->inputLen - $this->ind < 4) {
            return null;
        }

        if (substr($this->input, $this->ind, 4) === 'null') {
            $token = new Token(TokenType::Null, "null", $this->row, $this->col);
            $this->ind += 4;
            $this->col += 4;
            return $token;
        }

        return null;
    }

    private function tryGetFalseToken(): ?Token
    {
        if ($this->inputLen - $this->ind < 5) {
            return null;
        }

        if (substr($this->input, $this->ind, 5) === 'false') {
            $token = new Token(TokenType::False, "false", $this->row, $this->col);
            $this->ind += 5;
            $this->col += 5;
            return $token;
        }

        return null;
    }

    private function tryGetTrueToken(): ?Token
    {
        if ($this->inputLen - $this->ind < 4) {
            return null;
        }

        if (substr($this->input, $this->ind, 4) === 'true') {
            $token = new Token(TokenType::True, "true", $this->row, $this->col);
            $this->ind += 4;
            $this->col += 4;
            return $token;
        }

        return null;
    }

    private static function isControlChar(string $ch): bool
    {
        return ctype_cntrl($ch);
    }

    private function get4Hex(): ?string
    {
        if ($this->inputLen - $this->ind < 4) {
            return null;
        }

        $literal = substr($this->input, $this->ind, 4);
        for ($i = 0; $i < 4; $i++) {
            $currChar = strtolower($literal[$i]);
            $isHex = '0' <= $currChar && $currChar <= '9' || 'a' <= $currChar && $currChar <= 'f';
            if (!$isHex) {
                return null;
            }
        }
        $this->ind += 4;
        return $literal;
    }

    private function tryGetStringLiteralToken(): ?Token
    {
        $originalInd = $this->ind;

        if ($this->input[$this->ind] !== '"') {
            return null;
        }
        $this->ind++;

        $colmIncr = 1;
        $literal = '"';
        while (!self::isEOF() && $this->input[$this->ind] !== '"') {
            $currChar = $this->input[$this->ind++];
            if (self::isControlChar($currChar)) {
                $this->ind = $originalInd;
                return null;
            }
            else if ($currChar === '\\') {
                if (self::isEOF()) {
                    $this->ind = $originalInd;
                    return null;
                }
                $currChar = $this->input[$this->ind++];
                if (
                    $currChar === '"'
                    || $currChar === '\\'
                    || $currChar === '/'
                    || $currChar === 'b'
                    || $currChar === 'f'
                    || $currChar === 'n'
                    || $currChar === 'r'
                    || $currChar === 't'
                ) {
                    $literal .= "\\$currChar";
                    $colmIncr += 2;
                    continue;
                } else if ($currChar === "u") {
                    $hex = self::get4Hex();
                    if ($hex === null) {
                        $this->ind = $originalInd;
                        return null;
                    }
                    $literal .= '\\u' . $hex;
                    $colmIncr += 6;
                    continue;
                } else {
                    $this->ind = $originalInd;
                    return null;
                }

            } else {
                $literal .= $currChar;
                $colmIncr++;
            }
        }

        if (self::isEOF() || $this->input[$this->ind] !== '"') {
            $this->ind = $originalInd;
            return null;
        }
        $this->ind++;

        $literal .= '"';
        $token = new Token(TokenType::StringLiteral, $literal, $this->row, $this->col);
        $this->col += $colmIncr + 1;
        return $token;
    }
    private static function isDigit(string $ch): bool
    {
        return '0' <= $ch[0] && $ch[0] <= '9';
    }

    private function tryGetWholoNumberPart(int $originalInd): ?string
    {
        $literal = '';

        $currChar = $this->input[$this->ind++];
        if ($currChar === '-') {
            $literal .= '-';
            $currChar = $this->input[$this->ind++];
        }

        if (self::isEOF() || !self::isDigit($currChar)) {
            $this->ind = $originalInd;
            return null;
        }

        $literal .= $currChar;

        if ($currChar !== '0') {
            while (!self::isEOF() && self::isDigit($this->input[$this->ind])) {
                $currChar = $this->input[$this->ind++];
                $literal .= $currChar;
            }
        }

        if (self::isEOF()) {
            return $literal;
        }
        $currChar = $this->input[$this->ind];

        if (self::isDigit($currChar)) {
            $this->ind = $originalInd;
            return null;
        }

        return $literal;
    }

    private function tryGetFractionNumberPart(int $originalInd): ?string
    {
        $currChar = $this->input[$this->ind];
        if ($currChar !== '.') {
            return '';
        }

        $literal = '.';
        $this->ind++;

        if (self::isEOF() || !self::isDigit($this->input[$this->ind])) {
            $this->ind = $originalInd;
            return null;
        }

        while (!self::isEOF() && self::isDigit($this->input[$this->ind])) {
            $currChar = $this->input[$this->ind++];
            $literal .= $currChar;
        }

        return $literal;
    }

    private function tryGetExponentNumberPart(int $originalInd): ?string
    {
        $currChar = $this->input[$this->ind];
        if ($currChar !== 'e' && $currChar !== 'E') {
            return '';
        }

        $literal = $currChar;
        $this->ind++;

        if (self::isEOF()) {
            $this->ind = $originalInd;
            return null;
        }

        if ($this->input[$this->ind] === '-' || $this->input[$this->ind] === '+') {
            $currChar = $this->input[$this->ind++];
            $literal .= $currChar;
        }

        if (self::isEOF() || !self::isDigit($this->input[$this->ind])) {
            $this->ind = $originalInd;
            return null;
        }

        while (!self::isEOF() && self::isDigit($this->input[$this->ind])) {
            $currChar = $this->input[$this->ind++];
            $literal .= $currChar;
        }

        return $literal;
    }

    private function tryGetNumberLiteralToken(): ?Token
    {
        $originalInd = $this->ind;
        $wholePart = self::tryGetWholoNumberPart($originalInd);
        if ($wholePart === null) {
            return null;
        }
        if (self::isEOF()) {
            $token = new Token(TokenType::NumberLiteral, $wholePart, $this->row, $this->col);
            $this->col += $this->ind - $originalInd;
            return $token;
        }
        $fractionPart = self::tryGetFractionNumberPart($originalInd);
        if ($fractionPart === null) {
            return null;
        }
        if (self::isEOF()) {
            $token = new Token(TokenType::NumberLiteral, $wholePart . $fractionPart, $this->row, $this->col);
            $this->col += $this->ind - $originalInd;
            return $token;
        }
        $expPart = self::tryGetExponentNumberPart($originalInd);
        if ($expPart === null) {
            return null;
        }

        $token = new Token(TokenType::NumberLiteral, $wholePart . $fractionPart . $expPart, $this->row, $this->col);
        $this->col += $this->ind - $originalInd;
        return $token;
    }



    public function nextToken(): Token
    {
        if (self::isEOF()) {
            return new Token(TokenType::EOF, "", $this->row, $this->col);
        }
        if (self::isWhiteSpace()) {
            return self::getWhiteSpaceToken();
        }

        $token = null;
        switch ($this->input[$this->ind]) {
            case ':':
                $token = new Token(TokenType::Colon, ':', $this->row, $this->col);
                break;
            case ',':
                $token = new Token(TokenType::Comma, ',', $this->row, $this->col);
                break;
            case '[':
                $token = new Token(TokenType::OpeningSquareBracket, '[', $this->row, $this->col);
                break;
            case ']':
                $token = new Token(TokenType::ClosingSquareBracket, ']', $this->row, $this->col);
                break;
            case '{':
                $token = new Token(TokenType::OpeningCurlyBracket, '{', $this->row, $this->col);
                break;
            case '}':
                $token = new Token(TokenType::ClosingCurlyBracket, '}', $this->row, $this->col);
                break;
        }

        if ($token !== null) {
            $this->ind++;
            $this->col++;
            return $token;
        }

        $token = self::tryGetNullToken();
        if ($token !== null) {
            return $token;
        }
        $token = self::tryGetFalseToken();
        if ($token !== null) {
            return $token;
        }
        $token = self::tryGetTrueToken();
        if ($token !== null) {
            return $token;
        }

        $token = self::tryGetStringLiteralToken();
        if ($token !== null) {
            return $token;
        }
        $token = self::tryGetNumberLiteralToken();
        if ($token !== null) {
            return $token;
        }

        return new Token(TokenType::Invalid, "", $this->row, $this->col);
    }



    public function hasNextToken(): bool
    {
        return !self::isEOF();
    }
}