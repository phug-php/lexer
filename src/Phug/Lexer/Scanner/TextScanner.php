<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\TextToken;

class TextScanner implements ScannerInterface
{
    public function scan(State $state)
    {

        $reader = $state->getReader();

        /** @var TextToken $token */
        $token = $state->createToken(TextToken::class);
        $text = $reader->readUntilNewLine();
        
        if (mb_strlen($text) < 1) {
            return;
        }

        //Always omit the very first space in basically every text (if there is one)
        if ($text[0] === ' ') {

            $text = substr($text, 1);
        }

        $token->setValue($text);
        yield $token;
    }
}
