<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\WhenToken;

class WhenScanner implements ScannerInterface
{

    public function scan(State $state)
    {

        $scanner = new ControlStatementScanner(
            WhenToken::class,
            ['when', 'default']
        );

        foreach ($state->scan($scanner) as $token) {
            yield $token;
        }
    }
}
