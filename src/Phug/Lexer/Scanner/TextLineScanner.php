<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\TextToken;

class TextLineScanner implements ScannerInterface
{
    public function scan(State $state)
    {

        $reader = $state->getReader();

        if (!$reader->match('([!]?)\|')) {
            return;
        }

        $escaped = $reader->getMatch(1) === '!';

        $reader->consume();

        foreach ($state->scan(TextBlockScanner::class) as $token) {
            if ($escaped && $token instanceof TextToken) {
                $token->escape();
            }

            yield $token;
        }
    }
}
