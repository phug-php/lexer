<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\ConditionalToken;

class ConditionalScanner implements ScannerInterface
{

    public function scan(State $state)
    {

        $scanner = new ControlStatementScanner(
            ConditionalToken::class,
            ['if', 'unless', 'else[ \t]*if', 'else']
        );

        foreach ($state->scan($scanner) as $token) {
            if ($token instanceof ConditionalToken) {
                $token->setName(preg_replace('/[ \t]/', '', $token->getName()));
            }

            yield $token;
        }
    }
}
