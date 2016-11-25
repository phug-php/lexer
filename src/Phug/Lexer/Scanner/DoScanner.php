<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\DoToken;

class DoScanner implements ScannerInterface
{

    public function scan(State $state)
    {

        $scanner = new ControlStatementScanner(
            DoToken::class,
            ['do']
        );

        foreach ($state->scan($scanner) as $token) {
            yield $token;
        }
    }
}
