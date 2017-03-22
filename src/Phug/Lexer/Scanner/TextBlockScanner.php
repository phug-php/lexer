<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\Lexer\Token\TextToken;

class TextBlockScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        foreach ($state->scan(TextScanner::class) as $token) {
            yield $token;
        }

        foreach ($state->scan(NewLineScanner::class) as $token) {
            yield $token;
        }

        $level = $state->getLevel() + 1;
        $lines = [];
        while ($reader->hasLength()) {
            $indentationScanner = new IndentationScanner();
            if ($indentationScanner->getIndentLevel($state, $level) < $level) {
                break;
            }

            $lines[] = $reader->readUntilNewLine();
            if ($reader->peekNewLine()) {
                $reader->consume(1);
            }
        }

        if (count($lines)) {
            yield $state->createToken(IndentToken::class);

            $token = $state->createToken(TextToken::class);
            $token->setValue(implode("\n", $lines));

            yield $token;

            foreach ($state->scan(NewLineScanner::class) as $token) {
                yield $token;

                return;
            }

            if ($reader->getLength()) {
                yield $state->createToken(NewLineToken::class);

                while ($level > $state->getLevel()) {
                    yield $state->createToken(OutdentToken::class);

                    $level--;
                }
            }
        }
    }
}
