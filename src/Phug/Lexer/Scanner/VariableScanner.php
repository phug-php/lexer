<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\CaseToken;
use Phug\Lexer\Token\VariableToken;

class VariableScanner implements ScannerInterface
{

    public function scan(State $state)
    {

        return $state->scanToken(
            VariableToken::class,
            '\$(?<name>[a-zA-Z_][a-zA-Z0-9_]*)'
        );
    }
}
