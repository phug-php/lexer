<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\State;
use Phug\Lexer\Token\CommentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TextToken;

class CommentScanner extends MultilineScanner
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
        $line = $reader->readUntilNewLine();
        $lines = $line === '' ? [] : [$line];

        $newLine = false;
        while ($reader->hasLength()) {
            $newLine = true;
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

            $lines[] = $reader->readUntilNewLine();

            if ($newLine = $reader->peekNewLine()) {
                $reader->consume(1);
            }
        }

        if (end($lines) === '') {
            array_pop($lines);
        }

        /** @var TextToken $token */
        $token = $state->createToken(TextToken::class);
        $token->setValue(implode("\n", $lines));

        yield $token;

        if ($newLine) {
            yield $state->createToken(NewLineToken::class);
        }
    }
}
