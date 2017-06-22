<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\State;

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

        while ($reader->hasLength()) {
            $indentationScanner = new IndentationScanner();
            $newLevel = $indentationScanner->getIndentLevel($state, $level);

            if (!$reader->peekChars(['<', ' ', "\t"])) {
                return;
            }

            if ($newLevel < $level) {
                if ($reader->match('[ \t]*\n')) {
                    $reader->consume(mb_strlen($reader->getMatch(0)));
                    $lines[] = '';

                    continue;
                }

                $state->setLevel($newLevel);

                break;
            }

            $this->interpolateLines($state, $lines);
            $lines[] = $reader->readUntilNewLine();

            if ($reader->peekNewLine()) {
                $reader->consume(1);
            }
        }

        foreach ($this->createBlockTokens($state, $lines) as $token) {
            yield $token;
        }
    }
}
