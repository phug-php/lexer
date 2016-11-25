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
        /** @var ExpansionToken $token */
        $token = $state->createToken(ExpansionToken::class);

        $spaces = $reader->readIndentation();
        $token->setHasSpace($spaces !== null);

        yield $token;
    }
}
