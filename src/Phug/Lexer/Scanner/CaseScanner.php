<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\CaseToken;

class CaseScanner implements ScannerInterface
{

    public function scan(State $state)
    {

        $scanner = new ControlStatementScanner(
            CaseToken::class,
            ['case']
        );

        foreach ($state->scan($scanner) as $token) {
            yield $token;
        }
    }
}
