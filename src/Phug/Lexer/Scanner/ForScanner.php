<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\ForToken;

class ForScanner implements ScannerInterface
{

    public function scan(State $state)
    {

        $scanner = new ControlStatementScanner(
            ForToken::class,
            ['for']
        );

        foreach ($state->scan($scanner) as $token) {
            yield $token;
        }
    }
}
