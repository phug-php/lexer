<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\ImportToken;

class ImportScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        return $state->scanToken(
            ImportToken::class,
            '(?<name>extends|include)(?::(?<filter>[a-zA-Z_][a-zA-Z0-9\-_]*))?[\t ]+(?<path>[a-zA-Z0-9\-_\\/\. ]+)'
        );
    }
}
