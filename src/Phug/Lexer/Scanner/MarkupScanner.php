<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\State;
use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\InterpolationStartToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TagInterpolationEndToken;
use Phug\Lexer\Token\TagInterpolationStartToken;
use Phug\Lexer\Token\TextToken;

class MarkupScanner extends TextBlockScanner
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        if (!$reader->peekChar('<')) {
            return;
        }

        $level = $state->getLevel();
        $lines = [];

        $newLine = false;
        while ($reader->hasLength()) {
            $newLine = true;
            $indentationScanner = new IndentationScanner();
            $newLevel = $indentationScanner->getIndentLevel($state, $level);

            if (!$reader->peekChars(['<', ' ', "\t", "\n"])) {
                break;
            }

            if ($newLevel < $level) {
                if ($reader->match('[ \t]*\n')) {
                    $reader->consume(mb_strlen($reader->getMatch(0)));
                    $lines[] = [];

                    continue;
                }

                $state->setLevel($newLevel);

                break;
            }

            $line = [];

            foreach ($state->scan(InterpolationScanner::class) as $subToken) {
                $line[] = $subToken instanceof TextToken ? $subToken->getValue() : $subToken;
            }

            $line[] = $reader->readUntilNewLine();
            $lines[] = $line;

            if ($newLine = $reader->peekNewLine()) {
                $reader->consume(1);
            }
        }

        $buffer = '';
        $interpolationLevel = 0;
        foreach ($lines as $number => $lineValues) {
            if ($number) {
                $buffer .= "\n";
            }
            foreach ($lineValues as $value) {
                if (is_string($value)) {
                    if ($interpolationLevel) {
                        yield $this->unEscapedToken($state, $value);

                        continue;
                    }
                    $buffer .= $value;

                    continue;
                }

                if (!$interpolationLevel) {
                    yield $this->unEscapedToken($state, $buffer);

                    $buffer = '';
                }

                yield $value;

                if ($value instanceof TagInterpolationStartToken || $value instanceof InterpolationStartToken) {
                    $interpolationLevel++;
                }

                if ($value instanceof TagInterpolationEndToken || $value instanceof InterpolationEndToken) {
                    $interpolationLevel--;
                }
            }
        }

        yield $this->unEscapedToken($state, $buffer);

        if ($newLine) {
            yield $state->createToken(NewLineToken::class);
        }
    }
}
