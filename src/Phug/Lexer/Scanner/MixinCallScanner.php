<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\MixinCallToken;

class MixinCallScanner implements ScannerInterface
{
    public function scan(State $state)
    {

        foreach ($state->scanToken(
            MixinCallToken::class,
            '\+(?<name>[a-zA-Z_][a-zA-Z0-9\-_]*)'
        ) as $token) {
            yield $token;

            foreach ($state->scan(ClassScanner::class) as $subToken) {
                yield $subToken;
            }

            foreach ($state->scan(SubScanner::class) as $subToken) {
                yield $subToken;
            }
        }
    }
}
