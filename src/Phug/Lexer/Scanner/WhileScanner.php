<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\WhileToken;

class WhileScanner implements ScannerInterface
{

    public function scan(State $state)
    {

        $scanner = new ControlStatementScanner(
            WhileToken::class,
            ['while']
        );

        foreach ($state->scan($scanner) as $token) {
            yield $token;
        }
    }
}
