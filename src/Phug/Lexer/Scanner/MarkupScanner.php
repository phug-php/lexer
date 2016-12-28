<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;

class MarkupScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        if (!$reader->peekChar('<')) {
            return;
        }

        foreach ($state->scan(TextScanner::class) as $token) {
            yield $token;
        }
    }
}
