<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\ExpansionToken;

class ExpansionScanner implements ScannerInterface
{
    
    public function scan(State $state)
    {

        $reader = $state->getReader();

        if (!$reader->peekChar(':')) {
            return;
        }

        $reader->consume();

        //Allow any kind of spacing after an expansion
        $reader->readIndentation();

        yield $state->createToken(ExpansionToken::class);
    }
}
