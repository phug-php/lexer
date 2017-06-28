<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\State;
use Phug\Lexer\Token\CommentToken;

class CommentScanner extends TextBlockScanner
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        if (!$reader->peekString('//')) {
            return;
        }

        $reader->consume();

        /** @var CommentToken $token */
        $token = $state->createToken(CommentToken::class);

        if ($reader->peekChar('-')) {
            $reader->consume();
            $token->hide();
        }

        yield $token;
        $level = $state->getLevel();
        $lines = [$reader->readUntilNewLine()];

        while ($reader->hasLength()) {
            $indentationScanner = new IndentationScanner();
            $newLevel = $indentationScanner->getIndentLevel($state, $level);

            if (!$reader->peekChars([' ', "\t", "\n"])) {
                break;
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
            $line = $reader->readUntilNewLine();

            if ($reader->peekNewLine()) {
                $line .= "\n";
                $reader->consume(1);
            }

            $lines[] = $line;
        }

        foreach ($this->createBlockTokens($state, $lines) as $token) {
            yield $token;
        }
    }
}
